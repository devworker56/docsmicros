<?php
// header.php - Bilingual documentation header
?>
<!DOCTYPE html>
<html lang="<?php echo $current_language; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title . ' - ' . SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="/css/style.css" rel="stylesheet">
</head>
<body>
    <header class="fixed-top bg-white shadow-sm">
        <nav class="navbar navbar-expand-lg navbar-light py-2">
            <div class="container">
                <!-- Logo -->
                <a class="navbar-brand fw-bold text-primary" href="index.php">
                    <span class="icon-container">
                        <i class="fas fa-shield shield-icon"></i>
                        <i class="fas fa-hand-holding-heart heart-icon"></i>
                    </span>
                    AidVeritas <?php echo $current_language === 'fr' ? 'Docs' : 'Docs'; ?>
                </a>

                <!-- Mobile Toggle -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Navigation Menu -->
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page === 'home' ? 'active' : ''; ?>" href="index.php">
                                <i class="fas fa-home me-1"></i>
                                <?php echo $lang['home']; ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page === 'donors' ? 'active' : ''; ?>" href="section.php?id=1">
                                <i class="fas fa-mobile-alt me-1"></i>
                                <?php echo $lang['for_donors']; ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page === 'organizations' ? 'active' : ''; ?>" href="section.php?id=2">
                                <i class="fas fa-heart me-1"></i>
                                <?php echo $lang['for_organizations']; ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page === 'businesses' ? 'active' : ''; ?>" href="section.php?id=3">
                                <i class="fas fa-store me-1"></i>
                                <?php echo $lang['for_businesses']; ?>
                            </a>
                        </li>
                    </ul>

                    <!-- Right side items -->
                    <div class="d-flex align-items-center">
                        <!-- Search -->
                        <form class="d-flex me-3" action="search.php" method="GET">
                            <div class="input-group">
                                <input type="text" class="form-control" name="q" 
                                       placeholder="<?php echo $lang['search_placeholder']; ?>">
                                <button class="btn btn-outline-primary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>

                        <!-- Language Switcher -->
                        <div class="dropdown me-3">
                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-language me-1"></i>
                                <?php echo strtoupper($current_language); ?>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="?lang=fr&redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>">Français (FR)</a></li>
                                <li><a class="dropdown-item" href="?lang=en&redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>">English (EN)</a></li>
                            </ul>
                        </div>

                        <!-- Admin Access -->
                        <?php if (isset($_SESSION['admin_logged_in'])): ?>
                        <div class="dropdown">
                            <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-cog me-1"></i>
                                <?php echo $lang['admin']; ?>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="admin/"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="admin/logout.php"><i class="fas fa-sign-out-alt me-2"></i><?php echo $current_language === 'fr' ? 'Déconnexion' : 'Logout'; ?></a></li>
                            </ul>
                        </div>
                        <?php else: ?>
                        <a href="admin/login.php" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-lock me-1"></i>
                            Admin
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <!-- Spacer for fixed header -->
    <div style="height: 80px;"></div>