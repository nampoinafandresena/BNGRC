<main class="container">
    <div class="section-title">
        <h2>Suivi National des Distributions</h2>
        <p style="margin-top: 15px; font-style: italic; color: #666;">Données centralisées en temps réel</p>
    </div>

    <div class="donation-sim-box">
        <h3 style="color: var(--royal-red); margin-bottom: 20px;">🎁 Suivi des Dons en Temps Réel</h3>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <thead>
                <tr style="background-color: #f0f8ff;">
                    <th style="border: 1px solid #ddd; padding: 10px; text-align: left;">ID</th>
                    <th style="border: 1px solid #ddd; padding: 10px; text-align: left;">Donateur</th>
                    <th style="border: 1px solid #ddd; padding: 10px; text-align: left;">Article</th>
                    <th style="border: 1px solid #ddd; padding: 10px; text-align: right;">Reçu</th>
                    <th style="border: 1px solid #ddd; padding: 10px; text-align: right;">Distribué</th>
                    <th style="border: 1px solid #ddd; padding: 10px; text-align: right;">Restant</th>
                </tr>
            </thead>
            <tbody id="donsTableBody">
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px; color: #999;">⏳ Chargement des dons...</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="donation-sim-box">
        <h3 style="color: var(--royal-red); margin-bottom: 20px;">Simulateur de Distribution d'Aide</h3>
        <p style="margin-bottom: 30px; font-size: 0.95rem;">
            Lancez la simulation automatique pour redistribuer les dons selon les besoins et l'ordre chronologique.
        </p>
        
        <div class="sim-form">
            <button class="btn-gold" onclick="simulateDistribution()"> Simuler par date</button>
            <button class="btn-gold" onclick="resetDispatch()"> Réinitialiser distributions</button>

            <button class="btn-gold" id="validateBtn" onclick="validateDistribution()" style="display: none; background-color: #28a745; margin-left: 10px;">✓ Valider</button>
            <button class="btn-gold" id="cancelBtn" onclick="cancelSimulation()" style="display: none; background-color: #6c757d; margin-left: 10px;">✗ Annuler</button>
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
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($infos['besoins'] as $b): ?>
                            <tr>
                                <td><?= htmlspecialchars($b['article_label']) ?></td>
                                <td><strong><?= number_format($b['quantite_demandee'], 0) ?></strong></td>
                                <td style="color: green;"><?= number_format($b['quantite_recue'], 0) ?></td>
                                <td style="color: var(--royal-red); font-weight: bold;"><?= number_format($b['reste'], 0) ?></td>
                                <?php if($b['reste'] > 0): ?>
                                    <td>
                                        <a href="<?= BASE_URL ?>achat/formulaire?id_ville=<?= $b['id_ville'] ?>&id_article=<?= $b['id_article'] ?>&reste=<?= $b['reste'] ?>" 
                                        class="status-badge transit" 
                                        style="text-decoration: none;">
                                        <i class="fas fa-shopping-cart"></i> Acheter
                                        </a>
                                    </td>
                                <?php endif; ?>
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

    function resetDispatch() {
        if (confirm('Êtes-vous sûr de vouloir réinitialiser toutes les distributions? Cette action est irréversible.')) {
            fetch('/dispatch/reset', { method: 'POST' })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Distributions réinitialisées avec succès!');
                        location.reload();
                    } else {
                        alert('Erreur lors de la réinitialisation: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Erreur lors de la réinitialisation: ' + error.message);
                });
        }
    }

    // ========================================================
    // CHARGEMENT DES DONS EN TEMPS RÉEL
    // ========================================================
    
    function loadDons() {
        fetch('/api/dons')
            .then(response => response.json())
            .then(dons => {
                const tableBody = document.getElementById('donsTableBody');
                
                if (dons.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 20px; color: #999;">Aucun don enregistré</td></tr>';
                    return;
                }
                
                tableBody.innerHTML = dons.map(don => `
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 10px;">#${don.id}</td>
                        <td style="border: 1px solid #ddd; padding: 10px;">${don.donateur}</td>
                        <td style="border: 1px solid #ddd; padding: 10px;">${don.article}</td>
                        <td style="border: 1px solid #ddd; padding: 10px; text-align: right;"><strong>${don.quantite_recue}</strong></td>
                        <td style="border: 1px solid #ddd; padding: 10px; text-align: right; color: green;"><strong>${don.quantite_distribuee}</strong></td>
                        <td style="border: 1px solid #ddd; padding: 10px; text-align: right; color: ${don.quantite_restante > 0 ? 'var(--royal-red)' : '#28a745'}; font-weight: bold;">${don.quantite_restante}</td>
                    </tr>
                `).join('');
            })
            .catch(error => {
                console.error('Erreur lors du chargement des dons:', error);
                document.getElementById('donsTableBody').innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 20px; color: var(--royal-red);">❌ Erreur lors du chargement</td></tr>';
            });
    }
    
    // Charger les dons au chargement de la page
    loadDons();
    
    // Recharger les dons toutes les 5 secondes pour voir les changements en temps réel
    setInterval(loadDons, 5000);

    /*
     * ========================================================
     * LOGIQUE DE SIMULATION ET VALIDATION DES DISTRIBUTIONS
     * ========================================================
     * 
     * ANCIEN COMPORTEMENT (COMMENTÉ) :
     * - Route unique : /dispatch/simulate
     * - Sauvegardait directement les distributions
     * - Rafraîchissait la page après 2 secondes automatiquement
     * Inconvénient : pas de contrôle utilisateur, pas de preview
     * 
     * NOUVEAU COMPORTEMENT :
     * - Deux routes séparées : /dispatch/preview et /dispatch/validate
     * - /preview : affiche les propositions (sans sauvegarder)
     * - Utilisateur peut voir le résultat et décider
     * - Si validation : /validate persiste les données
     * - Boutons : Simuler, Valider, Annuler
     * ========================================================
     */
    
    let currentProposals = []; // Stocke les propositions actuelles

    function simulateDistribution() {
        const resultBox = document.getElementById('simResult');
        const simContent = document.getElementById('simContent');
        const validateBtn = document.getElementById('validateBtn');
        const cancelBtn = document.getElementById('cancelBtn');
        
        simContent.innerHTML = '<p style="text-align: center;">⏳ Simulation en cours...</p>';
        resultBox.style.display = 'block';
        
        fetch('/dispatch/preview')
            .then(response => response.json())
            .then(data => {
                currentProposals = data.propositions;
                
                let statsHtml = `
                    <div style="padding: 15px; background: #f0f8ff; border-radius: 5px; border-left: 4px solid var(--royal-red); margin-bottom: 20px;">
                        <p><strong>📊 Propositions de Distribution</strong></p>
                        <p>Dons traités: <strong>${data.stats.dons_traites}</strong></p>
                        <p>Attributions proposées: <strong>${data.stats.attributions_proposees}</strong></p>
                        <p>Quantité totale proposée: <strong>${data.stats.quantite_totale_proposee}</strong></p>
                    </div>
                `;
                
                // Afficher le tableau des propositions
                if (data.propositions.length > 0) {
                    statsHtml += `
                        <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
                            <thead>
                                <tr style="background-color: #f0f8ff;">
                                    <th style="border: 1px solid #ddd; padding: 10px; text-align: left;">Don</th>
                                    <th style="border: 1px solid #ddd; padding: 10px; text-align: left;">Donateur</th>
                                    <th style="border: 1px solid #ddd; padding: 10px; text-align: left;">Quantité</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${data.propositions.map(prop => `
                                    <tr>
                                        <td style="border: 1px solid #ddd; padding: 10px;">#${prop.id_don}</td>
                                        <td style="border: 1px solid #ddd; padding: 10px;">${prop.donateur}</td>
                                        <td style="border: 1px solid #ddd; padding: 10px;">${prop.quantite_attribuee}</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    `;
                } else {
                    statsHtml += '<p style="color: var(--royal-red);">ℹ️ Aucune proposition disponible.</p>';
                }
                
                simContent.innerHTML = statsHtml;
                validateBtn.style.display = 'inline-block';
                cancelBtn.style.display = 'inline-block';
                resultBox.scrollIntoView({ behavior: 'smooth' });
            })
            .catch(error => {
                simContent.innerHTML = '<p style="color: var(--royal-red);">❌ Erreur lors de la simulation: ' + error.message + '</p>';
            });
    }

    function validateDistribution() {
        if (currentProposals.length === 0) {
            alert('Aucune proposition à valider. Lancez d\'abord une simulation.');
            return;
        }

        const validateBtn = document.getElementById('validateBtn');
        const cancelBtn = document.getElementById('cancelBtn');
        const simContent = document.getElementById('simContent');
        
        simContent.innerHTML = '<p style="text-align: center;">⏳ Validation en cours...</p>';
        validateBtn.disabled = true;
        
        fetch('/dispatch/validate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            validateBtn.disabled = false;
            
            if (data.success) {
                let resultHtml = `
                    <div style="padding: 15px; background: #d4edda; border-radius: 5px; border-left: 4px solid #28a745; margin-bottom: 20px;">
                        <p><strong style="color: #155724;">✅ Validation réussie!</strong></p>
                        <p>Attributions créées: <strong>${data.attributions_creees}</strong></p>
                        <p>Quantité totale attribuée: <strong>${data.quantite_totale_attribuee}</strong></p>
                    </div>
                `;
                simContent.innerHTML = resultHtml;
                validateBtn.style.display = 'none';
                cancelBtn.style.display = 'none';
                currentProposals = [];
                
                // Rafraîchir la page après 2 secondes
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                let errorHtml = `
                    <div style="padding: 15px; background: #f8d7da; border-radius: 5px; border-left: 4px solid var(--royal-red);">
                        <p><strong style="color: var(--royal-red);">❌ Erreur lors de la validation</strong></p>
                        <p>${data.erreurs.join('<br>')}</p>
                    </div>
                `;
                simContent.innerHTML = errorHtml;
            }
        })
        .catch(error => {
            validateBtn.disabled = false;
            simContent.innerHTML = '<p style="color: var(--royal-red);">❌ Erreur lors de la validation: ' + error.message + '</p>';
        });
    }

    function cancelSimulation() {
        const resultBox = document.getElementById('simResult');
        const validateBtn = document.getElementById('validateBtn');
        const cancelBtn = document.getElementById('cancelBtn');
        
        resultBox.style.display = 'none';
        validateBtn.style.display = 'none';
        cancelBtn.style.display = 'none';
        currentProposals = [];
    }
</script>