<main class="container">
    <div class="section-title">
        <h2>Suivi National des Distributions</h2>
        <p style="margin-top: 15px; font-style: italic; color: #666;">Données centralisées en temps réel</p>
    </div>

    <div class="donation-sim-box">
        <h3 style="color: var(--royal-red); margin-bottom: 20px;">Simulateur de Distribution d'Aide</h3>
        <p style="margin-bottom: 30px; font-size: 0.95rem;">
            Lancez la simulation automatique pour redistribuer les dons selon les besoins et l'ordre chronologique.
        </p>
        
        <div class="sim-form">
            <button class="btn-gold" onclick="simulateDistribution()">Lancer la simulation</button>
        </div>

        <div id="simResult" class="sim-result" style="display: none;">
            <h4 style="margin-bottom: 15px;">Résultats de la Simulation :</h4>
            <div id="simContent"></div>
        </div>
    </div>

    <div class="city-grid">
        <?php 
        // On regroupe les données par ville pour l'affichage
        $villes = [];
        foreach($stats as $s) {
            $villes[$s['ville_nom']]['region'] = $s['region_nom'];
            $villes[$s['ville_nom']]['besoins'][] = $s;
        }

        foreach($villes as $nomVille => $infos): 
        ?>
        <div class="city-block">
            <div class="city-header">
                <div>
                    <h3 style="margin: 0; font-size: 1.4rem;"><?= htmlspecialchars($nomVille) ?></h3>
                    <small style="opacity: 0.8; font-weight: 300;"><?= htmlspecialchars($infos['region']) ?></small>
                </div>
                <span class="status-badge <?= (count($infos['besoins']) > 2) ? 'critical' : 'transit' ?>">
                    <?= (count($infos['besoins']) > 2) ? 'Urgence Haute' : 'Vigilance' ?>
                </span>
            </div>
            
            <div class="city-content">
                <div class="panel">
                    <h4><i class="fas fa-bullseye" style="margin-right: 10px;"></i> État des Besoins</h4>
                    <table>
                        <thead>
                            <tr>
                                <th>Article</th>
                                <th>Demandé</th>
                                <th>Reçu</th>
                                <th>Reste</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($infos['besoins'] as $b): ?>
                            <tr>
                                <td><?= htmlspecialchars($b['article_label']) ?></td>
                                <td><strong><?= number_format($b['quantite_demandee'], 0) ?></strong></td>
                                <td style="color: green;"><?= number_format($b['quantite_recue'], 0) ?></td>
                                <td style="color: var(--royal-red); font-weight: bold;"><?= number_format($b['reste'], 0) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="panel">
                    <h4><i class="fas fa-info-circle" style="margin-right: 10px;"></i> Résumé Logistique</h4>
                    <div style="padding: 15px; background: #f8f9fa; border-radius: 5px;">
                        <p style="font-size: 0.9rem;">Dernière mise à jour : <?= date('d/m/Y H:i') ?></p>
                        <p style="font-size: 0.8rem; color: #666; margin-top: 10px;">
                            Les distributions sont calculées selon l'ordre de priorité chronologique des besoins saisis.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</main>

<script>
    function simulateDistribution() {
        const resultBox = document.getElementById('simResult');
        const simContent = document.getElementById('simContent');
        
        // Afficher un message de chargement
        simContent.innerHTML = '<p style="text-align: center;">⏳ Simulation en cours...</p>';
        resultBox.style.display = 'block';
        
        // Appeler l'API /dispatch/simulate
        fetch('/dispatch/simulate')
            .then(response => response.json())
            .then(data => {
                let htmlContent = `
                    <div style="padding: 15px; background: #f0f8ff; border-radius: 5px; border-left: 4px solid var(--royal-red);">
                        <p><strong>✅ Simulation terminée</strong></p>
                        <p>Dons traités: <strong>${data.dons_traites}</strong></p>
                        <p>Attributions créées: <strong>${data.attributions_creees}</strong></p>
                        <p>Quantité totale attribuée: <strong>${data.quantite_totale_attribuee}</strong></p>
                        ${data.erreurs.length > 0 ? '<p style="color: var(--royal-red);">⚠️ Erreurs: ' + data.erreurs.join(', ') + '</p>' : ''}
                    </div>
                `;
                simContent.innerHTML = htmlContent;
                resultBox.scrollIntoView({ behavior: 'smooth' });
                
                // Rafraîchir la page après 2 secondes pour voir les données mises à jour
                setTimeout(() => {
                    location.reload();
                }, 2000);
            })
            .catch(error => {
                simContent.innerHTML = '<p style="color: var(--royal-red);">❌ Erreur lors de la simulation: ' + error.message + '</p>';
            });
    }
</script>