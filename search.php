<?php
// search.php - Search across documentation
require_once 'includes/config.php';
require_once 'includes/database.php';

$search_query = isset($_GET['q']) ? trim($_GET['q']) : '';
$page_title = $current_language === 'fr' ? 'Résultats de recherche' : 'Search Results';
$current_page = 'search';

$results = [];
if (!empty($search_query)) {
    $db = new Database();
    $conn = $db->getConnection();
    
    $search_term = '%' . $search_query . '%';
    $stmt = $conn->prepare("
        SELECT p.*, 
               c.title_en as chapter_title_en, c.title_fr as chapter_title_fr,
               s.title_en as section_title_en, s.title_fr as section_title_fr
        FROM pages p
        JOIN chapters c ON p.chapter_id = c.id
        JOIN sections s ON c.section_id = s.id
        WHERE (p.title_en LIKE ? OR p.title_fr LIKE ? OR p.content_en LIKE ? OR p.content_fr LIKE ?)
        AND p.is_active = TRUE AND c.is_active = TRUE AND s.is_active = TRUE
        ORDER BY 
            (CASE 
                WHEN p.title_en LIKE ? THEN 1
                WHEN p.title_fr LIKE ? THEN 1
                WHEN p.content_en LIKE ? THEN 2
                WHEN p.content_fr LIKE ? THEN 2
                ELSE 3
            END),
            s.sort_order, c.sort_order, p.sort_order
    ");
    
    $stmt->execute([
        $search_term, $search_term, $search_term, $search_term,
        $search_term, $search_term, $search_term, $search_term
    ]);
    $results = $stmt->fetchAll();
}
?>

<?php include 'includes/header.php'; ?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <!-- Search Header -->
            <div class="text-center mb-5">
                <h1 class="display-5 fw-bold text-primary mb-3">
                    <?php echo $current_language === 'fr' ? 'Recherche' : 'Search'; ?>
                </h1>
                <form action="search.php" method="GET" class="search-box">
                    <div class="input-group input-group-lg">
                        <input type="text" class="form-control" name="q" 
                               value="<?php echo htmlspecialchars($search_query); ?>"
                               placeholder="<?php echo $lang['search_placeholder']; ?>">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Search Results -->
            <?php if (empty($search_query)): ?>
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle me-2"></i>
                    <?php echo $current_language === 'fr' 
                        ? 'Veuillez entrer un terme de recherche.'
                        : 'Please enter a search term.'; ?>
                </div>
            <?php elseif (empty($results)): ?>
                <div class="alert alert-warning text-center">
                    <i class="fas fa-search me-2"></i>
                    <?php echo $current_language === 'fr' 
                        ? 'Aucun résultat trouvé pour "' . htmlspecialchars($search_query) . '"'
                        : 'No results found for "' . htmlspecialchars($search_query) . '"'; ?>
                </div>
            <?php else: ?>
                <div class="search-results">
                    <p class="text-muted mb-4">
                        <?php echo $current_language === 'fr' 
                            ? count($results) . ' résultat(s) trouvé(s) pour "' . htmlspecialchars($search_query) . '"'
                            : count($results) . ' result(s) found for "' . htmlspecialchars($search_query) . '"'; ?>
                    </p>
                    
                    <?php foreach ($results as $result): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h3 class="h5 fw-bold">
                                    <a href="page.php?slug=<?php echo $result['slug']; ?>" class="text-decoration-none">
                                        <?php echo $current_language === 'fr' ? $result['title_fr'] : $result['title_en']; ?>
                                    </a>
                                </h3>
                                <span class="badge bg-primary">
                                    <?php echo $current_language === 'fr' ? $result['section_title_fr'] : $result['section_title_en']; ?>
                                </span>
                            </div>
                            
                            <p class="text-muted small mb-2">
                                <i class="fas fa-folder me-1"></i>
                                <?php echo $current_language === 'fr' ? $result['chapter_title_fr'] : $result['chapter_title_en']; ?>
                            </p>
                            
                            <p class="mb-0">
                                <?php 
                                $content = $current_language === 'fr' ? $result['content_fr'] : $result['content_en'];
                                $plain_content = strip_tags($content);
                                
                                // Highlight search term in content
                                $highlighted_content = preg_replace(
                                    "/(" . preg_quote($search_query, '/') . ")/i", 
                                    '<mark>$1</mark>', 
                                    substr($plain_content, 0, 200)
                                );
                                echo $highlighted_content . '...';
                                ?>
                            </p>
                            
                            <a href="page.php?slug=<?php echo $result['slug']; ?>" class="btn btn-sm btn-outline-primary mt-2">
                                <?php echo $current_language === 'fr' ? 'Lire la suite' : 'Read more'; ?>
                                <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>