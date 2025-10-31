<?php
// admin/login.php - Admin login page
require_once '../includes/config.php';
require_once '../includes/database.php';

// Redirect if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error = $current_language === 'fr' 
            ? 'Veuillez remplir tous les champs.'
            : 'Please fill in all fields.';
    } else {
        $db = new Database();
        $conn = $db->getConnection();
        
        $stmt = $conn->prepare("SELECT * FROM admin_users WHERE username = ? AND is_active = TRUE");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $user['username'];
            $_SESSION['admin_user_id'] = $user['id'];
            $_SESSION['admin_role'] = $user['role'];
            
            header('Location: index.php');
            exit;
        } else {
            $error = $current_language === 'fr'
                ? 'Nom d\'utilisateur ou mot de passe incorrect.'
                : 'Invalid username or password.';
        }
    }
}

$page_title = $current_language === 'fr' ? 'Connexion Admin' : 'Admin Login';
?>

<!DOCTYPE html>
<html lang="<?php echo $current_language; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title . ' - ' . SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
</head>
<body class="auth-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="auth-card">
                    <div class="auth-header">
                        <h3 class="text-white mb-0">
                            <span class="icon-container">
                                <i class="fas fa-shield shield-icon"></i>
                                <i class="fas fa-hand-holding-heart heart-icon"></i>
                            </span>
                            AidVeritas Admin
                        </h3>
                    </div>
                    <div class="card-body p-4">
                        <h4 class="card-title text-center mb-4">
                            <?php echo $current_language === 'fr' ? 'Connexion' : 'Login'; ?>
                        </h4>
                        
                        <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="username" class="form-label">
                                    <i class="fas fa-user me-2"></i>
                                    <?php echo $current_language === 'fr' ? 'Nom d\'utilisateur' : 'Username'; ?>
                                </label>
                                <input type="text" class="form-control" id="username" name="username" 
                                       value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" 
                                       required>
                            </div>
                            
                            <div class="mb-4">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-2"></i>
                                    <?php echo $current_language === 'fr' ? 'Mot de passe' : 'Password'; ?>
                                </label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 mb-3">
                                <i class="fas fa-sign-in-alt me-2"></i>
                                <?php echo $current_language === 'fr' ? 'Se connecter' : 'Login'; ?>
                            </button>
                            
                            <div class="text-center">
                                <a href="../index.php" class="text-muted text-decoration-none">
                                    <i class="fas fa-arrow-left me-1"></i>
                                    <?php echo $current_language === 'fr' ? 'Retour au site' : 'Back to site'; ?>
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="text-center mt-3">
                    <small class="text-muted">
                        <?php echo $current_language === 'fr' 
                            ? 'Portail de documentation AidVeritas'
                            : 'AidVeritas Documentation Portal'; ?>
                    </small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>