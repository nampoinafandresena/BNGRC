<section id="form-don" class="container">
    <div class="section-title">
        <h2>Enregistrement de convoi</h2>
        <p>Saisie officielle des ressources entrantes</p>
    </div>

    <div style="background: var(--white); border: 1px solid var(--border-color); padding: 40px; box-shadow: var(--shadow);">
        <form style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
            
            <div class="panel">
                <h4 style="margin-bottom: 20px; color: var(--gold);"><i class="fas fa-info-circle"></i> Origine du Don</h4>
                
                <div style="margin-bottom: 20px;">
                    <label style="display:block; margin-bottom:8px; font-weight:700; font-size:0.8rem;">Donateur / Organisation</label>
                    <select style="width:100%; padding:12px; border:1px solid #ddd; background:#fff;">
                        <option>PAM (Programme Alimentaire Mondial)</option>
                        <option>Croix Rouge Malagasy</option>
                        <option>Secteur Privé</option>
                        <option>Don État Français</option>
                    </select>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display:block; margin-bottom:8px; font-weight:700; font-size:0.8rem;">Référence du bordereau</label>
                    <input type="text" style="width:100%; padding:12px; border:1px solid #ddd;" placeholder="Ex: BORD-MG-8890">
                </div>
            </div>

            <div class="panel">
                <h4 style="margin-bottom: 20px; color: var(--gold);"><i class="fas fa-box-open"></i> Détails Techniques</h4>
                
                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 10px; margin-bottom: 20px;">
                    <div>
                        <label style="display:block; margin-bottom:8px; font-weight:700; font-size:0.8rem;">Type de marchandise</label>
                        <input type="text" style="width:100%; padding:12px; border:1px solid #ddd;" placeholder="Ex: Riz blanc">
                    </div>
                    <div>
                        <label style="display:block; margin-bottom:8px; font-weight:700; font-size:0.8rem;">Unité</label>
                        <select style="width:100%; padding:12px; border:1px solid #ddd; background:#fff;">
                            <option>Kg</option>
                            <option>Sacs</option>
                            <option>Cartons</option>
                            <option>Litres</option>
                        </select>
                    </div>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display:block; margin-bottom:8px; font-weight:700; font-size:0.8rem;">Quantité totale</label>
                    <input type="number" style="width:100%; padding:12px; border:1px solid #ddd;" placeholder="Ex: 5000">
                </div>
            </div>

            <div style="grid-column: 1 / span 2;">
                <label style="display:block; margin-bottom:8px; font-weight:700; font-size:0.8rem;">Observations particulières</label>
                <textarea style="width:100%; padding:12px; border:1px solid #ddd; height: 100px; font-family: 'Lato';" placeholder="Précisez l'état du convoi ou les conditions de stockage requises..."></textarea>
            </div>

            <div style="grid-column: 1 / span 2; text-align: right; display: flex; gap: 15px; justify-content: flex-end;">
                <button type="reset" style="background:none; border: 1px solid #ccc; padding: 12px 25px; cursor: pointer; text-transform: uppercase; font-size: 0.8rem;">Annuler</button>
                <button type="submit" class="btn-gold" style="padding: 12px 40px;">Enregistrer et Simuler la répartition</button>
            </div>
        </form>
    </div>
</section>