<!DOCTYPE html>
<html>
<head>
    <title>HoraireStage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
</head>
<body>
<div class="container my-5">
    <h2> Horaires stage </h2>
    <a class="btn btn-primary" href="createHoraire.php">Créer un horaire</a>
    <br>
    <table class="table">
        <thead>
            <tr>
                <th>Heure de début</th>
                <th>Heure de début de pause</th>
                <th>Heure de fin de pause</th>
                <th>Temps de pause</th>
                <th>Heure de fin</th>
                <th>Signée </th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $servername = 'localhost';
                $username = 'root';
                $password = '';
                $database = 'horairesstage';
                $conn = new mysqli($servername,$username,$password,$database);
                if($conn->connect_error){
                    die("Connection failed: " . $conn->connect_error);
                }
                $sql = "SELECT * FROM horaire";
                $result = $conn->query($sql);

                if(!$result){
                    die("Invalid query : ".$conn->error);
                }
            
                    
                while($row = $result->fetch_assoc()){
                    echo "
                        <tr> 
                    <td>".$row['heureDebut']."</td>
                    <td>".$row['heureDebutPause']."</td>
                    <td>".$row['heureFinPause']."</td>
                    <td>".$row['tempsPause']."</td>
                    <td>".$row['heureFin']."</td>
                    <td>".$row['signee']."</td>
                    <td> 
                        <a class='btn btn-primary' href='editHoraire.php'>Modifier</a>
                        <a class='btn btn-danger' href='deleteHoraire.php'>Supprimer</a>
                    </td>
                    </tr>
                    ";
                }
            ?>
            
        </tbody>
    </table>
</div> 
<form action="" method="post">
    <label for="heureDebut">Heure de début</label>
    <input type="time" name="heureDebut" id="heureDebut" required>
    <br>
    <label for="heureDebutPause">Debut pause </label>
    <input type="time" name="heureDebutPause" id="heureDebutPause" required>
    <br>
    <label for="heureFinPause">Fin pause </label>
    <input type="time" name="heureFinPause" id="heureFinPause" required>
    <br>
    <input type="submit" value="Envoyer">
</form>

<?php
   if($_SERVER["REQUEST_METHOD"] == "POST"){
        $errors = array();
        //gestion erreurs
        $heureDebutPause = new DateTime($_POST["heureDebutPause"]);
        $heureFinPause = new DateTime($_POST["heureFinPause"]);
        if($heureFinPause < $heureDebutPause){
            $errors[] = "La fin de la pause doit être après le début de la pause";
        }
        
        $heureMinDebut = new DateTime("7:00:00");
        $heureMaxDebut = new DateTime("9:30:00");
        if(count($errors) <= 0){
            $tempsPause = new DateInterval("PT30M");
            $heureFin = new DateTime($_POST["heureDebut"]);
            $heureFin->add(new DateInterval("PT7H"));
            if(!empty($_POST["heureDebutPause"]) && !empty($_POST["heureFinPause"])){
                
                $tempsPause = $heureDebutPause->diff($heureFinPause);
                $heureFin->add($tempsPause);
            }
            echo "Heure de debut : ".$_POST["heureDebut"]."<br>";
            if(!empty($_POST["heureDebutPause"]) && !empty($_POST["heureFinPause"])){
                echo "Heure de debut de pause : ".$heureDebutPause->format('H:i')."<br>";
                echo "Heure de fin de pause : ".$heureFinPause->format('H:i')."<br>";

            }
            if($tempsPause->h > 0 && $tempsPause->i > 0){
                echo "Temps de pause : ".$tempsPause->h."h".$tempsPause->i." minutes"."<br>";
            }
            else if($tempsPause->h > 0){
                echo "Temps de pause : ".$tempsPause->h."h"."<br>";
            }
            else{
                echo "Temps de pause : ".$tempsPause->i." minutes"."<br>";
            }
            echo "Heure de Fin : ".$heureFin->format("H:i");
    }
    else{
        //message erreur
        foreach($errors as $error){
            echo "<p style='color:red'> Erreur : ".$error."</p>";
        }
    }
    }
?>


