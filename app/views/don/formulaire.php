<section id="form-don" class="container">
    <div class="section-title">
        <h2>Enregistrement de dons</h2>
        <p>Saisie officielle des ressources entrantes</p>
    </div>

    <div style="background: var(--white); border: 1px solid var(--border-color); padding: 40px; box-shadow: var(--shadow);">
        <form style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;" action="/don/insert" method="post">
            
            <div class="panel">
                <h4 style="margin-bottom: 20px; color: var(--gold);"><i class="fas fa-info-circle"></i> Nature du Don</h4>
                
                <div style="margin-bottom: 20px;">
                    <label style="display:block; margin-bottom:8px; font-weight:700; font-size:0.8rem;">Choisissez un article</label>
                    <select name="id_article" style="width:100%; padding:12px; border:1px solid #ddd; background:#fff;">
                        <?php foreach ($articles as $a) { ?>
                            <option value="<?= $a["id"]?>"><?= $a["label"] ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display:block; margin-bottom:8px; font-weight:700; font-size:0.8rem;">Quantite selon l'unite</label>
                    <input type="number" name="quantite" min="0" style="width:100%; padding:12px; border:1px solid #ddd;" placeholder="Ex: 50" required>
                </div>
            <!-- </div>

            <div class="panel"> -->
                <h4 style="margin-bottom: 20px; color: var(--gold);"><i class="fas fa-box-open"></i> Détails</h4>
                
                <div style="margin-bottom: 20px;">
                    <label style="display:block; margin-bottom:8px; font-weight:700; font-size:0.8rem;">Date de reception</label>
                        <input type="date" name="date_reception" style="width:100%; padding:12px; border:1px solid #ddd;" required>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display:block; margin-bottom:8px; font-weight:700; font-size:0.8rem;">Donateur</label>
                    <input type="text" name="donateur" style="width:100%; padding:12px; border:1px solid #ddd;" placeholder="Ex: Vanessa Manonga">
                </div>
            </div>

            <div style="grid-column: 1 / span 2; text-align: right; display: flex; gap: 15px; justify-content: flex-end;">
                <button type="reset" style="background:none; border: 1px solid #ccc; padding: 12px 25px; cursor: pointer; text-transform: uppercase; font-size: 0.8rem;">Annuler</button>
                <button type="submit" class="btn-gold" style="padding: 12px 40px;">Enregistrer</button>
            </div>
        </form>
    </div>
</section>