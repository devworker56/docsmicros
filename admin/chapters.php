<?php
// admin/chapters.php - Manage chapters
require_once 'config.php';

$page_title = $current_language === 'fr' ? 'Gérer les Chapitres' : 'Manage Chapters';
$current_page = 'chapters';

$db = new Database();
$conn = $db->getConnection();

// Get all sections for dropdown
$sections = $conn->query("SELECT * FROM sections WHERE is_active = TRUE ORDER BY sort_order")->fetchAll();

// Handle form actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        if ($action === 'create' || $action === 'update') {
            $section_id = (int)$_POST['section_id'];
            $title_en = trim($_POST['title_en']);
            $title_fr = trim($_POST['title_fr']);
            $description_en = trim($_POST['description_en']);
            $description_fr = trim($_POST['description_fr']);
            $sort_order = (int)$_POST['sort_order'];
            $is_active = isset($_POST['is_active']) ? 1 : 0;
            
            if ($action === 'create') {
                $stmt = $conn->prepare("
                    INSERT INTO chapters (section_id, title_en, title_fr, description_en, description_fr, sort_order, is_active)
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$section_id, $title_en, $title_fr, $description_en, $description_fr, $sort_order, $is_active]);
                $success = $current_language === 'fr' ? 'Chapitre créé avec succès!' : 'Chapter created successfully!';
            } else {
                $id = (int)$_POST['id'];
                $stmt = $conn->prepare("
                    UPDATE chapters 
                    SET section_id = ?, title_en = ?, title_fr = ?, description_en = ?, description_fr = ?, 
                        sort_order = ?, is_active = ?, updated_at = CURRENT_TIMESTAMP
                    WHERE id = ?
                ");
                $stmt->execute([$section_id, $title_en, $title_fr, $description_en, $description_fr, $sort_order, $is_active, $id]);
                $success = $current_language === 'fr' ? 'Chapitre mis à jour avec succès!' : 'Chapter updated successfully!';
            }
        } elseif ($action === 'delete') {
            $id = (int)$_POST['id'];
            
            // Check if chapter has pages
            $stmt = $conn->prepare("SELECT COUNT(*) as count FROM pages WHERE chapter_id = ?");
            $stmt->execute([$id]);
            $page_count = $stmt->fetch()['count'];
            
            if ($page_count > 0) {
                $error = $current_language === 'fr' 
                    ? 'Impossible de supprimer ce chapitre car il contient des pages.'
                    : 'Cannot delete this chapter because it contains pages.';
            } else {
                $stmt = $conn->prepare("DELETE FROM chapters WHERE id = ?");
                $stmt->execute([$id]);
                $success = $current_language === 'fr' ? 'Chapitre supprimé avec succès!' : 'Chapter deleted successfully!';
            }
        }
    }
}

