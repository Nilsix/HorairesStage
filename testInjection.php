<?php
// Connexion classique
$conn = new mysqli("localhost", "root", "", "horairesstage");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $valeur = $_POST["test"];

    // ⚠️ INJECTION VULNÉRABLE volontaire
    $sql = "INSERT INTO horaire (dateHoraire, heureDebut, heureDebutPause, heureFinPause, tempsPause, heureFin, signature)
            VALUES ('$valeur', '09:00:00', '12:00:00', '12:30:00', '00:30:00', '16:30:00', 0)";

    if ($conn->query($sql)) {
        echo "<p>✅ Requête exécutée avec succès.</p>";
    } else {
        echo "<p>❌ Erreur : " . $conn->error . "</p>";
    }
}
?>

<h2>Test d'injection SQL (volontairement vulnérable)</h2>
<form method="post">
    <label for="test">Champ dangereux :</label>
    <input type="text" name="test" id="test">
    <input type="submit" value="Envoyer">
</form>
