<?php
// chapter.php - Shows all pages in a chapter
require_once 'includes/config.php';
require_once 'includes/database.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$chapter_id = (int)$_GET['id'];
$db = new Database();
$conn = $db->getConnection();

// Get chapter details
$stmt = $conn->prepare("
    SELECT c.*, s.id as section_id, s.title_en as section_title_en, s.title_fr as section_title_fr, s.icon as section_icon
    FROM chapters c
    JOIN sections s ON c.section_id = s.id
    WHERE c.id = ? AND c.is_active = TRUE AND s.is_active = TRUE
");
$stmt->execute([$chapter_id]);
$chapter = $stmt->fetch();

if (!$chapter) {
    header('Location: index.php');
    exit;
}

// Get pages for this chapter
$stmt = $conn->prepare("
    SELECT * FROM pages 
    WHERE chapter_id = ? AND is_active = TRUE 
    ORDER BY sort_order
");
$stmt->execute([$chapter_id]);
$pages = $stmt->fetchAll();

$page_title = $current_language === 'fr' ? $chapter['title_fr'] : $chapter['title_en'];
$current_page = 'chapter';
?>

<?php include 'includes/header.php'; ?>

<div class="docs-container">
    <!-- Sidebar -->
    <aside class="docs-sidebar">
        <div class="sidebar-section">
            <h5><?php echo $current_language === 'fr' ? $chapter['section_title_fr'] : $chapter['section_title_en']; ?></h5>
            
            <!-- Get all chapters for this section -->
            <?php
            $stmt = $conn->prepare("
                SELECT c.* FROM chapters c
                WHERE c.section_id = ? AND c.is_active = TRUE
                ORDER BY c.sort_order
            ");
            $stmt->execute([$chapter['section_id']]);
            $chapters = $stmt->fetchAll();
            
            foreach ($chapters as $chap):
            ?>
            <div class="sidebar-chapter">
                <a href="chapter.php?id=<?php echo $chap['id']; ?>" 
                   class="<?php echo $chap['id'] == $chapter_id ? 'active' : ''; ?>">
                    <i class="fas fa-folder me-2"></i>
                    <?php echo $current_language === 'fr' ? $chap['title_fr'] : $chap['title_en']; ?>
                </a>
                <div class="sidebar-pages">
                    <?php
                    $stmt_pages = $conn->prepare("
                        SELECT * FROM pages 
                        WHERE chapter_id = ? AND is_active = TRUE 
                        ORDER BY sort_order
                    ");
                    $stmt_pages->execute([$chap['id']]);
                    $pages_list = $stmt_pages->fetchAll();
                    
                    foreach ($pages_list as $page_item):
                    ?>
                    <a href="page.php?slug=<?php echo $page_item['slug']; ?>">
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
                    <li class="breadcrumb-item"><a href="section.php?id=<?php echo $chapter['section_id']; ?>">
                        <?php echo $current_language === 'fr' ? $chapter['section_title_fr'] : $chapter['section_title_en']; ?>
                    </a></li>
                    <li class="breadcrumb-item active"><?php echo $current_language === 'fr' ? $chapter['title_fr'] : $chapter['title_en']; ?></li>
                </ol>
            </nav>

            <!-- Chapter Header -->
            <div class="chapter-header mb-5">
                <div class="d-flex align-items-center mb-3">
                    <i class="<?php echo $chapter['section_icon']; ?> fa-2x text-primary me-3"></i>
                    <h1 class="display-4 fw-bold"><?php echo $current_language === 'fr' ? $chapter['title_fr'] : $chapter['title_en']; ?></h1>
                </div>
                <p class="lead text-muted">
                    <?php echo $current_language === 'fr' ? $chapter['description_fr'] : $chapter['description_en']; ?>
                </p>
            </div>

            <!-- Pages List -->
            <div class="pages-list">
                <?php if (empty($pages)): ?>
                <div class="alert alert-info">
                    <?php echo $current_language === 'fr' 
                        ? 'Aucune page disponible pour le moment.' 
                        : 'No pages available at the moment.'; ?>
                </div>
                <?php else: ?>
                    <?php foreach ($pages as $page): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <h3 class="h5 fw-bold">
                                <a href="page.php?slug=<?php echo $page['slug']; ?>" class="text-decoration-none">
                                    <?php echo $current_language === 'fr' ? $page['title_fr'] : $page['title_en']; ?>
                                </a>
                            </h3>
                            <p class="text-muted mb-0">
                                <?php 
                                $content = $current_language === 'fr' ? $page['content_fr'] : $page['content_en'];
                                $plain_content = strip_tags($content);
                                echo substr($plain_content, 0, 150) . '...';
                                ?>
                            </p>
                            <div class="mt-2">
                                <a href="page.php?slug=<?php echo $page['slug']; ?>" class="btn btn-sm btn-outline-primary">
                                    <?php echo $current_language === 'fr' ? 'Lire la suite' : 'Read more'; ?>
                                    <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<?php include 'includes/footer.php'; ?>