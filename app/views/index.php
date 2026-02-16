

<main class="container">
    <div class="section-title">
        <h2>Suivi National des Distributions</h2>
    </div>

    <div class="city-block">
        <div class="city-header">
            <h3>Antananarivo</h3>
            <span class="status-badge critical">Vigilance</span>
        </div>
        <div class="city-content">
            <div class="panel">
                <h4>Besoins</h4>
                <table>
                    <thead><tr><th>Article</th><th>Quantité</th></tr></thead>
                    <tbody><tr><td>Vivres</td><td>1200 kg</td></tr></tbody>
                </table>
            </div>
            <div class="panel">
                <h4>Collectes</h4>
                <table>
                    <thead><tr><th>Origine</th><th>Statut</th></tr></thead>
                    <tbody><tr><td>Privé</td><td><span class="status-badge delivered">Reçu</span></td></tr></tbody>
                </table>
            </div>
        </div>
    </div>
</main>
<script>
        // Données fictives des villes avec un "Score d'urgence" (0 à 100)
        const cityData = [
            { name: "Mananjary", urgencyScore: 90, note: "Zone dévastée à 80%" },
            { name: "Farafangana", urgencyScore: 60, note: "Accès difficile mais besoins modérés" },
            { name: "Vohipeno", urgencyScore: 80, note: "Inondations sévères signalées" },
            { name: "Manakara", urgencyScore: 40, note: "Situation stable" }
        ];

        function simulateDistribution() {
            const qty = parseInt(document.getElementById('donQty').value);
            const type = document.getElementById('donType').value;
            const resultBox = document.getElementById('simResult');
            const tableBody = document.getElementById('simBody');

            // Validation simple
            if (!qty || qty <= 0) {
                alert("Veuillez entrer une quantité valide.");
                return;
            }

            // Calculer le total des scores d'urgence pour faire des pourcentages
            let totalScore = 0;
            cityData.forEach(city => totalScore += city.urgencyScore);

            // Générer le tableau HTML
            let htmlContent = "";

            cityData.forEach(city => {
                // Algorithme de répartition : (Score Ville / Score Total) * Quantité Totale
                let allocated = Math.round((city.urgencyScore / totalScore) * qty);
                
                // Si la quantité est 0, on ne l'affiche pas ou on affiche 0
                if (allocated > 0) {
                    htmlContent += `
                        <tr>
                            <td><strong>${city.name}</strong></td>
                            <td>${city.urgencyScore}/100</td>
                            <td style="color: var(--royal-red); font-weight: bold;">${allocated} ${type}</td>
                            <td style="font-size: 0.85rem; font-style: italic;">${city.note}</td>
                        </tr>
                    `;
                }
            });

            // Afficher le résultat
            tableBody.innerHTML = htmlContent;
            resultBox.style.display = "block";
            
            // Scroll fluide vers le résultat
            resultBox.scrollIntoView({ behavior: 'smooth' });
        }
    </script>
