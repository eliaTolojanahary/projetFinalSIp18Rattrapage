<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card">
    <h1>Dashboard operateur</h1>
    <p style="font-size:18px;">
        Total des frais perçus (retraits &amp; transferts) :
        <strong style="color:var(--accent);font-size:24px;"><?= esc(number_format($totalFrais ?? 0, 2)) ?> $</strong>
    </p>
</div>

<div class="card">
    <h2>Comptes clients</h2>
    <p class="muted">Gerer les comptes clients et consulter leur situation.</p>
    <a class="btn" href="<?= site_url('clients') ?>">Voir la liste des clients</a>
</div>

<?= $this->endSection() ?>
