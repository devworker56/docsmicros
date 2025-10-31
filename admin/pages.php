<?php
// admin/pages.php - Manage pages
require_once 'config.php';

$page_title = $current_language === 'fr' ? 'Gérer les Pages' : 'Manage Pages';
$current_page = 'pages';

$db = new Database();
$conn = $db->getConnection();

// Get all sections and chapters for dropdowns
$sections = $conn->query("SELECT * FROM sections WHERE is_active = TRUE ORDER BY sort_order")->fetchAll();
$chapters = $conn->query("SELECT * FROM chapters WHERE is_active = TRUE ORDER BY sort_order")->fetchAll();

// Filter by chapter if specified
$filter_chapter_id = isset($_GET['chapter_id']) ? (int)$_GET['chapter_id'] : null;

// Handle form actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        if ($action === 'create' || $action === 'update') {
            $chapter_id = (int)$_POST['chapter_id'];
            $title_en = trim($_POST['title_en']);
            $title_fr = trim($_POST['title_fr']);
            $content_en = trim($_POST['content_en']);
            $content_fr = trim($_POST['content_fr']);
            $slug = trim($_POST['slug']);
            $sort_order = (int)$_POST['sort_order'];
            $is_active = isset($_POST['is_active']) ? 1 : 0;
            
            // Generate slug if empty
            if (empty($slug)) {
                $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $title_en));
            }
            
            if ($action === 'create') {
                $stmt = $conn->prepare("
                    INSERT INTO pages (chapter_id, title_en, title_fr, content_en, content_fr, slug, sort_order, is_active)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$chapter_id, $title_en, $title_fr, $content_en, $content_fr, $slug, $sort_order, $is_active]);
                
                // Save version
                $page_id = $conn->lastInsertId();
                $stmt = $conn->prepare("
                    INSERT INTO page_versions (page_id, content_en, content_fr, user_id)
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->execute([$page_id, $content_en, $content_fr, $_SESSION['admin_user_id']]);
                
                $success = $current_language === 'fr' ? 'Page créée avec succès!' : 'Page created successfully!';
            } else {
                $id = (int)$_POST['id'];
                
                // Save current version before update
                $stmt = $conn->prepare("SELECT content_en, content_fr FROM pages WHERE id = ?");
                $stmt->execute([$id]);
                $old_content = $stmt->fetch();
                
                $stmt = $conn->prepare("
                    UPDATE pages 
                    SET chapter_id = ?, title_en = ?, title_fr = ?, content_en = ?, content_fr = ?, 
                        slug = ?, sort_order = ?, is_active = ?, updated_at = CURRENT_TIMESTAMP
                    WHERE id = ?
                ");
                $stmt->execute([$chapter_id, $title_en, $title_fr, $content_en, $content_fr, $slug, $sort_order, $is_active, $id]);
                
                // Save version if content changed
                if ($old_content['content_en'] !== $content_en || $old_content['content_fr'] !== $content_fr) {
                    $stmt = $conn->prepare("
                        INSERT INTO page_versions (page_id, content_en, content_fr, user_id)
                        VALUES (?, ?, ?, ?)
                    ");
                    $stmt->execute([$id, $content_en, $content_fr, $_SESSION['admin_user_id']]);
                }
                
                $success = $current_language === 'fr' ? 'Page mise à jour avec succès!' : 'Page updated successfully!';
            }
        } elseif ($action === 'delete') {
            $id = (int)$_POST['id'];
            $stmt = $conn->prepare("DELETE FROM pages WHERE id = ?");
            $stmt->execute([$id]);
            $success = $current_language === 'fr' ? 'Page supprimée avec succès!' : 'Page deleted successfully!';
        }
    }
}

// Get all pages with chapter and section info
$sql = "
    SELECT p.*, 
           c.title_en as chapter_title_en, c.title_fr as chapter_title_fr,
           s.title_en as section_title_en, s.title_fr as section_title_fr
    FROM pages p
    JOIN chapters c ON p.chapter_id = c.id
    JOIN sections s ON c.section_id = s.id
";
$params = [];

if ($filter_chapter_id) {
    $sql .= " WHERE p.chapter_id = ?";
    $params[] = $filter_chapter_id;
}

$sql .= " ORDER BY s.sort_order, c.sort_order, p.sort_order";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$pages = $stmt->fetchAll();

