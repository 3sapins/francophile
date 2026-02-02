    </main>
    
    <footer class="main-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-brand">
                    <span class="logo-icon">📚</span>
                    <span>Francophile.ch</span>
                </div>
                <p class="footer-tagline">Plateforme d'exercices de français pour le cycle 3</p>
                <p class="footer-copyright">&copy; <?= date('Y') ?> Francophile - Tous droits réservés</p>
            </div>
            <div class="footer-links">
                <a href="/mentions-legales.php">Mentions légales</a>
                <a href="/contact.php">Contact</a>
            </div>
        </div>
    </footer>
    
    <script src="/assets/js/app.js"></script>
    <?php if (isset($pageScripts)): ?>
        <?php foreach ($pageScripts as $script): ?>
            <script src="<?= htmlspecialchars($script) ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
