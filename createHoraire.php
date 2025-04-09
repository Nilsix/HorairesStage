<?php
    session_start();
    include 'config.php';


    $heureDebut = "";
    $heureDebutPause = "";
    $heureFinPause = "";
    $tempsPause = "";
    $heureFin = "";
    $signature = 0;
    $dateHoraire = "";
    $errors = array();
    
    

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
                $tempsPause = $tempsPause->format('H:i');
                $heureFin = $heureFin->format('H:i');
            }
            
            
            $sql = "INSERT INTO horaire (dateHoraire,heureDebut, heureDebutPause, heureFinPause, tempsPause, heureFin, signature) VALUES ('$dateHoraire' , '$heureDebut' , '$heureDebutPause', '$heureFinPause', '$tempsPause', '$heureFin', '$signature')";
            $result = $conn->query($sql);
            $_SESSION['SUCCESS'] = "Horaire ajouté avec succès";
            exportDatabase();
            header("Location: index.php");
            exit();
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Creer Horaire</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
</head>
<body>
    <div class="container my-5">
        <h2>Creer Horaire</h2>
        <?php 
        
        if(!empty($errors)) {
            foreach($errors as $error) {
                echo "<div class='alert alert-warning' role='alert'> <strong>$error </strong></div>";
            }
        }
        else if(!empty($messageSuccess)){
            $_SESSION['messageSuccess'] = $messageSuccess;
            header("Location: index.php");
            exit();
        }
        ?>
        <form action="" method="post">
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label" for="date">Date</label>
                <div class="col sm-6">
                    <input type="date" name="dateHoraire" id="dateHoraire" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label" for="heureDebut">Heure de début</label>
                <div class="col sm-6">
                    <input type="time" name="heureDebut" id="heureDebut" value="" required>
                </div>
            </div>

            <div class="row mb-3">
                <label class="col-sm-3 col-form-label" for="heureDebutPause">Debut pause </label>
                <div class="col sm-6">
                    <input type="time" name="heureDebutPause" id="heureDebutPause" value="">
                </div>
            </div>

            <div class="row mb-3">
                <label class="col-sm-3 col-form-label" for="heureFinPause">Fin pause </label>
                <div class="col sm-6">
                    <input type="time" name="heureFinPause" id="heureFinPause" value="">
                </div>
            </div>

            <div class="row mb-3">
                <div class="offset-sm-3 col-sm-3 d-grid">
                    <button type="submit" class="btn btn-primary" value="Create Horaire">Create Horaire</button>
                </div>
                <div class="col-sm-3 d-grid">
                    <a href="index.php" class="btn btn-outline-primary">Annuler</a>
                </div>
            </div>
        </form>
    </div>
</body>
</html>
