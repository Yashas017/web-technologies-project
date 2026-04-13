<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/db.php';

$message = '';
$error = '';
$rememberedEmail = $_COOKIE['remember_exp10_email'] ?? '';
$registerName = '';
$registerEmail = '';
$registerDepartment = '';
$loginEmail = $rememberedEmail;

if (isset($_GET['logout'])) {
    unset($_SESSION['exp10_user']);
    header('Location: exp10.php');
    exit;
}

try {
    $pdo = db();

    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
        $formType = $_POST['form_type'] ?? '';

        if ($formType === 'register') {
            $registerName = trim($_POST['name'] ?? '');
            $registerEmail = trim($_POST['email'] ?? '');
            $registerDepartment = trim($_POST['department'] ?? '');
            $password = $_POST['password'] ?? '';

            if ($registerName === '' || $registerDepartment === '') {
                $error = 'Name and department are required.';
            } elseif (!filter_var($registerEmail, FILTER_VALIDATE_EMAIL)) {
                $error = 'Enter a valid registration email.';
            } elseif (strlen($password) < 5) {
                $error = 'Password must be at least 5 characters.';
            } else {
                $checkStmt = $pdo->prepare('SELECT id FROM exp10_users WHERE email = ?');
                $checkStmt->execute([$registerEmail]);

                if ($checkStmt->fetch()) {
                    $error = 'An account with this email already exists.';
                } else {
                    $stmt = $pdo->prepare('INSERT INTO exp10_users (name, email, department, password) VALUES (?, ?, ?, ?)');
                    $stmt->execute([$registerName, $registerEmail, $registerDepartment, password_hash($password, PASSWORD_DEFAULT)]);
                    $message = 'Registration saved in database successfully. You can log in now.';
                    $registerName = $registerEmail = $registerDepartment = '';
                }
            }
        }

        if ($formType === 'login') {
            $loginEmail = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $remember = isset($_POST['remember']);

            $stmt = $pdo->prepare('SELECT * FROM exp10_users WHERE email = ?');
            $stmt->execute([$loginEmail]);
            $user = $stmt->fetch();

            if (!$user || !password_verify($password, $user['password'])) {
                $error = 'Invalid login credentials.';
            } else {
                $_SESSION['exp10_user'] = [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'department' => $user['department']
                ];

                if ($remember) {
                    setcookie('remember_exp10_email', $loginEmail, time() + (86400 * 30));
                } else {
                    setcookie('remember_exp10_email', '', time() - 3600);
                }

                header('Location: exp10.php');
                exit;
            }
        }
    }

    $users = $pdo->query('SELECT id, name, email, department, created_at FROM exp10_users ORDER BY id DESC')->fetchAll();
} catch (Throwable $exception) {
    $users = [];
    $error = 'Database connection failed. Import database.sql in phpMyAdmin first.';
}

$loggedIn = isset($_SESSION['exp10_user']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Experiment 10</title>
    <link rel="stylesheet" href="lab-style.css">
</head>
<body>
    <main class="wrap section-space">
        <?php if ($loggedIn): ?>
            <section class="panel">
                <p class="eyebrow">Experiment 10</p>
                <h2 class="page-title">Session and cookie dashboard with MySQL users.</h2>
                <p class="lead">The logged-in student is loaded from the database, the login session stays active, and the remembered email is stored in a cookie.</p>

                <div class="badge-row">
                    <span class="badge-pill">Session Active</span>
                    <span class="badge-pill">Database Connected</span>
                    <span class="badge-pill">Cookie Remember Email</span>
                </div>

                <div class="panel muted-card">
                    <p><strong>Name:</strong> <?= htmlspecialchars($_SESSION['exp10_user']['name'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($_SESSION['exp10_user']['email'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p><strong>Department:</strong> <?= htmlspecialchars($_SESSION['exp10_user']['department'], ENT_QUOTES, 'UTF-8') ?></p>
                </div>

                <div class="table-actions">
                    <a class="primary-btn" href="index.html">Back to Home</a>
                    <a class="secondary-btn" href="exp10.php?logout=1">Logout</a>
                </div>
            </section>
        <?php else: ?>
            <section class="panel">
                <p class="eyebrow">Experiment 10</p>
                <h2 class="page-title">Registration, login, sessions and cookies.</h2>
                <p class="lead">This experiment stores all user details in the <strong>exp10_users</strong> MySQL table and lets you log in using those saved accounts.</p>

                <?php if ($message !== ''): ?>
                    <div class="flash-box flash-success"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>

                <?php if ($error !== ''): ?>
                    <div class="flash-box flash-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>

                <div class="form-grid">
                    <form method="post" class="panel muted-card">
                        <input type="hidden" name="form_type" value="register">
                        <h3>Create Account</h3>
                        <label>Name
                            <input type="text" name="name" value="<?= htmlspecialchars($registerName, ENT_QUOTES, 'UTF-8') ?>" required>
                        </label>
                        <label>Email
                            <input type="email" name="email" value="<?= htmlspecialchars($registerEmail, ENT_QUOTES, 'UTF-8') ?>" required>
                        </label>
                        <label>Department
                            <input type="text" name="department" value="<?= htmlspecialchars($registerDepartment, ENT_QUOTES, 'UTF-8') ?>" required>
                        </label>
                        <label>Password
                            <input type="password" name="password" required>
                        </label>
                        <button type="submit">Register User</button>
                    </form>

                    <form method="post" class="panel">
                        <input type="hidden" name="form_type" value="login">
                        <h3>Login</h3>
                        <label>Email
                            <input type="email" name="email" value="<?= htmlspecialchars($loginEmail, ENT_QUOTES, 'UTF-8') ?>" required>
                        </label>
                        <label>Password
                            <input type="password" name="password" required>
                        </label>
                        <label>
                            <input type="checkbox" name="remember" value="1"> Remember my email
                        </label>
                        <button type="submit">Login</button>
                        <div class="summary-box">Demo login: <strong>student@demo.com</strong> / <strong>12345</strong></div>
                    </form>
                </div>

                <section class="panel">
                    <h3>Stored User Details</h3>
                    <table class="record-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Department</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= (int) $user['id'] ?></td>
                                    <td><?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($user['department'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($user['created_at'], ENT_QUOTES, 'UTF-8') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </section>
            </section>
        <?php endif; ?>
    </main>
</body>
</html>
