<div class="container">

    <!-- ===== TITRE ===== -->
    <div class="section-title">
        <h2>Situation Nationale des Secours</h2>
    </div>

    <!-- ===== KPI FINANCIERS ===== -->
    <div class="city-block">
        <div class="city-header">
            <h3>Indicateurs Clés Nationaux</h3>
            <button class="btn-gold">Actualiser</button>
        </div>

        <div class="city-content" style="grid-template-columns: 1fr; text-align:center;">

    <!-- ===== LIGNE HAUTE : TOTAL vs DISTRIBUÉ ===== -->
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:40px; margin-bottom:40px;">

            <!-- TOTAL -->
            <div class="panel">
                <h4>Montant Total des Besoins</h4>
                <h2 style="font-size:2.5rem;"><?= $total ?> Ar</h2>
                <a href="" class="status-badge critical">Voir les details</a>
                
            </div>

            <!-- DISTRIBUÉ -->
            <div class="panel">
                <h4>Montant Besoins satisfaits</h4>
                <h2 style="font-size:2.5rem; color:var(--gold);"><?= $satisfied ?> Ar</h2>
                <a href="" class="status-badge delivered">Voir les details</a>
            </div>

        </div>

        <!-- ===== LIGNE BASSE : RESTE À FINANCER ===== -->
        <div style="max-width:600px; margin:0 auto;">

            <div class="panel" style="padding:30px;">
                <h4 style="font-size:1rem;">Montant Restant à Financer</h4>

                <h1 style="
                    font-size:3rem;
                    margin:20px 0;
                ">
                    4 470 000 000 Ar
                </h1>
                <a href="" class="status-badge transit" >Voir les details</a>
                
            </div>

        </div>
    </div>

</div>

    <!-- ===== RÉCAPITULATION PAR VILLE ===== -->
    <!-- <div class="city-block">
        <div class="city-header">
            <h3>Détail par Région / Ville</h3>
        </div>

        <div class="city-content">

            <div class="panel">
                <h4>Antananarivo</h4>
                <table>
                    <thead>
                        <tr>
                            <th>Besoin</th>
                            <th>Valeur</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Riz</td>
                            <td>1 200 000 000 Ar</td>
                            <td><span class="status-badge delivered">Distribué</span></td>
                        </tr>
                        <tr>
                            <td>Médicaments</td>
                            <td>850 000 000 Ar</td>
                            <td><span class="status-badge transit">En transit</span></td>
                        </tr>
                        <tr>
                            <td>Tentes</td>
                            <td>430 000 000 Ar</td>
                            <td><span class="status-badge critical">Critique</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="panel">
                <h4>Toamasina</h4>
                <table>
                    <thead>
                        <tr>
                            <th>Besoin</th>
                            <th>Valeur</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Eau potable</td>
                            <td>600 000 000 Ar</td>
                            <td><span class="status-badge delivered">Distribué</span></td>
                        </tr>
                        <tr>
                            <td>Vivres</td>
                            <td>780 000 000 Ar</td>
                            <td><span class="status-badge transit">En transit</span></td>
                        </tr>
                        <tr>
                            <td>Kits sanitaires</td>
                            <td>320 000 000 Ar</td>
                            <td><span class="status-badge critical">Critique</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div> -->

</div>
