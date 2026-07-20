<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card">
    <h1>Liste des clients</h1>
</div>

<div class="card table-wrap">
    <table>
        <thead>
        <tr>
            <th>Num tel</th>
            <th>Client</th>
            <th>Num compte</th>
            <th>Solde</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php if (! empty($clients)): ?>
            <?php foreach ($clients as $client): ?>
                <tr>
                    <td><?= esc($client['numero_telephone']) ?></td>
                    <td><?= esc($client['prenom'] . ' ' . $client['nom']) ?></td>
                    <td><?= esc($client['numero_telephone']) ?></td>
                    <td>
                        <span class="status <?= ($client['solde'] >= 0) ? 'status-disponible' : 'status-prete' ?>">
                            <?= esc(number_format($client['solde'], 2)) ?> $
                        </span>
                    </td>
                    <td>
                        <a class="btn" href="<?= site_url('/operator/clients/' . $client['id']) ?>">Detail</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" class="muted">Aucun client trouve.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?= $this->endSection() ?>
