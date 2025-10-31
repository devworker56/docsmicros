<?php
// admin/sections.php - Manage sections
require_once 'config.php';

$page_title = $current_language === 'fr' ? 'Gérer les Sections' : 'Manage Sections';
$current_page = 'sections';

$db = new Database();
$conn = $db->getConnection();

// Handle form actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        if ($action === 'create' || $action === 'update') {
            $title_en = trim($_POST['title_en']);
            $title_fr = trim($_POST['title_fr']);
            $description_en = trim($_POST['description_en']);
            $description_fr = trim($_POST['description_fr']);
            $icon = trim($_POST['icon']);
            $sort_order = (int)$_POST['sort_order'];
            $is_active = isset($_POST['is_active']) ? 1 : 0;
            
            if ($action === 'create') {
                $stmt = $conn->prepare("
                    INSERT INTO sections (title_en, title_fr, description_en, description_fr, icon, sort_order, is_active)
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$title_en, $title_fr, $description_en, $description_fr, $icon, $sort_order, $is_active]);
                $success = $current_language === 'fr' ? 'Section créée avec succès!' : 'Section created successfully!';
            } else {
                $id = (int)$_POST['id'];
                $stmt = $conn->prepare("
                    UPDATE sections 
                    SET title_en = ?, title_fr = ?, description_en = ?, description_fr = ?, 
                        icon = ?, sort_order = ?, is_active = ?, updated_at = CURRENT_TIMESTAMP
                    WHERE id = ?
                ");
                $stmt->execute([$title_en, $title_fr, $description_en, $description_fr, $icon, $sort_order, $is_active, $id]);
                $success = $current_language === 'fr' ? 'Section mise à jour avec succès!' : 'Section updated successfully!';
            }
        } elseif ($action === 'delete') {
            $id = (int)$_POST['id'];
            
            // Check if section has chapters
            $stmt = $conn->prepare("SELECT COUNT(*) as count FROM chapters WHERE section_id = ?");
            $stmt->execute([$id]);
            $chapter_count = $stmt->fetch()['count'];
            
            if ($chapter_count > 0) {
                $error = $current_language === 'fr' 
                    ? 'Impossible de supprimer cette section car elle contient des chapitres.'
                    : 'Cannot delete this section because it contains chapters.';
            } else {
                $stmt = $conn->prepare("DELETE FROM sections WHERE id = ?");
                $stmt->execute([$id]);
                $success = $current_language === 'fr' ? 'Section supprimée avec succès!' : 'Section deleted successfully!';
            }
        }
    }
}

// Get all sections
$stmt = $conn->prepare("SELECT * FROM sections ORDER BY sort_order, created_at DESC");
$stmt->execute();
$sections = $stmt->fetchAll();

// Get section for editing
$edit_section = null;
if (isset($_GET['edit'])) {
    $stmt = $conn->prepare("SELECT * FROM sections WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $edit_section = $stmt->fetch();
}
?>

<?php include 'header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><?php echo $admin_lang['sections']; ?></h1>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#sectionModal">
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

<!-- Sections Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th><?php echo $admin_lang['title']; ?></th>
                        <th>Icon</th>
                        <th><?php echo $current_language === 'fr' ? 'Ordre' : 'Order'; ?></th>
                        <th><?php echo $admin_lang['status']; ?></th>
                        <th><?php echo $admin_lang['created_at']; ?></th>
                        <th><?php echo $admin_lang['actions']; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($sections)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="fas fa-folder-open fa-2x mb-3"></i>
                            <p><?php echo $current_language === 'fr' ? 'Aucune section trouvée' : 'No sections found'; ?></p>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($sections as $section): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="<?php echo $section['icon']; ?> text-primary me-3"></i>
                                    <div>
                                        <strong><?php echo $section['title_en']; ?></strong><br>
                                        <small class="text-muted"><?php echo $section['title_fr']; ?></small>
                                    </div>
                                </div>
                            </td>
                            <td><code><?php echo $section['icon']; ?></code></td>
                            <td><?php echo $section['sort_order']; ?></td>
                            <td>
                                <span class="badge <?php echo $section['is_active'] ? 'bg-success' : 'bg-secondary'; ?>">
                                    <?php echo $section['is_active'] ? $admin_lang['active'] : $admin_lang['inactive']; ?>
                                </span>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <?php echo date('M j, Y', strtotime($section['created_at'])); ?>
                                </small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="?edit=<?php echo $section['id']; ?>" class="btn btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $section['id']; ?>">
                                        <button type="submit" class="btn btn-outline-danger btn-delete" 
                                                data-confirm="<?php echo $current_language === 'fr' 
                                                    ? 'Êtes-vous sûr de vouloir supprimer cette section ?' 
                                                    : 'Are you sure you want to delete this section?'; ?>">
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

<!-- Section Modal -->
<div class="modal fade" id="sectionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <?php echo $edit_section ? 
                            ($current_language === 'fr' ? 'Modifier la Section' : 'Edit Section') : 
                            ($current_language === 'fr' ? 'Nouvelle Section' : 'New Section'); ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="<?php echo $edit_section ? 'update' : 'create'; ?>">
                    <?php if ($edit_section): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_section['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="title_en" class="form-label">Title (English) *</label>
                                <input type="text" class="form-control" id="title_en" name="title_en" 
                                       value="<?php echo $edit_section ? htmlspecialchars($edit_section['title_en']) : ''; ?>" 
                                       required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="title_fr" class="form-label">Titre (Français) *</label>
                                <input type="text" class="form-control" id="title_fr" name="title_fr" 
                                       value="<?php echo $edit_section ? htmlspecialchars($edit_section['title_fr']) : ''; ?>" 
                                       required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="description_en" class="form-label">Description (English)</label>
                                <textarea class="form-control" id="description_en" name="description_en" rows="3"><?php 
                                    echo $edit_section ? htmlspecialchars($edit_section['description_en']) : ''; 
                                ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="description_fr" class="form-label">Description (Français)</label>
                                <textarea class="form-control" id="description_fr" name="description_fr" rows="3"><?php 
                                    echo $edit_section ? htmlspecialchars($edit_section['description_fr']) : ''; 
                                ?></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="icon" class="form-label">Icon (Font Awesome) *</label>
                                <input type="text" class="form-control" id="icon" name="icon" 
                                       value="<?php echo $edit_section ? htmlspecialchars($edit_section['icon']) : 'fas fa-folder'; ?>" 
                                       required>
                                <small class="text-muted">
                                    <?php echo $current_language === 'fr' 
                                        ? 'Ex: fas fa-mobile-alt, fas fa-heart, fas fa-store' 
                                        : 'Ex: fas fa-mobile-alt, fas fa-heart, fas fa-store'; ?>
                                </small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sort_order" class="form-label">
                                    <?php echo $current_language === 'fr' ? 'Ordre d\'affichage' : 'Sort Order'; ?>
                                </label>
                                <input type="number" class="form-control" id="sort_order" name="sort_order" 
                                       value="<?php echo $edit_section ? $edit_section['sort_order'] : '0'; ?>" 
                                       min="0">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                   <?php echo (!$edit_section || $edit_section['is_active']) ? 'checked' : ''; ?>>
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

<?php if ($edit_section): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var sectionModal = new bootstrap.Modal(document.getElementById('sectionModal'));
    sectionModal.show();
});
</script>
<?php endif; ?>

<?php include 'footer.php'; ?>