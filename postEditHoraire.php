<?php 
session_start();
include 'config.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errors = array();
    $_SESSION['idHoraire'] = $_POST['idHoraire'];
    $heureDebut = $_POST["heureDebut"];
    $heureDebut = new DateTime($heureDebut);
    $heureDebutPause = $_POST["heureDebutPause"] ?? "";
    $heureFinPause = $_POST["heureFinPause"] ?? "";
    $dateHoraire = $_POST["dateHoraire"];
    // gestion erreurs
    if(!empty($heureDebutPause) && !empty($heureFinPause)){
        $heureDebutPause = new DateTime($heureDebutPause);
        $heureFinPause = new DateTime($heureFinPause);
        if ($heureFinPause <= $heureDebutPause) {
            $errors[] = "La fin de la pause doit être après le début de la pause";
        }
    }
    if(!empty($heureDebutPause) && $heureDebutPause <= $heureDebut){
        $errors[] = "Le début de la pause doit être après l'heure du début";
    }
    $duplicateSql = "SELECT * FROM horaire WHERE dateHoraire = '$dateHoraire'";
    $duplicateResult = $conn->query($duplicateSql);
    if($duplicateResult->num_rows > 0){
        $errors[] = "La date est déja utilisée";
    }

    if (count($errors) <= 0) {
        if (!empty($heureDebutPause) && !empty($heureFinPause)) {
            $tempsPause = $heureDebutPause->diff($heureFinPause);
            $heureDebutPause = $heureDebutPause->format('H:i');
            $heureFinPause = $heureFinPause->format('H:i');
        }
        $heureDebut = $heureDebut->format('H:i');
        if(!empty($tempsPause)){
            $heureFin = new DateTime($heureDebut);
            $heureFin->add(new DateInterval("PT7H"));
            $heureFin->add($tempsPause);
            $tempsPauseDT = new DateTime("00:00");
            $tempsPauseDT->add($tempsPause);
            $tempsPauseDT = $tempsPauseDT->format('H:i');
            $heureFin = $heureFin->format('H:i');
        }
        $sql = "UPDATE horaire SET heureDebut = '$heureDebut', heureDebutPause = '$heureDebutPause', heureFinPause = '$heureFinPause', tempsPause = '$tempsPauseDT', heureFin = '$heureFin' WHERE idHoraire = $idHoraire";
        $result = $conn->query($sql);
        $_SESSION['SUCCESS'] = "Horaire modifié avec succès";
        exportDatabase();
        header("Location: index.php");
        exit();
    }
    else{
        $_SESSION['ERROR'] = $errors;
        header("Location: editHoraire.php");
        exit();
    }
}
?>