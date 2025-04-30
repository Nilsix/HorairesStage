<?php
    session_start();
    include 'config.php';
    $errors = array();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Modifier Horaire</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
</head>
<body>
    <div class="container my-5">
        <h2>Modifier Horaire</h2>
        <?php 
        if($_SERVER["REQUEST_METHOD"] == "GET"){
            if(isset($_GET['idHoraire'])){
                $idHoraire = $_GET['idHoraire'];
            }
            else if(isset($_SESSION['idHoraire'])){
                $idHoraire = $_SESSION['idHoraire'];
                unset($_SESSION['idHoraire']);
            }else{
                header("Location: index.php");
                exit();
            }
            $sql = "SELECT * FROM horaire WHERE idHoraire = $idHoraire";
            $result = $conn->query($sql);
            $row = $result->fetch_assoc();
            if($row['signature'] == 1){
                $_SESSION['ERROR'] = "Impossible de modifier un horaire signé";
                header("Location: index.php");
            }
            $dateHoraire = new DateTime($row['dateHoraire']);
            $dateHoraireOriginal = new DateTime($row['dateHoraire']);
            $heureDebut = new DateTime($row['heureDebut']);
            $heureDebutPause = new DateTime($row['heureDebutPause']);
            $heureFinPause = new DateTime($row['heureFinPause']);
            $tempsPause = new DateTime($row['tempsPause']);
            $heureFin = new DateTime($row['heureFin']);
            if($heureDebutPause->format('H:i') == '00:00'){
                $heureDebutPause = "";
            }
            else{
                $heureDebutPause = $heureDebutPause->format('H:i');
            }
            if($heureFinPause->format('H:i') == '00:00'){
                $heureFinPause = "";
            }
            else{
                $heureFinPause = $heureFinPause->format('H:i');
            }
            if($tempsPause->format('H:i') == '00:00'){
                $tempsPause = "";
            }
            else{
                $tempsPause = $tempsPause->format('H:i');
            }
            if($heureFin->format('H:i') == '00:00'){
                $heureFin = "";
            }
            else{
                $heureFin = $heureFin->format('H:i');
            }
            if(isset($_SESSION['ERROR'])) {
                foreach($_SESSION['ERROR'] as $error) {
                    echo "<div class='alert alert-warning' role='alert'> <strong>$error </strong></div>";
                }
                unset($_SESSION['ERROR']);
            }
            else if(!empty($messageSuccess)){
                $_SESSION['messageSuccess'] = $messageSuccess;
                header("Location: index.php");
                exit();
            }
            echo "
            <form action='postEditHoraire.php' method='post'>
                <input type='hidden' name='dateHoraireOriginal' value='" . $dateHoraireOriginal->format('Y-m-d') . "'>
                <input type='hidden' name='idHoraire' value='$idHoraire'>
                <div class='row mb-3'>
                    <label class='col-sm-3 col-form-label' for='date'>Date</label>
                    <div class='col sm-6'>
                        <input type='date' name='dateHoraire' id='dateHoraire' value='" . $dateHoraire->format('Y-m-d') . "' required>
                    </div>
                </div>
                <div class='row mb-3'>
                    <label class='col-sm-3 col-form-label' for='heureDebut'>Heure de début</label>
                    <div class='col sm-6'>
                        <input type='time' name='heureDebut' id='heureDebut' value='" . $heureDebut->format('H:i') . "' required>
                    </div>
                </div>

                <div class='row mb-3'>
                    <label class='col-sm-3 col-form-label' for='heureDebutPause'>Début pause</label>
                    <div class='col sm-6'>
                        <input type='time' name='heureDebutPause' id='heureDebutPause' value='" . $heureDebutPause. "'>
                    </div>
                </div>

                <div class='row mb-3'>
                    <label class='col-sm-3 col-form-label' for='heureFinPause'>Fin pause</label>
                    <div class='col sm-6'>
                        <input type='time' name='heureFinPause' id='heureFinPause' value='" . $heureFinPause. "'>
                    </div>
                </div>

                <div class='row mb-3'>
                    <div class='offset-sm-3 col-sm-3 d-grid'>
                        <button type='submit' class='btn btn-primary' value='Create Horaire'>Modifier Horaire</button>
                    </div>
                    <div class='col-sm-3 d-grid'>
                        <a href='index.php' class='btn btn-outline-primary'>Annuler</a>
                    </div>
                </div>
            </form>
            ";
        }
    ?>

    </div>
</body>
</html>
