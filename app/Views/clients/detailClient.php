<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mobile Money - Détail du compte</title>
    <link rel="stylesheet" href="<?= base_url('style.css') ?>">
</head>
<body>
    <div class="login-card" style="max-width:700px;">
        <h1>Détail du compte</h1>

        <?php include APPPATH . 'Views/clients/layoutClient.php'; ?>

        <div style="margin-top:24px;">
            <h2 style="color:#1e3c72; font-size:16px; margin-bottom:12px;">Fiche Client</h2>
            <table>
                <tr>
                    <th>Nom et Prénom</th>
                    <td><?= esc(($compte['prenom'] ?? '') . ' ' . ($compte['nom'] ?? '')) ?></td>
                </tr>
                <tr>
                    <th>Numéro de téléphone</th>
                    <td><?= esc($compte['numero_telephone']) ?></td>
                </tr>
            </table>
        </div>

        <div style="margin-top:24px;">
            <h2 style="color:#1e3c72; font-size:16px; margin-bottom:12px;">Détail Montant - Compte Principal</h2>
            <table>
                <tr>
                    <th>Somme Dépôts</th>
                    <td style="color:#1e7e34; font-weight:700;">
                        <?= esc(number_format($totalDepots, 2, ',', ' ')) ?> Ar
                    </td>
                </tr>
                <tr>
                    <th>Somme Retraits</th>
                    <td style="color:#b3261e; font-weight:700;">
                        <?= esc(number_format($totalRetraits, 2, ',', ' ')) ?> Ar
                    </td>
                </tr>
                <tr>
                    <th>Somme Transferts</th>
                    <td style="font-weight:700;">
                        <?= esc(number_format($totalTransferts, 2, ',', ' ')) ?> Ar
                    </td>
                </tr>
                <tr>
                    <th>Situation Compte</th>
                    <td style="font-weight:700; font-size:16px; color:<?= $situationCompte >= 0 ? '#1e7e34' : '#b3261e' ?>;">
                        <?= esc(number_format($situationCompte, 2, ',', ' ')) ?> Ar
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="color:#666; font-size:13px; padding-top:8px;">
                        Situation compte = Somme dépôts − Somme retraits − Somme transferts (dont origine client)
                    </td>
                </tr>
            </table>
        </div>

        <div style="margin-top:24px; text-align:center;">
            <a href="<?= base_url('historique') ?>" style="display:inline-block; padding:12px 24px; background:#2a5298; color:#fff; border-radius:8px; text-decoration:none; font-weight:600; margin-right:8px;">
                Voir les historiques
            </a>
            <a href="#solde" onclick="document.getElementById('soldeSection').style.display='block'; this.style.display='none';" style="display:inline-block; padding:12px 24px; background:#1e3c72; color:#fff; border-radius:8px; text-decoration:none; font-weight:600;">
                Voir le solde
            </a>
        </div>

        <div id="soldeSection" style="display:none; margin-top:20px; text-align:center; padding:16px; background:#f0f7ff; border-radius:8px; border:1px solid #b7d4f0;">
            <p style="color:#1e3c72; font-size:14px; margin-bottom:4px;">Montant dans le compte principal</p>
            <p style="color:#1e3c72; font-size:28px; font-weight:700;">
                <?= esc(number_format($compte['solde'], 2, ',', ' ')) ?> Ar
            </p>
            <p style="color:#666; font-size:13px; margin-top:4px;">
                Situation compte = <?= esc(number_format($situationCompte, 2, ',', ' ')) ?> Ar (dont origine client)
            </p>
        </div>
    </div>
</body>
</html>
