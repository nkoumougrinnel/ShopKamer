<?php
require_once __DIR__ . '/config/db.php';

$errors  = [];
$success = false;
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']     ?? '');
    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password']      ?? '';
    $confirm  = $_POST['confirm']       ?? '';
 
    // ── Validation ──────────────────────────────────────────
    if ($name === '') {
        $errors[] = 'Le nom complet est requis.';
    } elseif (strlen($name) < 2) {
        $errors[] = 'Le nom doit contenir au moins 2 caractères.';
    }
 
    if ($email === '') {
        $errors[] = 'L\'adresse e-mail est requise.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'L\'adresse e-mail n\'est pas valide.';
    }
 
    if ($password === '') {
        $errors[] = 'Le mot de passe est requis.';
    } elseif (strlen($password) < 8) {
        $errors[] = 'Le mot de passe doit contenir au moins 8 caractères.';
    } elseif (!preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $errors[] = 'Le mot de passe doit contenir au moins une majuscule et un chiffre.';
    }
 
    if ($confirm !== $password) {
        $errors[] = 'Les mots de passe ne correspondent pas.';
    }
 
    // ── Vérification doublon e-mail ──────────────────────────
    if (empty($errors)) {
        $mysqli = getDbConnection();
        $stmt = $mysqli->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errors[] = 'Un compte avec cet e-mail existe déjà.';
        }

        $stmt->close();
    }
 
    // ── Enregistrement ───────────────────────────────────────
    if (empty($errors)) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $mysqli->prepare(
            'INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)'
        );
        $role = 'user';
        $stmt->bind_param('ssss', $name, $email, $passwordHash, $role);
        $stmt->execute();
        $stmt->close();
 
        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopKamer - Inscription</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
 
    <main>
        <section class="login-container">
            <h2>Créer un compte</h2>

            <?php if ($success): ?>
                <!-- ── Message de succès ─────────────────────── -->
                <div class="auth-alert auth-alert--success">
                    <strong>Compte créé avec succès !</strong><br>
                    Vous pouvez maintenant <a href="login.php">vous connecter</a>.
                </div>
 
            <?php else: ?>
                <!-- ── Erreurs de validation ─────────────────── -->
                <?php if (!empty($errors)): ?>
                    <div class="auth-alert auth-alert--error">
                        <ul>
                            <?php foreach ($errors as $err): ?>
                                <li><?= htmlspecialchars($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
 
                <!-- ── Formulaire ────────────────────────────── -->
                <form action="" method="post" class="login-form" novalidate>
 
                    <div class="form-group">
                        <label for="name">Nom complet</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            placeholder="Alice Dupont"
                            value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                            required>
                    </div>
 
                    <div class="form-group">
                        <label for="email">Adresse e-mail</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="alice@exemple.com"
                            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                            required>
                    </div>
 
                    <div class="form-group">
                        <label for="password">Mot de passe</label>
                        <div class="input-password">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="Min. 8 caractères, 1 majuscule, 1 chiffre"
                                required>
                        </div>
                        <div class="password-strength" id="strength-bar">
                            <span></span><span></span><span></span><span></span>
                        </div>
                        <small class="password-hint" id="strength-label"></small>
                    </div>
 
                    <div class="form-group">
                        <label for="confirm">Confirmer le mot de passe</label>
                        <div class="input-password">
                            <input
                                type="password"
                                id="confirm"
                                name="confirm"
                                placeholder="Répétez votre mot de passe"
                                required>
                        </div>
                    </div>
 
                    <button type="submit">Créer mon compte</button>
 
                    <p class="extra-links">
                        Déjà inscrit ? <a href="login.php">Se connecter</a>
                    </p>
                </form>
            <?php endif; ?>
        </section>
    </main>
 
    <?php include 'includes/footer.php'; ?>
 
    <script>
        // ── Indicateur de force du mot de passe ────────────
        const pwdInput    = document.getElementById('password');
        const bars        = document.querySelectorAll('#strength-bar span');
        const strengthLbl = document.getElementById('strength-label');
 
        const levels = [
            { label: '',          color: '' },
            { label: 'Faible',    color: '#e05252' },
            { label: 'Moyen',     color: '#e0a852' },
            { label: 'Bon',       color: '#7ec8e3' },
            { label: 'Fort',      color: '#52c97e' },
        ];
 
        function scorePassword(pwd) {
            let score = 0;
            if (pwd.length >= 8)                         score++;
            if (/[A-Z]/.test(pwd))                       score++;
            if (/[0-9]/.test(pwd))                       score++;
            if (/[^A-Za-z0-9]/.test(pwd))               score++;
            return score;   // 0 – 4
        }
 
        pwdInput.addEventListener('input', () => {
            const score = pwdInput.value.length ? scorePassword(pwdInput.value) : 0;
            bars.forEach((bar, i) => {
                bar.style.background = i < score ? levels[score].color : '#2a2a2a';
            });
            strengthLbl.textContent   = levels[score].label;
            strengthLbl.style.color   = levels[score].color;
        });
    </script>
</body>
</html>