<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mobile Money - Historique</title>
    <link rel="stylesheet" href="<?= base_url('style.css') ?>">
</head>
<body>
    <div class="login-card" style="max-width:1000px;">
    <div class="login-card" style="max-width:1000px;">
        <h1>Historique des transactions</h1>

        <?php if (empty($historique)): ?>
            <p class="subtitle">Aucune transaction pour le moment.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Compte expediteur</th>
                        <th>Compte destinataire</th>
                        <th>Commission</th>
                        <th>Montant</th>
                        <th>frais </th>
                        <th>frais retrait</th>
                        <th>promotion</th>
                        <th>epargnes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($historique as $t): ?>
                        <tr>
                            <td><?= esc(date('d/m/Y H:i', strtotime($t['date_operation']))) ?></td>
                            <td><?= esc($t['type_libelle']) ?></td>
                            <td>
                                <?php if ($t['type_code'] === 'retrait'): ?>
                                    —
                                <?php else: ?>
                                    <?= esc($t['compte_emetteur']) ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($t['type_code'] === 'depot'): ?>
                                    —
                                <?php else: ?>
                                    <?= esc($t['compte_destinataire'] ?? $t['compte_emetteur']) ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($t['commission']): ?>
                                    <?= esc($t['commission']) ?>
                                <?php else: ?>
                                    0
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($t['montant']): ?>
                                    <?= esc($t['montant']) ?>
                                <?php else: ?>
                                    0
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($t['montant_frais']): ?>
                                    <?= esc($t['montant_frais']) ?>
                                <?php else: ?>
                                    0
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($t['frais_retrait']): ?>
                                    <?= esc($t['frais_retrait']) ?>
                                <?php else: ?>
                                    0
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($t['promotion']): ?>
                                    <?= esc($t['promotion']) ?>
                                <?php else: ?>
                                    0
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($t['epargnes']): ?>
                                    <?= esc($t['epargnes']) ?>
                                <?php else: ?>
                                    0
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
