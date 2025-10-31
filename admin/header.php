<?php
// admin/header.php - Admin header
?>
<!DOCTYPE html>
<html lang="<?php echo $current_language; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - <?php echo $page_title . ' - ' . SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
    <style>
        .admin-sidebar {
            width: 250px;
            background: var(--gray-900);
            color: white;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            padding-top: 80px;
            transition: all 0.3s;
        }
        .admin-main {
            margin-left: 250px;
            padding: 20px;
            min-height: 100vh;
            background: var(--gray-50);
        }
        .admin-navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            height: 80px;
        }
        .sidebar-menu .nav-link {
            color: var(--gray-300);
            padding: 0.75rem 1.5rem;
            border-left: 3px solid transparent;
            transition: all 0.2s;
        }
        .sidebar-menu .nav-link:hover,
        .sidebar-menu .nav-link.active {
            color: white;
            background: var(--gray-800);
            border-left-color: var(--primary-blue);
        }
        @media (max-width: 768px) {
            .admin-sidebar {
                margin-left: -250px;
            }
            .admin-sidebar.show {
                margin-left: 0;
            }
            .admin-main {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Admin Navbar -->
    <nav class="admin-navbar">
        <div class="container-fluid">
            <div class="d-flex align-items-center justify-content-between py-3">
                <div class="d-flex align-items-center">
                    <button class="btn btn-outline-secondary me-3 d-lg-none" id="sidebarToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <a class="navbar-brand fw-bold" href="../index.php">
                        <span class="icon-container">
                            <i class="fas fa-shield shield-icon"></i>
                            <i class="fas fa-hand-holding-heart heart-icon"></i>
                        </span>
                        AidVeritas <?php echo $current_language === 'fr' ? 'Admin' : 'Admin'; ?>
                    </a>
                </div>
                <div class="d-flex align-items-center">
                    <span class="text-muted me-3 d-none d-sm-block">
                        <i class="fas fa-user me-1"></i>
                        <?php echo $_SESSION['admin_username']; ?>
                    </span>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-cog"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="../index.php">
                                <i class="fas fa-external-link-alt me-2"></i>
                                <?php echo $current_language === 'fr' ? 'Voir le site' : 'View Site'; ?>
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="?lang=fr">
                                <i class="fas fa-language me-2"></i>
                                Français
                            </a></li>
                            <li><a class="dropdown-item" href="?lang=en">
                                <i class="fas fa-language me-2"></i>
                                English
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i>
                                <?php echo $admin_lang['logout']; ?>
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Admin Sidebar -->
    <aside class="admin-sidebar" id="adminSidebar">
        <nav class="sidebar-menu">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'dashboard' ? 'active' : ''; ?>" href="index.php">
                        <i class="fas fa-tachometer-alt me-2"></i>
                        <?php echo $admin_lang['dashboard']; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'sections' ? 'active' : ''; ?>" href="sections.php">
                        <i class="fas fa-folder me-2"></i>
                        <?php echo $admin_lang['sections']; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'chapters' ? 'active' : ''; ?>" href="chapters.php">
                        <i class="fas fa-book me-2"></i>
                        <?php echo $admin_lang['chapters']; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'pages' ? 'active' : ''; ?>" href="pages.php">
                        <i class="fas fa-file me-2"></i>
                        <?php echo $admin_lang['pages']; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'users' ? 'active' : ''; ?>" href="users.php">
                        <i class="fas fa-users me-2"></i>
                        <?php echo $admin_lang['users']; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'settings' ? 'active' : ''; ?>" href="settings.php">
                        <i class="fas fa-cog me-2"></i>
                        <?php echo $admin_lang['settings']; ?>
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="admin-main" id="adminMain">
        <div class="container-fluid">