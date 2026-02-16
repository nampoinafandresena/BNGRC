<main class="container">
    <div class="section-title">
        <h2>Suivi National des Distributions</h2>
        <p style="margin-top: 15px; font-style: italic; color: #666;">Données centralisées en temps réel</p>
    </div>

    <div class="donation-sim-box">
        <h3 style="color: var(--royal-red); margin-bottom: 20px;">Simulateur de Distribution d'Aide</h3>
        <p style="margin-bottom: 30px; font-size: 0.95rem;">
            Calculez automatiquement la répartition idéale d'un don en fonction du score d'urgence actuel de chaque zone.
        </p>
        
        <div class="sim-form">
            <select class="sim-input" id="donType">
                <option value="Riz">Riz (Sacs 50kg)</option>
                <option value="Eau">Eau Potable (Bouteilles)</option>
                <option value="Tentes">Tentes (Unités)</option>
                <option value="Médicaments">Kits Médicaux (Cartons)</option>
            </select>
            <input type="number" class="sim-input" id="donQty" placeholder="Quantité Totale (ex: 500)">
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
                        <th>Justification</th>
                    </tr>
                </thead>
                <tbody id="simBody">
                    </tbody>
            </table>
            <div style="text-align: right; margin-top: 20px;">
                <button class="btn-gold" style="background: var(--royal-red);">Confirmer l'envoi</button>
            </div>
        </div>
    </div>

    <div class="city-grid">
        
        <div class="city-block">
            <div class="city-header">
                <div>
                    <h3 style="margin: 0; font-size: 1.4rem;">Mananjary</h3>
                    <small style="opacity: 0.8; font-weight: 300;">Région Vatovavy</small>
                </div>
                <span class="status-badge critical">Urgence Maximale</span>
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
                            <tr>
                                <td>Riz (50kg)</td>
                                <td>500</td>
                                <td style="color: #16a34a;">200</td>
                                <td style="color: var(--royal-red); font-weight: bold;">300</td>
                            </tr>
                            <tr>
                                <td>Eau Potable</td>
                                <td>2000 L</td>
                                <td style="color: #16a34a;">500 L</td>
                                <td style="color: var(--royal-red); font-weight: bold;">1500 L</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="panel">
                    <h4><i class="fas fa-truck" style="margin-right: 10px;"></i> Dernières Collectes</h4>
                    <table>
                        <thead>
                            <tr>
                                <th>Source</th>
                                <th>Date</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Don Privé</td>
                                <td>15 Fév</td>
                                <td><span class="status-badge delivered">Reçu</span></td>
                            </tr>
                            <tr>
                                <td>ONG Partenaire</td>
                                <td>16 Fév</td>
                                <td><span class="status-badge transit">En Route</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</main>