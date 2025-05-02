<?php
session_start();
include 'config.php';
if(!isset($_SESSION["selectInForm"])){
    $_SESSION["selectInForm"] = 3;
}
importDatabase();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Horaires Stage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
</head>
<body>
<div class="container my-5">
    <h2> Horaires stage </h2>
    <a class="btn btn-primary" href="createHoraire.php">Créer un horaire</a>
    <form class="d-flex" action="index.php" method="post" onchange="this.submit()"> 
        <select name="tri" id="tri">
            <option value="signature"<?php if($_SESSION["selectInForm"] == 1) echo'selected';?>>Signés</option>
            <option value="non_signature" <?php if($_SESSION["selectInForm"] == 2) echo'selected';?>>Non signés</option>
            <option value="all" <?php if($_SESSION["selectInForm"] == 3) echo'selected';?> >Tous</option>
        </select>
    </form>
    <br>
    <br>
    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>Date</th>
                <th>Heure de début</th>
                <th>Début de pause</th>
                <th>Fin de pause</th>
                <th>Heure de fin</th>
                <th>Temps de pause</th>
                <th>Signée </th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
                
                // Créer la base de données si elle n'existe pas
                $sql = "CREATE DATABASE IF NOT EXISTS horairesstage";
                if($conn->query($sql) === TRUE){
                    $conn->select_db('horairesstage');
                }
                $sql = "SELECT * FROM horaire ORDER BY dateHoraire DESC";
                if($_SERVER["REQUEST_METHOD"] == "POST"){
                    if($_POST["tri"] == "signature"){
                        $_SESSION["selectInForm"] = 1;
                        $sql = "SELECT * FROM horaire WHERE signature = 1 ORDER BY dateHoraire DESC";
                    }
                    else if($_POST["tri"] == "non_signature"){
                        $_SESSION["selectInForm"] = 2;
                        $sql = "SELECT * FROM horaire WHERE signature = 0 ORDER BY dateHoraire DESC";
                    }
                    else{
                        $_SESSION["selectInForm"] = 3;
                        $sql = "SELECT * FROM horaire ORDER BY dateHoraire DESC";
                    }
                }
                
                $result = $conn->query($sql);
                while($row = $result->fetch_assoc()){
                    $heureDebut = new DateTime($row['heureDebut']);
                    $heureDebutPause = new DateTime($row['heureDebutPause']);
                    $heureFinPause = new DateTime($row['heureFinPause']);
                    $tempsPause = new DateTime($row['tempsPause']);
                    $heureFin = new DateTime($row['heureFin']);
                    $dateHoraire = new DateTime($row['dateHoraire']);
                    // Vérifier si l'heure est 00:00
                    if($heureDebutPause->format('H:i') == '00:00'){
                        $heureDebutPause = "";
                    }
                    if($heureFinPause->format('H:i') == '00:00'){
                        $heureFinPause = "";
                    }
                    if($tempsPause->format('H:i') == '00:00'){
                        $tempsPause = "";
                    }
                    if($heureFin->format('H:i') == '00:00'){
                        $heureFin = "";
                    }
                    echo "
                        <tr>
                            <td>".$dateHoraire->format('d/m/Y')."</td>
                    <td>".$heureDebut->format('H:i')."</td>";
                    if(!empty($heureDebutPause)){
                        echo "<td>".$heureDebutPause->format('H:i')."</td>";
                        
                    }
                    else{
                       
                        echo "<td></td>";
                    }
                    if(!empty($heureFinPause)){
                        echo "<td>".$heureFinPause->format('H:i')."</td>";
                    }
                    else{
                        echo "<td></td>";
                    }
                    if(!empty($heureFin)){
                        echo "<td>".$heureFin->format('H:i')."</td>";
                    }
                    else{
                        $heureFinTempDebut = clone $heureDebut;
                        $heureFinTempDebut->add(new DateInterval('PT7H30M'));
                        $heureFinTempFin = clone $heureDebut;
                        $heureFinTempFin->add(new DateInterval('PT8H'));
                        echo "<td>".$heureFinTempDebut->format('H:i')." - ".$heureFinTempFin->format('H:i')."</td>";
                    }
                    if(!empty($tempsPause)){
                        echo "<td>".$tempsPause->format('H:i')."</td>";
                    }
                    else{
                        echo "<td></td>";
                    }
                    if($row['signature'] == 1){
                        echo "<td>OUI</td>";
                        echo " <td></td>";
                    }
                    else{
                        echo "<td>NON</td>";
                        echo "
                    <td>
                        <form action='editHoraire.php' method='get' class='d-inline'>
                        <input type='hidden' name='idHoraire' value='".$row['idHoraire']."'>
                        <input type='submit' class='btn btn-primary' value='Modifier'>
                        </form>
                        <form action='deleteHoraire.php' method='post' class='d-inline delete-form'>
                        <input type='hidden' name='idHoraire' value='".$row['idHoraire']."'>
                        <input type='submit' class='btn btn-danger' value='Supprimer'>
                        </form>
                        <form action='signHoraire.php' method='post' class='d-inline sign-form'>
                        <input type='hidden' name='idHoraire' value='".$row['idHoraire']."'>
                        <input type='submit' class='btn btn-success' value='Signer'>
                        </form>
                    </td>";
                    }
                    echo "</tr>";           
                }
            ?>
        </tbody>
    </table>
    <?php
        if(isset($_SESSION["SUCCESS"])){
            echo "<div id='flashAppear' class='alert alert-success' role='alert'> <strong>".$_SESSION["SUCCESS"] ." </strong></div>";
            unset($_SESSION["SUCCESS"]);
        }
        if(isset($_SESSION["ERROR"])){
            echo "<div id='flashAppear' class='alert alert-danger' role='alert'> <strong>".$_SESSION["ERROR"] ." </strong></div>";
            unset($_SESSION["ERROR"]);
        }
    ?>
    <script>
        setTimeout(() =>{
            const flash = document.getElementById("flashAppear");
            if(flash){
                flash.style.transition = "opacity 0.5s ease";
                flash.style.opacity = 0;
                setTimeout(()=> flash.remove(),500);
            }
        }, 3000);
        document.querySelectorAll('.delete-form').forEach(form=> {
            form.addEventListener('submit',function(e){
                if(!confirm("Es-tu sûre de vouloir supprimer cet horaire?")){
                    e.preventDefault();
                }
            });
        });
        document.querySelectorAll('.sign-form').forEach(form=> {
            form.addEventListener('submit',function(e){
                if(!confirm("Es-tu sûre de vouloir signer? Ceci est une action irreversible qui t'empechera de modifier ou de supprimer un horaire")){
                    e.preventDefault();
                }
            });
        });
    </script>
</div> 
