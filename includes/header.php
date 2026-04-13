<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userName = $_SESSION['user_name'] ?? null;
?>
<header>
    <div class="logo">Shop<span>Kamer</span></div>
    <nav>
        <a href="index.php">Accueil</a>
        <a href="catalogue.php">Boutique</a>
        <a href="panier.php">Panier</a>
        <?php if ($userName): ?>
            <a href="logout.php">Déconnexion</a>
        <?php else: ?>
            <a href="login.php">Connexion</a>
        <?php endif; ?>
    </nav>
</header>