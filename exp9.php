<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

$message = '';
$error = '';
$editId = (int) ($_GET['edit'] ?? 0);
$title = '';
$category = '';
$price = '';

try {
    $pdo = db();

    if (isset($_GET['delete'])) {
        $deleteId = (int) $_GET['delete'];
        $stmt = $pdo->prepare('DELETE FROM exp9_products WHERE id = ?');
        $stmt->execute([$deleteId]);
        $message = 'Record deleted successfully.';
    }

    if ($editId > 0) {
        $stmt = $pdo->prepare('SELECT * FROM exp9_products WHERE id = ?');
        $stmt->execute([$editId]);
        $record = $stmt->fetch();

        if ($record) {
            $title = $record['title'];
            $category = $record['category'];
            $price = (string) $record['price'];
        } else {
            $editId = 0;
        }
    }

    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
        $action = $_POST['action'] ?? 'add';
        $id = (int) ($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $price = trim($_POST['price'] ?? '');

        if ($title === '' || $category === '' || !is_numeric($price) || (float) $price <= 0) {
            $error = 'Please enter a valid title, category, and price.';
        } elseif ($action === 'edit') {
            $stmt = $pdo->prepare('UPDATE exp9_products SET title = ?, category = ?, price = ? WHERE id = ?');
            $stmt->execute([$title, $category, $price, $id]);
            $message = 'Record updated successfully.';
            $title = $category = $price = '';
            $editId = 0;
        } else {
            $stmt = $pdo->prepare('INSERT INTO exp9_products (title, category, price) VALUES (?, ?, ?)');
            $stmt->execute([$title, $category, $price]);
            $message = 'Record added successfully.';
            $title = $category = $price = '';
        }
    }

    $records = $pdo->query('SELECT * FROM exp9_products ORDER BY id DESC')->fetchAll();
} catch (Throwable $exception) {
    $records = [];
    $error = 'Database connection failed. Import database.sql in phpMyAdmin first.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Experiment 9</title>
    <link rel="stylesheet" href="lab-style.css">
</head>
<body>
    <header class="lab-header">
        <div class="wrap nav-row">
            <div>
                <p class="eyebrow">Experiment 9</p>
                <h1>PHP CRUD Operations with MySQL</h1>
            </div>
            <nav class="nav-links">
                <a href="index.html">Home</a>
                <a href="exp7.html">Exp 7</a>
                <a href="exp8.php">Exp 8</a>
                <a href="exp10.php">Exp 10</a>
            </nav>
        </div>
    </header>

    <main class="wrap section-space">
        <section class="panel">
            <p class="eyebrow">Create, Read, Update, Delete</p>
            <h2 class="page-title">Manage product records stored in MySQL.</h2>
            <p class="lead">This experiment stores every add, edit, and delete action inside the <strong>exp9_products</strong> database table.</p>

            <?php if ($message !== ''): ?>
                <div class="flash-box flash-success"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>

            <?php if ($error !== ''): ?>
                <div class="flash-box flash-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>

            <form method="post" class="form-grid">
                <input type="hidden" name="action" value="<?= $editId > 0 ? 'edit' : 'add' ?>">
                <input type="hidden" name="id" value="<?= $editId ?>">
                <label>Product Title
                    <input type="text" name="title" value="<?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>" required>
                </label>
                <label>Category
                    <input type="text" name="category" value="<?= htmlspecialchars($category, ENT_QUOTES, 'UTF-8') ?>" required>
                </label>
                <label style="grid-column: 1 / -1;">Price
                    <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($price, ENT_QUOTES, 'UTF-8') ?>" required>
                </label>
                <div class="table-actions">
                    <button type="submit"><?= $editId > 0 ? 'Update Record' : 'Add Record' ?></button>
                    <a class="secondary-btn" href="exp9.php">Clear</a>
                </div>
            </form>

            <table class="record-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $record): ?>
                        <tr>
                            <td><?= (int) $record['id'] ?></td>
                            <td><?= htmlspecialchars($record['title'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($record['category'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td>Rs. <?= htmlspecialchars((string) $record['price'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($record['created_at'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="table-actions">
                                <a class="secondary-btn" href="exp9.php?edit=<?= (int) $record['id'] ?>">Edit</a>
                                <a class="button-link primary danger-btn" href="exp9.php?delete=<?= (int) $record['id'] ?>" onclick="return confirm('Delete this record?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>
