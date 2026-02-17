<main class="container">
    <div class="donation-sim-box" style="text-align: left;">
        <h2 style="color: var(--royal-red); margin-bottom: 20px;">Finaliser l'achat de secours</h2>
        
        <form action="/achat/valider" method="POST">
            <input type="hidden" name="id_ville" value="<?= $params->id_ville ?>">
            <input type="hidden" name="id_article" value="<?= $params->id_article ?>">
            <input type="hidden" name="prix_unitaire" value="<?= $params->prix_unitaire ?>">

            <div class="mb-3">
                <label>Quantité maximale nécessaire : <strong><?= $params->reste ?></strong></label>
                <input type="number" name="quantite" id="qteInput" class="sim-input" 
                       max="<?= $params->reste ?>" placeholder="Quantité à acheter" required 
                       style="width: 100%; margin-top:10px;">
            </div>

            <?php 
                $prixUnitaire = 45000; // Exemple: à récupérer via ton ArticleModel
                $fraisPourcent = 10;   // Exemple: à récupérer via ton ConfigModel
            ?>

            <div style="background: #f4f4f4; padding: 20px; margin-top: 20px; border-radius: 5px;">
                <p>Prix Unitaire : <span id="pu"><?= $prixUnitaire ?></span> Ar</p>
                <p>Frais d'achat : <span><?= $fraisPourcent ?></span>%</p>
                <hr>
                <h4 style="color: var(--royal-red);">Total à payer : <span id="totalAffichage">0</span> Ar</h4>
            </div>

            <div style="margin-top: 30px; display: flex; gap: 10px;">
                <button type="submit" class="btn-gold">Confirmer l'achat</button>
                <a href="/" class="btn-gold" style="background: #666; text-decoration: none;">Annuler</a>
            </div>
        </form>
    </div>
</main>

<script>
    const qteInput = document.getElementById('qteInput');
    const pu = <?= $prixUnitaire ?>;
    const frais = <?= $fraisPourcent ?>;
    const totalAffichage = document.getElementById('totalAffichage');

    qteInput.addEventListener('input', function() {
        let qte = parseFloat(this.value) || 0;
        let montantHT = qte * pu;
        let montantTTC = montantHT * (1 + (frais / 100));
        
        totalAffichage.innerText = montantTTC.toLocaleString('fr-FR');
    });
</script>