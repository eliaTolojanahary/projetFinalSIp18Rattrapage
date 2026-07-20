<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php
$errors = session('errors') ?? [];
$maxYear = date('Y');
$categories = [
    'Roman' => 'Roman',
    'Science-fiction' => 'Science-fiction',
    'Histoire' => 'Histoire',
    'Informatique' => 'Informatique',
    'Biographie' => 'Biographie',
    'Autre' => 'Autre',
];
?>

<div class="card">
    <h1>Ajouter un livre</h1>
    <form method="post" action="<?= site_url('livres/ajouter') ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="field">
            <label for="titre">Titre</label>
            <input id="titre" name="titre" type="text" value="<?= esc(old('titre')) ?>" required>
            <?php if (isset($errors['titre'])): ?>
                <div class="error-text"><?= esc($errors['titre']) ?></div>
            <?php endif; ?>
        </div>

        <div class="field">
            <label for="auteur">Auteur</label>
            <input id="auteur" name="auteur" type="text" value="<?= esc(old('auteur')) ?>" required>
            <?php if (isset($errors['auteur'])): ?>
                <div class="error-text"><?= esc($errors['auteur']) ?></div>
            <?php endif; ?>
        </div>

        <div class="field">
            <label for="isbn">ISBN</label>
            <input id="isbn" name="isbn" type="text" value="<?= esc(old('isbn')) ?>" required>
            <?php if (isset($errors['isbn'])): ?>
                <div class="error-text"><?= esc($errors['isbn']) ?></div>
            <?php endif; ?>
        </div>

        <div class="field">
            <label for="annee_publication">Annee</label>
            <input id="annee_publication" name="annee_publication" type="number" value="<?= esc(old('annee_publication')) ?>" min="0" max="<?= esc($maxYear) ?>" required>
            <?php if (isset($errors['annee_publication'])): ?>
                <div class="error-text"><?= esc($errors['annee_publication']) ?></div>
            <?php endif; ?>
        </div>

        <div class="field">
            <label for="categorie">Categorie</label>
            <select id="categorie" name="categorie">
                <option value="">Selectionner une categorie</option>
                <?php foreach ($categories as $value => $label): ?>
                    <option value="<?= esc($value) ?>" <?= old('categorie') === $value ? 'selected' : '' ?>><?= esc($label) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="field">
            <label for="resume">Resume</label>
            <textarea id="resume" name="resume" rows="5"><?= esc(old('resume')) ?></textarea>
        </div>

        <div class="field">
            <label for="couverture">Couverture (jpeg, png, webp - max 2 Mo)</label>
            <input id="couverture" name="couverture" type="file" accept="image/jpeg,image/png,image/webp">
        </div>

        <button class="btn" type="submit">Enregistrer</button>
        <a class="btn btn-outline" href="<?= site_url('livres') ?>">Annuler</a>
    </form>
</div>
<?= $this->endSection() ?>
