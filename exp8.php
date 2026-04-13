<?php
declare(strict_types=1);

$errors = [];
$success = false;
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$department = trim($_POST['department'] ?? '');
$year = trim($_POST['year'] ?? '');
$phone = trim($_POST['phone'] ?? '');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    if ($name === '') {
        $errors[] = 'Name is required.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Enter a valid email address.';
    }
    if ($department === '') {
        $errors[] = 'Department is required.';
    }
    if (!in_array($year, ['1st Year', '2nd Year', '3rd Year', '4th Year'], true)) {
        $errors[] = 'Select a valid year.';
    }
    if (!preg_match('/^[0-9]{10}$/', $phone)) {
        $errors[] = 'Phone number must be exactly 10 digits.';
    }

    if (!$errors) {
        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Experiment 8</title>
    <link rel="stylesheet" href="lab-style.css">
</head>
<body>
    <header class="lab-header">
        <div class="wrap nav-row">
            <div>
                <p class="eyebrow">Experiment 8</p>
                <h1>PHP Form Validation</h1>
            </div>
            <nav class="nav-links">
                <a href="index.html">Home</a>
                <a href="exp7.html">Exp 7</a>
                <a href="exp9.php">Exp 9</a>
                <a href="exp10.php">Exp 10</a>
            </nav>
        </div>
    </header>

    <main class="wrap section-space">
        <section class="panel">
            <p class="eyebrow">Server-side processing</p>
            <h2 class="page-title">Student registration form with PHP validation.</h2>
            <p class="lead">This experiment checks required fields, email format, year selection, and mobile number format on the server side.</p>

            <?php if ($success): ?>
                <div class="flash-box flash-success">
                    <strong>Registration submitted successfully.</strong>
                    <p>Name: <?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?></p>
                    <p>Email: <?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?></p>
                    <p>Department: <?= htmlspecialchars($department, ENT_QUOTES, 'UTF-8') ?></p>
                    <p>Year: <?= htmlspecialchars($year, ENT_QUOTES, 'UTF-8') ?></p>
                    <p>Phone: <?= htmlspecialchars($phone, ENT_QUOTES, 'UTF-8') ?></p>
                </div>
            <?php endif; ?>

            <?php if ($errors): ?>
                <div class="flash-box flash-error">
                    <strong>Please fix these errors:</strong>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="post" class="form-grid">
                <label>Full Name
                    <input type="text" name="name" value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>" required>
                </label>
                <label>Email Address
                    <input type="email" name="email" value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>" required>
                </label>
                <label>Department
                    <input type="text" name="department" value="<?= htmlspecialchars($department, ENT_QUOTES, 'UTF-8') ?>" required>
                </label>
                <label>Year
                    <select name="year" required>
                        <option value="">Select year</option>
                        <?php foreach (['1st Year', '2nd Year', '3rd Year', '4th Year'] as $option): ?>
                            <option value="<?= $option ?>" <?= $year === $option ? 'selected' : '' ?>><?= $option ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label style="grid-column: 1 / -1;">Phone Number
                    <input type="text" name="phone" maxlength="10" value="<?= htmlspecialchars($phone, ENT_QUOTES, 'UTF-8') ?>" required>
                </label>
                <div>
                    <button type="submit">Submit Form</button>
                </div>
            </form>
        </section>
    </main>
</body>
</html>
