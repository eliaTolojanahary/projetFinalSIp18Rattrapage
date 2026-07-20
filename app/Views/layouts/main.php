<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Gestion de bibliotheque') ?></title>
    <style>
        :root {
            --bg: #f7f7f3;
            --surface: #ffffff;
            --text: #1a1a1a;
            --accent: #0c5c56;
            --danger: #8f1d14;
            --ok: #1c7c3a;
            --muted: #666;
            --border: #ddd;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text);
            background: linear-gradient(180deg, #f7f7f3 0%, #eceee7 100%);
        }
        .nav {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 12px 20px;
            display: flex;
            gap: 14px;
            align-items: center;
        }
        .brand {
            font-weight: 700;
            margin-right: auto;
            color: var(--accent);
        }
        .nav a {
            text-decoration: none;
            color: var(--text);
            font-weight: 600;
        }
        .container {
            max-width: 1100px;
            margin: 24px auto;
            padding: 0 16px;
        }
        .flash {
            padding: 12px 14px;
            border-radius: 8px;
            margin-bottom: 16px;
            border: 1px solid;
        }
        .flash-success {
            background: #e9f7ee;
            color: #0f5b29;
            border-color: #b8e0c5;
        }
        .flash-error {
            background: #fdeceb;
            color: #7b1c18;
            border-color: #efc2bf;
        }
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 16px;
            margin-bottom: 16px;
        }
        .btn {
            border: 1px solid transparent;
            background: var(--accent);
            color: #fff;
            border-radius: 8px;
            padding: 8px 12px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-weight: 600;
        }
        .btn-secondary {
            background: #3f4a56;
        }
        .btn-danger {
            background: var(--danger);
        }
        .btn-outline {
            color: var(--accent);
            background: #fff;
            border-color: var(--accent);
        }
        .status {
            padding: 4px 8px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            display: inline-block;
        }
        .status-disponible {
            background: #e9f7ee;
            color: var(--ok);
        }
        .status-prete {
            background: #fdeceb;
            color: var(--danger);
        }
        .error-text {
            color: var(--danger);
            font-size: 13px;
            margin-top: 4px;
        }
        input, select, textarea {
            width: 100%;
            border: 1px solid #c8c8c8;
            border-radius: 8px;
            padding: 10px;
            font: inherit;
        }
        label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
        }
        .field { margin-bottom: 12px; }
        .muted { color: var(--muted); }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }
        th, td {
            border-bottom: 1px solid #ececec;
            text-align: left;
            padding: 10px;
            vertical-align: top;
        }
        @media (max-width: 800px) {
            .table-wrap {
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <nav class="nav">
        <div class="brand">Bibliotheque</div>
        <a href="<?= site_url('/') ?>">Catalogue</a>
        <a href="<?= site_url('livres/ajouter') ?>">Ajouter un livre</a>
    </nav>

    <main class="container">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="flash flash-success"><?= esc(session()->getFlashdata('success')) ?></div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="flash flash-error"><?= esc(session()->getFlashdata('error')) ?></div>
        <?php endif; ?>

        <?= $this->renderSection('content') ?>
    </main>
</body>
</html>
