<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BNGRC | Portail National de Solidarité</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>

  <header>
      <div class="brand">
          <img src="<?= BASE_URL ?>/assets/images/armoirie.png" alt="logo" class="me-2" style="height:44px;">
          <div class="brand-text">
              <h1>BNGRC</h1>
              <p>Repoblikan'i Madagasikara</p>
          </div>
      </div>
      <nav>
          <ul>
            <li><a href="<?= BASE_URL ?>/">Dashboard</a></li>
            <li><a href="<?= BASE_URL ?>/don/formulaire">Saisie de Don</a></li>
            <li><a href="<?= BASE_URL ?>/besoin/formulaire">Formuler un besoin</a></li>
            <li><a href="login.html">Déconnexion</a></li>
          </ul>
      </nav>
  </header>

    <?php include __DIR__ . '/' . $page . '.php'; ?>


</body>


<footer>
    <div style="font-family: 'Playfair Display', serif; font-size: 1.5rem; margin-bottom: 20px;">BNGRC</div>
    <p>&copy; 2026 Bureau National de Gestion des Risques et des Catastrophes.</p>
    <p>ETU004017 - ETU003902 - ETU004025</p>
</footer>