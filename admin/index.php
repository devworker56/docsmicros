<?php
// admin/index.php - Admin dashboard
require_once 'config.php';

$page_title = $current_language === 'fr' ? 'Tableau de bord' : 'Dashboard';
$current_page = 'dashboard';
?>

<?php include 'header.php'; ?>

<!-- Dashboard Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><?php echo $admin_lang['dashboard']; ?></h1>
    <div>
        <span class="text-muted">
            <i class="fas fa-calendar me-1"></i>
            <?php echo date('F j, Y'); ?>
        </span>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stat-number text-primary"><?php echo $stats['sections']; ?></div>
                    <div class="ms-3">
                        <div class="stat-label"><?php echo $admin_lang['total_sections']; ?></div>
                        <small class="text-muted">
                            <i class="fas fa-folder me-1"></i>
                            <?php echo $current_language === 'fr' ? 'Sections actives' : 'Active sections'; ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stat-number text-primary"><?php echo $stats['chapters']; ?></div>
                    <div class="ms-3">
                        <div class="stat-label"><?php echo $admin_lang['total_chapters']; ?></div>
                        <small class="text-muted">
                            <i class="fas fa-book me-1"></i>
                            <?php echo $current_language === 'fr' ? 'Chapitres actifs' : 'Active chapters'; ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stat-number text-primary"><?php echo $stats['pages']; ?></div>
                    <div class="ms-3">
                        <div class="stat-label"><?php echo $admin_lang['total_pages']; ?></div>
                        <small class="text-muted">
                            <i class="fas fa-file me-1"></i>
                            <?php echo $current_language === 'fr' ? 'Pages actives' : 'Active pages'; ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-5">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-bolt me-2"></i>
                    <?php echo $current_language === 'fr' ? 'Actions rapides' : 'Quick Actions'; ?>
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <a href="sections.php?action=create" class="btn btn-outline-primary w-100">
                            <i class="fas fa-plus me-2"></i>
                            <?php echo $current_language === 'fr' ? 'Nouvelle section' : 'New Section'; ?>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="chapters.php?action=create" class="btn btn-outline-primary w-100">
                            <i class="fas fa-plus me-2"></i>
                            <?php echo $current_language === 'fr' ? 'Nouveau chapitre' : 'New Chapter'; ?>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="pages.php?action=create" class="btn btn-outline-primary w-100">
                            <i class="fas fa-plus me-2"></i>
                            <?php echo $current_language === 'fr' ? 'Nouvelle page' : 'New Page'; ?>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="settings.php" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-cog me-2"></i>
                            <?php echo $current_language === 'fr' ? 'Paramètres' : 'Settings'; ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-clock me-2"></i>
                    <?php echo $admin_lang['recent_activity']; ?>
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($stats['recent_activity'])): ?>
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-inbox fa-3x mb-3"></i>
                        <p><?php echo $current_language === 'fr' ? 'Aucune activité récente' : 'No recent activity'; ?></p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th><?php echo $admin_lang['title']; ?></th>
                                    <th><?php echo $current_language === 'fr' ? 'Chapitre' : 'Chapter'; ?></th>
                                    <th><?php echo $admin_lang['updated_at']; ?></th>
                                    <th><?php echo $admin_lang['actions']; ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($stats['recent_activity'] as $activity): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo $current_language === 'fr' ? $activity['title_fr'] : $activity['title_en']; ?></strong>
                                    </td>
                                    <td>
                                        <?php echo $current_language === 'fr' ? $activity['chapter_fr'] : $activity['chapter_en']; ?>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?php echo date('M j, Y g:i A', strtotime($activity['updated_at'])); ?>
                                        </small>
                                    </td>
                                    <td>
                                        <a href="../page.php?slug=<?php 
                                            $stmt = $conn->prepare("SELECT slug FROM pages WHERE title_en = ?");
                                            $stmt->execute([$activity['title_en']]);
                                            $page = $stmt->fetch();
                                            echo $page['slug'];
                                        ?>" class="btn btn-sm btn-outline-primary" target="_blank">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="edit-page.php?id=<?php 
                                            $stmt = $conn->prepare("SELECT id FROM pages WHERE title_en = ?");
                                            $stmt->execute([$activity['title_en']]);
                                            $page = $stmt->fetch();
                                            echo $page['id'];
                                        ?>" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>