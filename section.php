<?php
// section.php - Shows all chapters and pages for a section
require_once 'includes/config.php';
require_once 'includes/database.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$section_id = (int)$_GET['id'];
$db = new Database();
$conn = $db->getConnection();

// Get section details
$stmt = $conn->prepare("SELECT * FROM sections WHERE id = ? AND is_active = TRUE");
$stmt->execute([$section_id]);
$section = $stmt->fetch();

if (!$section) {
    header('Location: index.php');
    exit;
}

// Get chapters for this section
$stmt = $conn->prepare("
    SELECT c.*, 
           COUNT(p.id) as page_count
    FROM chapters c
    LEFT JOIN pages p ON c.id = p.chapter_id AND p.is_active = TRUE
    WHERE c.section_id = ? AND c.is_active = TRUE
    GROUP BY c.id
    ORDER BY c.sort_order
");
$stmt->execute([$section_id]);
$chapters = $stmt->fetchAll();

$page_title = $current_language === 'fr' ? $section['title_fr'] : $section['title_en'];
$current_page = strtolower(str_replace(' ', '_', $section['title_en']));

// Get all pages for sidebar
$stmt = $conn->prepare("
    SELECT p.*, c.title_en as chapter_title_en, c.title_fr as chapter_title_fr
    FROM pages p
    JOIN chapters c ON p.chapter_id = c.id
    WHERE c.section_id = ? AND p.is_active = TRUE AND c.is_active = TRUE
    ORDER BY c.sort_order, p.sort_order
");
$stmt->execute([$section_id]);
$all_pages = $stmt->fetchAll();
?>

<?php include 'includes/header.php'; ?>

<div class="docs-container">
    <!-- Sidebar -->
    <aside class="docs-sidebar">
        <div class="sidebar-section">
            <h5><?php echo $current_language === 'fr' ? $section['title_fr'] : $section['title_en']; ?></h5>
            <?php foreach ($chapters as $chapter): ?>
            <div class="sidebar-chapter">
                <a href="chapter.php?id=<?php echo $chapter['id']; ?>" 
                   class="<?php echo (isset($_GET['chapter_id']) && $_GET['chapter_id'] == $chapter['id']) ? 'active' : ''; ?>">
                    <i class="fas fa-folder me-2"></i>
                    <?php echo $current_language === 'fr' ? $chapter['title_fr'] : $chapter['title_en']; ?>
                    <span class="badge bg-secondary float-end"><?php echo $chapter['page_count']; ?></span>
                </a>
                <div class="sidebar-pages">
                    <?php 
                    $chapter_pages = array_filter($all_pages, function($page) use ($chapter) {
                        return $page['chapter_id'] == $chapter['id'];
                    });
                    foreach ($chapter_pages as $page): 
                    ?>
                    <a href="page.php?slug=<?php echo $page['slug']; ?>" 
                       class="<?php echo (isset($_GET['slug']) && $_GET['slug'] == $page['slug']) ? 'active' : ''; ?>">
                        <?php echo $current_language === 'fr' ? $page['title_fr'] : $page['title_en']; ?>
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
                    <li class="breadcrumb-item active"><?php echo $current_language === 'fr' ? $section['title_fr'] : $section['title_en']; ?></li>
                </ol>
            </nav>

            <!-- Section Header -->
            <div class="section-header mb-5">
                <div class="d-flex align-items-center mb-3">
                    <i class="<?php echo $section['icon']; ?> fa-2x text-primary me-3"></i>
                    <h1 class="display-4 fw-bold"><?php echo $current_language === 'fr' ? $section['title_fr'] : $section['title_en']; ?></h1>
                </div>
                <p class="lead text-muted">
                    <?php echo $current_language === 'fr' ? $section['description_fr'] : $section['description_en']; ?>
                </p>
            </div>

            <!-- Chapters Grid -->
            <div class="row g-4">
                <?php foreach ($chapters as $chapter): ?>
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <h3 class="h5 fw-bold mb-3">
                                <i class="fas fa-folder text-primary me-2"></i>
                                <?php echo $current_language === 'fr' ? $chapter['title_fr'] : $chapter['title_en']; ?>
                            </h3>
                            <p class="text-muted mb-3">
                                <?php echo $current_language === 'fr' ? $chapter['description_fr'] : $chapter['description_en']; ?>
                            </p>
                            <div class="chapter-pages">
                                <?php 
                                $chapter_pages = array_filter($all_pages, function($page) use ($chapter) {
                                    return $page['chapter_id'] == $chapter['id'];
                                });
                                foreach ($chapter_pages as $page): 
                                ?>
                                <a href="page.php?slug=<?php echo $page['slug']; ?>" class="d-block text-decoration-none py-1">
                                    <i class="fas fa-file text-muted me-2"></i>
                                    <?php echo $current_language === 'fr' ? $page['title_fr'] : $page['title_en']; ?>
                                </a>
                                <?php endforeach; ?>
                            </div>
                            <div class="mt-3">
                                <a href="chapter.php?id=<?php echo $chapter['id']; ?>" class="btn btn-outline-primary btn-sm">
                                    <?php echo $current_language === 'fr' ? 'Voir le chapitre' : 'View Chapter'; ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>
</div>

<?php include 'includes/footer.php'; ?>