// Get all chapters with section info
$stmt = $conn->prepare("
    SELECT c.*, s.title_en as section_title_en, s.title_fr as section_title_fr
    FROM chapters c
    JOIN sections s ON c.section_id = s.id
    ORDER BY s.sort_order, c.sort_order
");
$stmt->execute();
$chapters = $stmt->fetchAll();

// Get chapter for editing
$edit_chapter = null;
if (isset($_GET['edit'])) {
    $stmt = $conn->prepare("SELECT * FROM chapters WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $edit_chapter = $stmt->fetch();
}
?>

<?php include 'header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><?php echo $admin_lang['chapters']; ?></h1>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#chapterModal">
        <i class="fas fa-plus me-2"></i>
        <?php echo $admin_lang['add_new']; ?>
    </button>
</div>

<?php if (isset($error)): ?>
<div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<?php if (isset($success)): ?>
<div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<!-- Chapters Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th><?php echo $admin_lang['title']; ?></th>
                        <th><?php echo $current_language === 'fr' ? 'Section' : 'Section'; ?></th>
                        <th><?php echo $current_language === 'fr' ? 'Ordre' : 'Order'; ?></th>
                        <th><?php echo $admin_lang['status']; ?></th>
                        <th><?php echo $current_language === 'fr' ? 'Pages' : 'Pages'; ?></th>
                        <th><?php echo $admin_lang['actions']; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($chapters)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="fas fa-book-open fa-2x mb-3"></i>
                            <p><?php echo $current_language === 'fr' ? 'Aucun chapitre trouvé' : 'No chapters found'; ?></p>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($chapters as $chapter): 
                        // Count pages in this chapter
                        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM pages WHERE chapter_id = ? AND is_active = TRUE");
                        $stmt->execute([$chapter['id']]);
                        $page_count = $stmt->fetch()['count'];
                        ?>
                        <tr>
                            <td>
                                <div>
                                    <strong><?php echo $chapter['title_en']; ?></strong><br>
                                    <small class="text-muted"><?php echo $chapter['title_fr']; ?></small>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">
                                    <?php echo $current_language === 'fr' ? $chapter['section_title_fr'] : $chapter['section_title_en']; ?>
                                </span>
                            </td>
                            <td><?php echo $chapter['sort_order']; ?></td>
                            <td>
                                <span class="badge <?php echo $chapter['is_active'] ? 'bg-success' : 'bg-secondary'; ?>">
                                    <?php echo $chapter['is_active'] ? $admin_lang['active'] : $admin_lang['inactive']; ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-info"><?php echo $page_count; ?></span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="?edit=<?php echo $chapter['id']; ?>" class="btn btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $chapter['id']; ?>">
                                        <button type="submit" class="btn btn-outline-danger btn-delete" 
                                                data-confirm="<?php echo $current_language === 'fr' 
                                                    ? 'Êtes-vous sûr de vouloir supprimer ce chapitre ?' 
                                                    : 'Are you sure you want to delete this chapter?'; ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    <a href="pages.php?chapter_id=<?php echo $chapter['id']; ?>" class="btn btn-outline-info">
                                        <i class="fas fa-file"></i>
                                    </a>
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

<!-- Chapter Modal -->
<div class="modal fade" id="chapterModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <?php echo $edit_chapter ? 
                            ($current_language === 'fr' ? 'Modifier le Chapitre' : 'Edit Chapter') : 
                            ($current_language === 'fr' ? 'Nouveau Chapitre' : 'New Chapter'); ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="<?php echo $edit_chapter ? 'update' : 'create'; ?>">
                    <?php if ($edit_chapter): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_chapter['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label for="section_id" class="form-label">
                            <?php echo $current_language === 'fr' ? 'Section' : 'Section'; ?> *
                        </label>
                        <select class="form-select" id="section_id" name="section_id" required>
                            <option value=""><?php echo $current_language === 'fr' ? 'Sélectionner une section' : 'Select a section'; ?></option>
                            <?php foreach ($sections as $section): ?>
                            <option value="<?php echo $section['id']; ?>" 
                                <?php echo ($edit_chapter && $edit_chapter['section_id'] == $section['id']) ? 'selected' : ''; ?>>
                                <?php echo $current_language === 'fr' ? $section['title_fr'] : $section['title_en']; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="title_en" class="form-label">Title (English) *</label>
                                <input type="text" class="form-control" id="title_en" name="title_en" 
                                       value="<?php echo $edit_chapter ? htmlspecialchars($edit_chapter['title_en']) : ''; ?>" 
                                       required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="title_fr" class="form-label">Titre (Français) *</label>
                                <input type="text" class="form-control" id="title_fr" name="title_fr" 
                                       value="<?php echo $edit_chapter ? htmlspecialchars($edit_chapter['title_fr']) : ''; ?>" 
                                       required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="description_en" class="form-label">Description (English)</label>
                                <textarea class="form-control" id="description_en" name="description_en" rows="3"><?php 
                                    echo $edit_chapter ? htmlspecialchars($edit_chapter['description_en']) : ''; 
                                ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="description_fr" class="form-label">Description (Français)</label>
                                <textarea class="form-control" id="description_fr" name="description_fr" rows="3"><?php 
                                    echo $edit_chapter ? htmlspecialchars($edit_chapter['description_fr']) : ''; 
                                ?></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="sort_order" class="form-label">
                            <?php echo $current_language === 'fr' ? 'Ordre d\'affichage' : 'Sort Order'; ?>
                        </label>
                        <input type="number" class="form-control" id="sort_order" name="sort_order" 
                               value="<?php echo $edit_chapter ? $edit_chapter['sort_order'] : '0'; ?>" 
                               min="0">
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                   <?php echo (!$edit_chapter || $edit_chapter['is_active']) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="is_active">
                                <?php echo $admin_lang['active']; ?>
                            </label>
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

<?php if ($edit_chapter): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var chapterModal = new bootstrap.Modal(document.getElementById('chapterModal'));
    chapterModal.show();
});
</script>
<?php endif; ?>

<?php include 'footer.php'; ?>