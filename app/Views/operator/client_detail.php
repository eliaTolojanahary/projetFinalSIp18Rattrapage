<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card">
    <h1>Détail du client</h1>
    <a class="btn btn-secondary" href="<?= site_url('/operator/clients') ?>">← Retour à la liste</a>
</div>

<div class="card">
    <h2>Informations</h2>
    <table>
        <tr>
            <th style="width:200px;">Nom</th>
            <td><?= esc($client['nom'] ?? '-') ?></td>
        </tr>
        <tr>
            <th>Prénom</th>
            <td><?= esc($client['prenom'] ?? '-') ?></td>
        </tr>
        <tr>
            <th>Téléphone</th>
            <td><?= esc($client['numero_telephone']) ?></td>
        </tr>
        <tr>
            <th>Numéro de compte</th>
            <td><?= esc($client['numero_telephone']) ?></td>
        </tr>
        <tr>
            <th>Solde actuel</th>
            <td>
                <span class="status <?= ($client['solde'] >= 0) ? 'status-disponible' : 'status-prete' ?>">
                    <?= esc(number_format($client['solde'], 2)) ?> $
                </span>
            </td>
        </tr>
    </table>
</div>

<div class="card">
    <h2>Situation du compte</h2>
    <table>
        <tr>
            <th style="width:280px;">Total Dépôts</th>
            <td style="color:var(--ok);font-weight:700;">
                <?= esc(number_format($client['situation']['total_depots'] ?? 0, 2)) ?> $
            </td>
        </tr>
        <tr>
            <th>Total Retraits</th>
            <td style="color:var(--danger);font-weight:700;">
                <?= esc(number_format($client['situation']['total_retraits'] ?? 0, 2)) ?> $
            </td>
        </tr>
        <tr>
            <th>Total Transferts</th>
            <td style="font-weight:700;">
                <?= esc(number_format($client['situation']['total_transferts'] ?? 0, 2)) ?> $
            </td>
        </tr>
        <tr>
            <td colspan="2" class="muted" style="padding-top:12px;font-size:13px;">
                Le solde est mis à jour à chaque opération (dépôt, retrait ou transfert).
            </td>
        </tr>
    </table>
</div>

<?= $this->endSection() ?>
