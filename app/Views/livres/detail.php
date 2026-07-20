<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="card">
    <?php $resumeTexte = (string) ($livre['resume'] ?? 'Aucun resume.'); ?>
    <h1><?= esc($livre['titre']) ?></h1>
    <p><strong>Auteur:</strong> <?= esc($livre['auteur']) ?></p>
    <p><strong>ISBN:</strong> <?= esc($livre['isbn']) ?></p>
    <p><strong>Annee de publication:</strong> <?= esc($livre['annee_publication']) ?></p>
    <p><strong>Categorie:</strong> <?= esc($livre['categorie'] ?? '-') ?></p>
    <p>
        <strong>Statut:</strong>
        <?php $estDisponible = ($livre['statut'] ?? '') === 'disponible'; ?>
        <span class="status <?= $estDisponible ? 'status-disponible' : 'status-prete' ?>">
            <?= $estDisponible ? 'Disponible' : 'Prete' ?>
        </span>
    </p>
    <p><strong>Resume:</strong><br><?= nl2br(esc($resumeTexte)) ?></p>

    <?php if (! empty($livre['couverture_fichier'])): ?>
        <p><strong>Couverture:</strong></p>
        <img src="<?= base_url('uploads/' . $livre['couverture_fichier']) ?>" alt="Couverture de <?= esc($livre['titre']) ?>" style="max-width:220px;border-radius:8px;border:1px solid #ddd;">
    <?php endif; ?>
</div>

<div class="card">
    <h2>Dernier emprunt</h2>
    <?php if (! empty($dernierEmprunt)): ?>
        <p><strong>Emprunteur:</strong> <?= esc($dernierEmprunt['nom_emprunteur']) ?></p>
        <p><strong>Date d'emprunt:</strong> <?= esc($dernierEmprunt['date_emprunt']) ?></p>
        <p><strong>Date de retour:</strong> <?= esc($dernierEmprunt['date_retour'] ?? 'Non retourne') ?></p>
    <?php else: ?>
        <p class="muted">Aucun emprunt enregistre pour ce livre.</p>
    <?php endif; ?>
</div>

<a class="btn" href="<?= site_url('livres') ?>">Retour au catalogue</a>
<?= $this->endSection() ?>
