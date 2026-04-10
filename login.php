<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopKamer - Connexion</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <section class="login-container">
            <h2>Connexion</h2>
            <form action="" method="post" class="login-form">
                <div class="form-group">
                    <label for="email">Adresse e-mail</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit">Se connecter</button>

                <p class="extra-links">
                    <a href="/forgot-password">Mot de passe oublié ?</a><br>
                    <a href="/register">Créer un compte</a>
                </p>
            </form>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>