<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card">
    <h1>Dashboard opérateur</h1>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:16px;margin-bottom:16px;">
    <div class="card">
        <p class="muted" style="margin:0;">Frais perçus (opérateur principal)</p>
        <p style="font-size:24px;font-weight:700;color:var(--accent);margin:8px 0 0;">
            <?= esc(number_format($totalFrais ?? 0, 2)) ?> $
        </p>
    </div>
    <div class="card">
        <p class="muted" style="margin:0;">Frais perçus (autres opérateurs)</p>
        <p style="font-size:24px;font-weight:700;color:var(--accent);margin:8px 0 0;">
            <?= esc(number_format($totalFraisAutre ?? 0, 2)) ?> $
        </p>
    </div>
    <div class="card">
        <p class="muted" style="margin:0;">Nombre total de clients</p>
        <p style="font-size:24px;font-weight:700;color:var(--accent);margin:8px 0 0;">
            <?= esc($nbClients ?? 0) ?>
        </p>
    </div>
    <div class="card">
        <p class="muted" style="margin:0;">Montant total détenu</p>
        <p style="font-size:24px;font-weight:700;color:var(--accent);margin:8px 0 0;">
            <?= esc(number_format($totalMontant ?? 0, 2)) ?> $
        </p>
    </div>
</div>

<div class="card">
    <h2>Situation par opérateur</h2>
    <p class="muted">Montants de frais à reverser par opérateur.</p>
    <?php if (!empty($montantsParOperateur)): ?>
    <table style="width:100%;border-collapse:collapse;margin-top:12px;">
        <thead>
            <tr style="border-bottom:2px solid var(--border);">
                <th style="text-align:left;padding:8px;">Opérateur</th>
                <th style="text-align:left;padding:8px;">Préfixe</th>
                <th style="text-align:right;padding:8px;">Montant total</th>
                <th style="text-align:right;padding:8px;">Nb transactions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($montantsParOperateur as $op): ?>
            <tr style="border-bottom:1px solid var(--border);">
                <td style="padding:8px;"><?= esc($op['nom_operateur']) ?></td>
                <td style="padding:8px;"><?= esc($op['prefixe']) ?></td>
                <td style="padding:8px;text-align:right;"><?= esc(number_format($op['montant_total'], 2)) ?> $</td>
                <td style="padding:8px;text-align:right;"><?= esc($op['nombre_transactions']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p class="muted" style="margin-top:12px;">Aucune transaction enregistrée.</p>
    <?php endif; ?>
</div>

<div class="card">
    <h2>Comptes clients</h2>
    <p class="muted">Gérer les comptes clients et consulter leur situation.</p>
    <a class="btn" href="<?= site_url('clients') ?>">Voir la liste des clients</a>
</div>

<?= $this->endSection() ?>
