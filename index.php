<?php
declare(strict_types=1);

$db = new PDO(
    'mysql:host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_DATABASE') . ';charset=utf8mb4',
    (string) getenv('DB_USERNAME'),
    (string) getenv('DB_PASSWORD'),
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $note = trim($_POST['note'] ?? '');

    if ($note === '' || strlen($note) > 1000) {
        $error = 'Isi catatan wajib diisi dan maksimal 1000 byte.';
    } else {
        $insert = $db->prepare('INSERT INTO notes (content) VALUES (?)');
        $insert->execute([$note]);
        header('Location: /', true, 303);
        exit;
    }
}

$notes = $db->query('SELECT id, content FROM notes ORDER BY id DESC');
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Catatan Praktikum</title>
<style>
    * { box-sizing: border-box; }

    body {
        width: min(640px, calc(100% - 40px));
        margin: clamp(32px, 7vw, 80px) auto;
        background: #f8f7f3;
        color: #252824;
        font: 16px/1.55 system-ui, sans-serif;
    }

    h1 {
        margin: 0 0 28px;
        font: 700 clamp(30px, 5vw, 42px)/1.1 Georgia, serif;
    }

    form {
        padding: 20px;
        border: 1px solid #deded5;
        border-radius: 8px;
        background: #fff;
    }

    label { font-weight: 600; }

    textarea {
        display: block;
        width: 100%;
        min-height: 112px;
        margin: 8px 0 12px;
        padding: 10px 12px;
        border: 1px solid #b8bcb3;
        border-radius: 5px;
        font: inherit;
        resize: vertical;
    }

    form br { display: none; }

    button {
        padding: 9px 14px;
        border: 0;
        border-radius: 5px;
        background: #294b39;
        color: #fff;
        font: inherit;
        cursor: pointer;
    }

    button:hover { background: #1e392a; }

    :focus-visible {
        outline: 2px solid #294b39;
        outline-offset: 3px;
    }

    h2 {
        margin: 32px 0 10px;
        font-size: 18px;
    }

    ul {
        margin: 0;
        padding: 0;
        list-style: none;
    }

    li {
        padding: 12px 0;
        border-top: 1px solid #deded5;
        white-space: pre-line;
        overflow-wrap: anywhere;
    }
</style>
</head>
<body>
    <h1>Catatan Praktikum</h1>
    <form method="post">
        <label for="note">Catatan baru</label><br>
        <textarea id="note" name="note" rows="4" maxlength="1000" required></textarea><br>
        <button type="submit">Simpan catatan</button>
    </form>
    <?php if ($error !== ''): ?>
        <p><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <h2>Daftar catatan</h2>
    <ul>
        <?php foreach ($notes as $item): ?>
            <li>#<?= (int) $item['id'] ?> —
                <?= htmlspecialchars($item['content'], ENT_QUOTES, 'UTF-8') ?>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>