// Get page for editing
$edit_page = null;
if (isset($_GET['edit'])) {
    $stmt = $conn->prepare("SELECT * FROM pages WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $edit_page = $stmt->fetch();
}
?>

<?php include 'header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><?php echo $admin_lang['pages']; ?></h1>
    <div>
        <?php if ($filter_chapter_id): ?>
        <a href="pages.php" class="btn btn-outline-secondary me-2">
            <i class="fas fa-times me-2"></i>
            <?php echo $current_language === 'fr' ? 'Effacer le filtre' : 'Clear Filter'; ?>
        </a>
        <?php endif; ?>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#pageModal">
            <i class="fas fa-plus me-2"></i>
            <?php echo $admin_lang['add_new']; ?>
        </button>
    </div>
</div>

<?php if (isset($error)): ?>
<div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<?php if (isset($success)): ?>
<div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<!-- Pages Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th><?php echo $admin_lang['title']; ?></th>
                        <th><?php echo $current_language === 'fr' ? 'Chapitre' : 'Chapter'; ?></th>
                        <th><?php echo $current_language === 'fr' ? 'Section' : 'Section'; ?></th>
                        <th>Slug</th>
                        <th><?php echo $current_language === 'fr' ? 'Ordre' : 'Order'; ?></th>
                        <th><?php echo $admin_lang['status']; ?></th>
                        <th><?php echo $admin_lang['actions']; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pages)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="fas fa-file fa-2x mb-3"></i>
                            <p><?php echo $current_language === 'fr' ? 'Aucune page trouvée' : 'No pages found'; ?></p>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($pages as $page): ?>
                        <tr>
                            <td>
                                <div>
                                    <strong><?php echo $page['title_en']; ?></strong><br>
                                    <small class="text-muted"><?php echo $page['title_fr']; ?></small>
                                </div>
                            </td>
                            <td>
                                <a href="?chapter_id=<?php echo $page['chapter_id']; ?>" class="badge bg-light text-dark text-decoration-none">
                                    <?php echo $current_language === 'fr' ? $page['chapter_title_fr'] : $page['chapter_title_en']; ?>
                                </a>
                            </td>
                            <td>
                                <span class="badge bg-secondary">
                                    <?php echo $current_language === 'fr' ? $page['section_title_fr'] : $page['section_title_en']; ?>
                                </span>
                            </td>
                            <td><code><?php echo $page['slug']; ?></code></td>
                            <td><?php echo $page['sort_order']; ?></td>
                            <td>
                                <span class="badge <?php echo $page['is_active'] ? 'bg-success' : 'bg-secondary'; ?>">
                                    <?php echo $page['is_active'] ? $admin_lang['active'] : $admin_lang['inactive']; ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="../page.php?slug=<?php echo $page['slug']; ?>" 
                                       class="btn btn-outline-info" target="_blank" title="<?php echo $current_language === 'fr' ? 'Voir la page' : 'View page'; ?>">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="?edit=<?php echo $page['id']; ?>" 
                                       class="btn btn-outline-primary" title="<?php echo $admin_lang['edit']; ?>">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $page['id']; ?>">
                                        <button type="submit" class="btn btn-outline-danger btn-delete" 
                                                data-confirm="<?php echo $current_language === 'fr' 
                                                    ? 'Êtes-vous sûr de vouloir supprimer cette page ?' 
                                                    : 'Are you sure you want to delete this page?'; ?>"
                                                title="<?php echo $admin_lang['delete']; ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Page Modal -->
<div class="modal fade" id="pageModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form method="POST" action="">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <?php echo $edit_page ? 
                            ($current_language === 'fr' ? 'Modifier la Page' : 'Edit Page') : 
                            ($current_language === 'fr' ? 'Nouvelle Page' : 'New Page'); ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="<?php echo $edit_page ? 'update' : 'create'; ?>">
                    <?php if ($edit_page): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_page['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="chapter_id" class="form-label">
                                    <?php echo $current_language === 'fr' ? 'Chapitre' : 'Chapter'; ?> *
                                </label>
                                <select class="form-select" id="chapter_id" name="chapter_id" required>
                                    <option value=""><?php echo $current_language === 'fr' ? 'Sélectionner un chapitre' : 'Select a chapter'; ?></option>
                                    <?php foreach ($chapters as $chapter): ?>
                                    <option value="<?php echo $chapter['id']; ?>" 
                                        <?php echo ($edit_page && $edit_page['chapter_id'] == $chapter['id']) ? 'selected' : ''; ?>>
                                        <?php echo $current_language === 'fr' ? $chapter['title_fr'] : $chapter['title_en']; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="slug" class="form-label">Slug *</label>
                                <input type="text" class="form-control" id="slug" name="slug" 
                                       value="<?php echo $edit_page ? htmlspecialchars($edit_page['slug']) : ''; ?>" 
                                       required>
                                <small class="text-muted">
                                    <?php echo $current_language === 'fr' 
                                        ? 'URL-friendly version du titre (auto-généré si vide)'
                                        : 'URL-friendly version of title (auto-generated if empty)'; ?>
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="title_en" class="form-label">Title (English) *</label>
                                <input type="text" class="form-control" id="title_en" name="title_en" 
                                       value="<?php echo $edit_page ? htmlspecialchars($edit_page['title_en']) : ''; ?>" 
                                       required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="title_fr" class="form-label">Titre (Français) *</label>
                                <input type="text" class="form-control" id="title_fr" name="title_fr" 
                                       value="<?php echo $edit_page ? htmlspecialchars($edit_page['title_fr']) : ''; ?>" 
                                       required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="content_en" class="form-label">Content (English) *</label>
                                <textarea class="form-control summernote" id="content_en" name="content_en" rows="10" required><?php 
                                    echo $edit_page ? htmlspecialchars($edit_page['content_en']) : ''; 
                                ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="content_fr" class="form-label">Contenu (Français) *</label>
                                <textarea class="form-control summernote" id="content_fr" name="content_fr" rows="10" required><?php 
                                    echo $edit_page ? htmlspecialchars($edit_page['content_fr']) : ''; 
                                ?></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sort_order" class="form-label">
                                    <?php echo $current_language === 'fr' ? 'Ordre d\'affichage' : 'Sort Order'; ?>
                                </label>
                                <input type="number" class="form-control" id="sort_order" name="sort_order" 
                                       value="<?php echo $edit_page ? $edit_page['sort_order'] : '0'; ?>" 
                                       min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3 pt-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                           <?php echo (!$edit_page || $edit_page['is_active']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="is_active">
                                        <?php echo $admin_lang['active']; ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <?php echo $admin_lang['cancel']; ?>
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>
                        <?php echo $admin_lang['save']; ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if ($edit_page): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var pageModal = new bootstrap.Modal(document.getElementById('pageModal'));
    pageModal.show();
});
</script>
<?php endif; ?>

<?php include 'footer.php'; ?>