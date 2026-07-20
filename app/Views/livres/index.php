<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php
$categories = [
    '' => 'Toutes les categories',
    'Roman' => 'Roman',
    'Science-fiction' => 'Science-fiction',
    'Histoire' => 'Histoire',
    'Informatique' => 'Informatique',
    'Biographie' => 'Biographie',
    'Autre' => 'Autre',
];
?>

<div class="card">
    <h1>Catalogue des livres</h1>
    <form method="get" action="<?= site_url('livres') ?>">
        <div style="display:grid;grid-template-columns:2fr 1fr auto;gap:10px;align-items:end;">
            <div class="field" style="margin:0;">
                <label for="mot_cle">Mot-cle (titre)</label>
                <input id="mot_cle" name="mot_cle" type="text" value="<?= esc($mot_cle ?? '') ?>" placeholder="Ex: Design Patterns">
            </div>
            <div class="field" style="margin:0;">
                <label for="categorie">Categorie</label>
                <select id="categorie" name="categorie">
                    <?php foreach ($categories as $value => $label): ?>
                        <option value="<?= esc($value) ?>" <?= (($categorie ?? '') === $value) ? 'selected' : '' ?>><?= esc($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <button class="btn" type="submit">Rechercher</button>
            </div>
        </div>
    </form>
</div>

<div class="card table-wrap">
    <table>
        <thead>
        <tr>
            <th>Titre</th>
            <th>Auteur</th>
            <th>Annee</th>
            <th>Statut</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php if (! empty($livres)): ?>
            <?php foreach ($livres as $livre): ?>
                <tr>
                    <td>
                        <a href="<?= site_url('livres/' . $livre['id']) ?>"><?= esc($livre['titre']) ?></a>
                    </td>
                    <td><?= esc($livre['auteur']) ?></td>
                    <td><?= esc($livre['annee_publication']) ?></td>
                    <td>
                        <?php $estDisponible = ($livre['statut'] ?? '') === 'disponible'; ?>
                        <span class="status <?= $estDisponible ? 'status-disponible' : 'status-prete' ?>">
                            <?= $estDisponible ? 'Disponible' : 'Prete' ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($estDisponible): ?>
                            <form method="post" action="<?= site_url('livres/' . $livre['id'] . '/preter') ?>" style="display:flex;gap:8px;flex-wrap:wrap;">
                                <?= csrf_field() ?>
                                <input type="text" name="nom_emprunteur" placeholder="Nom emprunteur" required style="min-width:170px;max-width:220px;">
                                <button class="btn btn-secondary" type="submit">Preter</button>
                            </form>
                        <?php else: ?>
                            <form method="post" action="<?= site_url('livres/' . $livre['id'] . '/retour') ?>" style="display:inline;">
                                <?= csrf_field() ?>
                                <button class="btn btn-outline" type="submit">Retourner</button>
                            </form>
                        <?php endif; ?>

                        <form method="post" action="<?= site_url('livres/' . $livre['id'] . '/supprimer') ?>" style="display:inline;" onsubmit="return confirm('Confirmer la suppression de ce livre ?');">
                            <?= csrf_field() ?>
                            <button class="btn btn-danger" type="submit">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" class="muted">Aucun livre trouve.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if (! empty($pager) && empty($isRecherche)): ?>
    <div class="card">
        <?= $pager->links() ?>
    </div>
<?php endif; ?>
<?= $this->endSection() ?>
