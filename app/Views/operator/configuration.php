<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card">
    <h1>Configuration</h1>
    <p class="muted">Gérer les préfixes valides et les barèmes de frais par tranche.</p>
</div>

<!-- ========== PRÉFIXES ========== -->
<div class="card">
    <h2>Préfixes</h2>

    <form method="POST" action="<?= site_url('/operator/configuration/prefix') ?>" style="margin-bottom:16px;display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;">
        <?= csrf_field() ?>
        <div class="field" style="flex:1;min-width:140px;">
            <label for="prefixe">Préfixe</label>
            <input type="text" id="prefixe" name="prefixe" placeholder="033" maxlength="3" required
                   value="<?= esc(old('prefixe')) ?>">
        </div>
        <div class="field" style="flex:2;min-width:200px;">
            <label for="libelle">Libellé</label>
            <input type="text" id="libelle" name="libelle" placeholder="Ex: Orange" required
                   value="<?= esc(old('libelle')) ?>">
        </div>
        <div class="field" style="display:flex;align-items:center;gap:8px;padding-bottom:4px;">
            <label for="est_operateur_principal" style="margin:0;white-space:nowrap;">Opérateur principal</label>
            <input type="checkbox" id="est_operateur_principal" name="est_operateur_principal" value="1"
                   <?= old('est_operateur_principal') ? 'checked' : '' ?>>
        </div>
        <div class="field" style="display:flex;align-items:flex-end;">
            <button type="submit" class="btn">Ajouter</button>
        </div>
    </form>

    <table>
        <thead>
            <tr>
                <th>Préfixe</th>
                <th>Libellé</th>
                <th>Principal</th>
                <th>État</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if (! empty($prefixes)): ?>
            <?php foreach ($prefixes as $p): ?>
                <tr>
                    <td><?= esc($p['prefixe']) ?></td>
                    <td><?= esc($p['libelle'] ?? '') ?></td>
                    <td>
                        <span class="status <?= $p['est_operateur_principal'] ? 'status-disponible' : 'status-prete' ?>">
                            <?= $p['est_operateur_principal'] ? 'Oui' : 'Non' ?>
                        </span>
                    </td>
                    <td>
                        <span class="status <?= $p['actif'] ? 'status-disponible' : 'status-prete' ?>">
                            <?= $p['actif'] ? 'Actif' : 'Inactif' ?>
                        </span>
                    </td>
                    <td style="display:flex;gap:6px;flex-wrap:wrap;">
                        <a class="btn btn-secondary" href="<?= site_url('/operator/configuration/prefix/' . $p['id'] . '/toggle') ?>">
                            <?= $p['actif'] ? 'Désactiver' : 'Activer' ?>
                        </a>
                        <a class="btn btn-secondary" href="<?= site_url('/operator/configuration/prefix/' . $p['id'] . '/toggle-principal') ?>">
                            <?= $p['est_operateur_principal'] ? 'Retirer principal' : 'Définir principal' ?>
                        </a>
                        <a class="btn btn-danger" href="<?= site_url('/operator/configuration/prefix/' . $p['id'] . '/delete') ?>"
                           onclick="return confirm('Supprimer ce préfixe ?')">
                            Supprimer
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="5" class="muted">Aucun préfixe configuré.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- ========== BARÈMES DE FRAIS ========== -->
<div class="card">
    <h2>Barèmes de frais</h2>

    <form method="POST" action="<?= site_url('/operator/configuration/bareme') ?>" style="margin-bottom:16px;display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;">
        <?= csrf_field() ?>
        <div class="field" style="flex:2;min-width:180px;">
            <label for="type_operation_id">Type d'opération</label>
            <select id="type_operation_id" name="type_operation_id" required>
                <option value="">-- Choisir --</option>
                <?php foreach ($types_operations as $to): ?>
                    <option value="<?= $to['id'] ?>"
                        <?= old('type_operation_id') == $to['id'] ? 'selected' : '' ?>>
                        <?= esc($to['libelle']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="field" style="flex:1;min-width:130px;">
            <label for="montant_min">Montant min</label>
            <input type="number" id="montant_min" name="montant_min" step="0.01" min="0" required
                   value="<?= esc(old('montant_min')) ?>">
        </div>
        <div class="field" style="flex:1;min-width:130px;">
            <label for="montant_max">Montant max</label>
            <input type="number" id="montant_max" name="montant_max" step="0.01" min="0" required
                   value="<?= esc(old('montant_max')) ?>">
        </div>
        <div class="field" style="flex:1;min-width:120px;">
            <label for="frais">Frais</label>
            <input type="number" id="frais" name="frais" step="0.01" min="0" required
                   value="<?= esc(old('frais')) ?>">
        </div>
        <div class="field">
            <button type="submit" class="btn">Ajouter</button>
        </div>
    </form>

    <table>
        <thead>
            <tr>
                <th>Type</th>
                <th>Montant min</th>
                <th>Montant max</th>
                <th>Frais</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if (! empty($baremes)): ?>
            <?php foreach ($baremes as $b): ?>
                <tr>
                    <td><?= esc($b['type_libelle']) ?></td>
                    <td><?= esc(number_format($b['montant_min'], 2)) ?> $</td>
                    <td><?= esc(number_format($b['montant_max'], 2)) ?> $</td>
                    <td><?= esc(number_format($b['frais'], 2)) ?> $</td>
                    <td>
                        <a class="btn btn-danger" href="<?= site_url('/operator/configuration/bareme/' . $b['id'] . '/delete') ?>"
                           onclick="return confirm('Supprimer ce barème ?')">
                            Supprimer
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="5" class="muted">Aucun barème configuré.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?= $this->endSection() ?>
