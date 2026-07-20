<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card">
    <h1>Dashboard opérateur</h1>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:16px;margin-bottom:16px;">
    <div class="card">
        <p class="muted" style="margin:0;">Frais perçus (retraits &amp; transferts)</p>
        <p style="font-size:24px;font-weight:700;color:var(--accent);margin:8px 0 0;">
            <?= esc(number_format($totalFrais ?? 0, 2)) ?> $
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
    <h2>Comptes clients</h2>
    <p class="muted">Gérer les comptes clients et consulter leur situation.</p>
    <a class="btn" href="<?= site_url('clients') ?>">Voir la liste des clients</a>
</div>

<?= $this->endSection() ?>
