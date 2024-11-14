<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de Réservation</title>
    <link rel="stylesheet" href="confiration.css">
</head>
<body>
    <div class="container">
        <h1>Merci pour votre Réservation !</h1>
        <p><?php echo htmlspecialchars($_GET['message']); ?></p>
        <a href="2.html" class="btn">Retour à l'accueil</a>
    </div>
</body>
</html>