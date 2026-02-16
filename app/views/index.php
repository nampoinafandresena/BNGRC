<main class="container">
    <div class="section-title">
        <h2>Suivi National des Distributions</h2>
        <p style="margin-top: 15px; font-style: italic; color: #666;">Données centralisées en temps réel</p>
    </div>

    <div class="donation-sim-box">
        <h3 style="color: var(--royal-red); margin-bottom: 20px;">Simulateur de Distribution d'Aide</h3>
        <p style="margin-bottom: 30px; font-size: 0.95rem;">
            Calculez automatiquement la répartition idéale d'un don en fonction des besoins actuels.
        </p>
        
        <div class="sim-form">
            <select class="sim-input" id="donType">
                <option value="Riz">Riz (Sacs 50kg)</option>
                <option value="Eau">Eau Potable (Litre)</option>
                <option value="Argent">Fonds (Ariary)</option>
                <option value="Tôles">Matériaux (Tôles)</option>
            </select>
            <input type="number" class="sim-input" id="donQty" placeholder="Quantité Totale">
            <button class="btn-gold" onclick="simulateDistribution()">Lancer la simulation</button>
        </div>

        <div id="simResult" class="sim-result">
            <h4 style="margin-bottom: 15px;">Proposition de Répartition Intelligente :</h4>
            <table id="simTable">
                <thead>
                    <tr>
                        <th>Ville Cible</th>
                        <th>Besoin Actuel</th>
                        <th>Quantité Allouée</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="simBody">
                    </tbody>
            </table>
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
    // On récupère les données PHP pour le JS de simulation
    const cityNeeds = <?= json_encode($stats) ?>;

    function simulateDistribution() {
        const qty = parseInt(document.getElementById('donQty').value);
        const typeLabel = document.getElementById('donType').value;
        const resultBox = document.getElementById('simResult');
        const tableBody = document.getElementById('simBody');

        if (!qty || qty <= 0) { alert("Entrez une quantité"); return; }

        // On filtre les besoins qui correspondent au type sélectionné et qui ont un reste > 0
        let targets = cityNeeds.filter(item => 
            item.article_label.includes(typeLabel) && item.reste > 0
        );

        let totalReste = targets.reduce((sum, item) => sum + parseFloat(item.reste), 0);
        let htmlContent = "";

        targets.forEach(city => {
            // Répartition au prorata du besoin restant
            let allocated = Math.min(city.reste, Math.round((city.reste / totalReste) * qty));
            
            if (allocated > 0) {
                htmlContent += `
                    <tr>
                        <td><strong>${city.ville_nom}</strong></td>
                        <td>${city.reste} unité(s)</td>
                        <td style="color: var(--royal-red); font-weight: bold;">${allocated} ${typeLabel}</td>
                        <td><button class="status-badge delivered" style="border:none; cursor:pointer;">Appliquer</button></td>
                    </tr>`;
            }
        });

        tableBody.innerHTML = htmlContent || "<tr><td colspan='4'>Aucun besoin critique pour cet article.</td></tr>";
        resultBox.style.display = "block";
        resultBox.scrollIntoView({ behavior: 'smooth' });
    }
</script>