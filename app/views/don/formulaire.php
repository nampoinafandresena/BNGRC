<section id="form-don" class="container" style="max-width: 900px; margin: 40px auto;">
    <div class="section-title">
        <h2>Enregistrement de dons</h2>
        <p>Saisie officielle des ressources entrantes</p>
    </div>

    <div style="background: var(--white); border: 1px solid var(--border-color); padding: 40px; box-shadow: var(--shadow); border-radius: 8px;">
        <form style="display: grid; grid-template-columns: 1fr; gap: 40px;" action="<?= BASE_URL ?>/don/insert" method="post">
            
            <div class="panel" style="display: flex; flex-direction: column; gap: 25px;">
                <h4 style="margin: 0; color: var(--gold);"><i class="fas fa-info-circle"></i> Nature du don</h4>
                
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <label style="font-weight: 700; font-size: 0.8rem; color: #333;">Choisissez un article</label>
                    <select name="id_article" style="width: 100%; padding: 12px; border: 1px solid #ddd; background: #fff; border-radius: 4px; font-size: 0.95rem;">
                        <?php foreach ($articles as $a) { ?>
                            <option value="<?= $a["id"]?>"><?= $a["label"] ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <label style="font-weight: 700; font-size: 0.8rem; color: #333;">Quantité selon l'unité</label>
                    <input type="number" name="quantite" min="0" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 0.95rem;" placeholder="Ex: 50" required>
                </div>
            </div>

            <div class="panel" style="display: flex; flex-direction: column; gap: 25px;">
                <h4 style="margin: 0; color: var(--gold);"><i class="fas fa-box-open"></i> Détails</h4>
                
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <label style="font-weight: 700; font-size: 0.8rem; color: #333;">Date de réception</label>
                    <input type="date" name="date_reception" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 0.95rem;" required>
                </div>

                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <label style="font-weight: 700; font-size: 0.8rem; color: #333;">Donateur</label>
                    <input type="text" name="donateur" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 0.95rem;" placeholder="Ex: Vanessa Manonga" value="Anonyme" autocomplete="on">
                </div>
            </div>

            <div style="grid-column: 1; display: flex; gap: 15px; justify-content: center; margin-top: 20px;">
                <button type="reset" style="background: none; border: 1px solid #ccc; padding: 12px 35px; cursor: pointer; text-transform: uppercase; font-size: 0.8rem; border-radius: 4px; font-weight: 600;">Annuler</button>
                <button type="submit" class="btn-gold" style="padding: 12px 45px; border-radius: 4px; font-weight: 600;">Enregistrer</button>
            </div>
        </form>
    </div>
</section>