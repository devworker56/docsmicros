<?php
// footer.php - Bilingual documentation footer
?>
    <footer class="footer">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <h5 class="mb-3">
                        <span class="icon-container">
                            <i class="fas fa-shield shield-icon"></i>
                            <i class="fas fa-hand-holding-heart heart-icon"></i>
                        </span>
                        AidVeritas Docs
                    </h5>
                    <p class="text-light">
                        <?php echo $current_language === 'fr' 
                            ? 'Documentation complète pour l\'écosystème AidVeritas - Dons vérifiables et attribués'
                            : 'Complete documentation for the AidVeritas ecosystem - Verifiable and attributed donations'; ?>
                    </p>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-light"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-light"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-light"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4">
                    <h5 class="mb-3"><?php echo $current_language === 'fr' ? 'Navigation' : 'Navigation'; ?></h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="index.php" class="text-light"><?php echo $lang['home']; ?></a></li>
                        <li class="mb-2"><a href="section.php?id=1" class="text-light"><?php echo $lang['for_donors']; ?></a></li>
                        <li class="mb-2"><a href="section.php?id=2" class="text-light"><?php echo $lang['for_organizations']; ?></a></li>
                        <li class="mb-2"><a href="section.php?id=3" class="text-light"><?php echo $lang['for_businesses']; ?></a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-4">
                    <h5 class="mb-3"><?php echo $current_language === 'fr' ? 'Liens utiles' : 'Useful Links'; ?></h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="https://aidveritas.com" class="text-light"><?php echo $current_language === 'fr' ? 'Site principal' : 'Main Website'; ?></a></li>
                        <li class="mb-2"><a href="https://aidveritas.com/resources.php" class="text-light"><?php echo $current_language === 'fr' ? 'Ressources' : 'Resources'; ?></a></li>
                        <li class="mb-2"><a href="https://aidveritas.com/contact.php" class="text-light"><?php echo $lang['contact']; ?></a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-4">
                    <h5 class="mb-3"><?php echo $lang['contact']; ?></h5>
                    <ul class="list-unstyled text-light">
                        <li class="mb-2"><i class="fas fa-envelope me-2"></i> docs@aidveritas.com</li>
                        <li class="mb-2"><i class="fas fa-phone me-2"></i> +1 (514) 123-4567</li>
                        <li class="mb-2"><i class="fas fa-map-marker-alt me-2"></i> Montréal, QC</li>
                    </ul>
                </div>
            </div>
            <hr class="my-4 border-light">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="text-light mb-0">&copy; <?php echo date('Y'); ?> AidVeritas. <?php echo $current_language === 'fr' ? 'Tous droits réservés.' : 'All rights reserved.'; ?></p>
                </div>
                <div class="col-md-6 text-md-end">
                    <span class="badge bg-light text-dark">
                        <i class="fas fa-lock me-1"></i>
                        <?php echo $current_language === 'fr' ? 'Sécurisé et vérifié' : 'Secure and Verified'; ?>
                    </span>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>