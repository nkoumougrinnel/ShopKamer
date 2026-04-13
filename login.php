<?php
session_start();
require_once __DIR__ . '/config/db.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '') {
        $errors[] = 'L\'adresse e-mail est requise.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'L\'adresse e-mail n\'est pas valide.';
    }

    if ($password === '') {
        $errors[] = 'Le mot de passe est requis.';
    }

    if (empty($errors)) {
        $mysqli = getDbConnection();
        $stmt = $mysqli->prepare('SELECT id, name, email, password, role FROM users WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if (!$user) {
            $errors[] = 'Identifiants incorrects.';
        } else {
            $passwordOk = false;

            if (password_verify($password, $user['password'])) {
                $passwordOk = true;
            } elseif ($password === $user['password']) {
                $passwordOk = true;
                $newHash = password_hash($password, PASSWORD_DEFAULT);
                $update = $mysqli->prepare('UPDATE users SET password = ? WHERE id = ?');
                $update->bind_param('si', $newHash, $user['id']);
                $update->execute();
                $update->close();
            }

            if ($passwordOk) {
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];

                header('Location: index.php');
                exit;
            }

            $errors[] = 'Identifiants incorrects.';
        }
    }
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
            <?php if (!empty($errors)): ?>
                <div class="auth-alert auth-alert--error">
                    <ul>
                        <?php foreach ($errors as $err): ?>
                            <li><?= htmlspecialchars($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
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
                    <a href="register.php">Créer un compte</a>
                </p>
            </form>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>