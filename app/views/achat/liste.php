<main class="container">
    <div class="section-title">
        <h2>Historique des Achats de Secours</h2>
        <p>Consultez les acquisitions financées par les dons en argent</p>
    </div>

    <div class="donation-sim-box" style="padding: 20px; margin-bottom: 30px;">
        <form action="<?= BASE_URL ?>achat/liste" method="GET" style="display: flex; gap: 15px; align-items: center; justify-content: center;">
            <label>Filtrer par ville :</label>
            <select name="id_ville" class="sim-input" style="width: 250px;">
                <option value="">Toutes les villes</option>
                <?php foreach($villes as $v): ?>
                    <option value="<?= $v['id'] ?>" <?= ($id_ville_selectionnee == $v['id']) ? 'selected' : '' ?>>
                        <?= $v['nom'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn-gold">Filtrer</button>
            <a href="<?= BASE_URL ?>achat/liste" class="btn-gold" style="background: #666; text-decoration: none;">Réinitialiser</a>
        </form>
    </div>

    <div class="city-block" style="padding: 0;">
        <div class="city-header">
            <h3 style="margin:0;">Registre des transactions</h3>
            <span>Total : <?= count($achats) ?> achat(s)</span>
        </div>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8f8f8;">
                    <th>Date</th>
                    <th>Ville Cible</th>
                    <th>Article</th>
                    <th>Quantité</th>
                    <th>Prix Unitaire</th>
                    <th>Montant Total (Frais inclus)</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($achats)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 30px;">Aucun achat trouvé pour cette sélection.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach($achats as $a): ?>
                    <tr>
                        <td><?= date('d/m/Y H:i', strtotime($a['date_achat'])) ?></td>
                        <td><strong><?= $a['nom_ville'] ?></strong></td>
                        <td><?= $a['nom_article'] ?></td>
                        <td><?= number_format($a['quantite'], 0) ?></td>
                        <td><?= number_format($a['prix_unitaire'], 0, ',', ' ') ?> Ar</td>
                        <td style="color: var(--royal-red); font-weight: bold;">
                            <?= number_format($a['montant_argent_utilise'], 0, ',', ' ') ?> Ar
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <div style="margin-top: 20px;">
        <a href="<?= BASE_URL ?>" class="btn-gold" style="background: var(--dark-grey); text-decoration: none;">
            <i class="fas fa-arrow-left"></i> Retour au Dashboard
        </a>
    </div>
</main>