<?php
// page.php - Displays individual documentation pages
require_once 'includes/config.php';
require_once 'includes/database.php';

if (!isset($_GET['slug'])) {
    header('Location: index.php');
    exit;
}

$slug = $_GET['slug'];
$db = new Database();
$conn = $db->getConnection();

// Get page content
$stmt = $conn->prepare("
    SELECT p.*, c.id as chapter_id, c.title_en as chapter_title_en, c.title_fr as chapter_title_fr,
           s.id as section_id, s.title_en as section_title_en, s.title_fr as section_title_fr,
           s.icon as section_icon
    FROM pages p
    JOIN chapters c ON p.chapter_id = c.id
    JOIN sections s ON c.section_id = s.id
    WHERE p.slug = ? AND p.is_active = TRUE AND c.is_active = TRUE AND s.is_active = TRUE
");
$stmt->execute([$slug]);
$page = $stmt->fetch();

if (!$page) {
    header('Location: index.php');
    exit;
}

// Get previous and next pages
$stmt = $conn->prepare("
    SELECT p.slug, p.title_en, p.title_fr
    FROM pages p
    JOIN chapters c ON p.chapter_id = c.id
    WHERE c.section_id = ? AND p.sort_order < ? AND p.is_active = TRUE
    ORDER BY p.sort_order DESC
    LIMIT 1
");
$stmt->execute([$page['section_id'], $page['sort_order']]);
$previous_page = $stmt->fetch();

$stmt = $conn->prepare("
    SELECT p.slug, p.title_en, p.title_fr
    FROM pages p
    JOIN chapters c ON p.chapter_id = c.id
    WHERE c.section_id = ? AND p.sort_order > ? AND p.is_active = TRUE
    ORDER BY p.sort_order ASC
    LIMIT 1
");
$stmt->execute([$page['section_id'], $page['sort_order']]);
$next_page = $stmt->fetch();

$page_title = $current_language === 'fr' ? $page['title_fr'] : $page['title_en'];
$current_page = 'page';
?>

<?php include 'includes/header.php'; ?>

<div class="docs-container">
    <!-- Sidebar -->
    <aside class="docs-sidebar">
        <div class="sidebar-section">
            <h5><?php echo $current_language === 'fr' ? $page['section_title_fr'] : $page['section_title_en']; ?></h5>
            
            <!-- Get all chapters for this section -->
            <?php
            $stmt = $conn->prepare("
                SELECT c.*, 
                       (SELECT COUNT(*) FROM pages p WHERE p.chapter_id = c.id AND p.is_active = TRUE) as page_count
                FROM chapters c
                WHERE c.section_id = ? AND c.is_active = TRUE
                ORDER BY c.sort_order
            ");
            $stmt->execute([$page['section_id']]);
            $chapters = $stmt->fetchAll();
            
            foreach ($chapters as $chapter):
            ?>
            <div class="sidebar-chapter">
                <a href="chapter.php?id=<?php echo $chapter['id']; ?>" 
                   class="<?php echo $chapter['id'] == $page['chapter_id'] ? 'active' : ''; ?>">
                    <i class="fas fa-folder me-2"></i>
                    <?php echo $current_language === 'fr' ? $chapter['title_fr'] : $chapter['title_en']; ?>
                </a>
                <div class="sidebar-pages">
                    <?php
                    $stmt_pages = $conn->prepare("
                        SELECT * FROM pages 
                        WHERE chapter_id = ? AND is_active = TRUE 
                        ORDER BY sort_order
                    ");
                    $stmt_pages->execute([$chapter['id']]);
                    $pages_list = $stmt_pages->fetchAll();
                    
                    foreach ($pages_list as $page_item):
                    ?>
                    <a href="page.php?slug=<?php echo $page_item['slug']; ?>" 
                       class="<?php echo $page_item['slug'] == $slug ? 'active' : ''; ?>">
                        <?php echo $current_language === 'fr' ? $page_item['title_fr'] : $page_item['title_en']; ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="docs-main">
        <div class="docs-content">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php"><?php echo $lang['home']; ?></a></li>
                    <li class="breadcrumb-item"><a href="section.php?id=<?php echo $page['section_id']; ?>">
                        <?php echo $current_language === 'fr' ? $page['section_title_fr'] : $page['section_title_en']; ?>
                    </a></li>
                    <li class="breadcrumb-item"><a href="chapter.php?id=<?php echo $page['chapter_id']; ?>">
                        <?php echo $current_language === 'fr' ? $page['chapter_title_fr'] : $page['chapter_title_en']; ?>
                    </a></li>
                    <li class="breadcrumb-item active"><?php echo $current_language === 'fr' ? $page['title_fr'] : $page['title_en']; ?></li>
                </ol>
            </nav>

            <!-- Page Content -->
            <article>
                <header class="mb-4">
                    <h1 class="display-5 fw-bold"><?php echo $current_language === 'fr' ? $page['title_fr'] : $page['title_en']; ?></h1>
                    <div class="text-muted">
                        <small>
                            <i class="fas fa-clock me-1"></i>
                            <?php echo $current_language === 'fr' ? 'Dernière mise à jour:' : 'Last updated:'; ?>
                            <?php echo date('F j, Y', strtotime($page['updated_at'])); ?>
                        </small>
                    </div>
                </header>

                <div class="page-content">
                    <?php echo $current_language === 'fr' ? $page['content_fr'] : $page['content_en']; ?>
                </div>
            </article>

            <!-- Page Navigation -->
            <div class="page-navigation">
                <div>
                    <?php if ($previous_page): ?>
                    <a href="page.php?slug=<?php echo $previous_page['slug']; ?>" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i>
                        <?php echo $lang['previous']; ?>: 
                        <?php echo $current_language === 'fr' ? $previous_page['title_fr'] : $previous_page['title_en']; ?>
                    </a>
                    <?php endif; ?>
                </div>
                <div>
                    <?php if ($next_page): ?>
                    <a href="page.php?slug=<?php echo $next_page['slug']; ?>" class="btn btn-primary">
                        <?php echo $lang['next']; ?>: 
                        <?php echo $current_language === 'fr' ? $next_page['title_fr'] : $next_page['title_en']; ?>
                        <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Admin Edit Button -->
            <?php if (isset($_SESSION['admin_logged_in'])): ?>
            <div class="admin-actions">
                <a href="admin/edit-page.php?id=<?php echo $page['id']; ?>" class="edit-btn" title="<?php echo $lang['edit_page']; ?>">
                    <i class="fas fa-edit"></i>
                </a>
            </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php include 'includes/footer.php'; ?>