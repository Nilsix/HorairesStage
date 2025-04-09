<?php
    session_start();
    include 'config.php';
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $id = $_POST['idHoraire'];
        $sql = "SELECT * FROM horaire WHERE idHoraire = $id";
        $row = $conn->query($sql)->fetch_assoc();
        if($row['signature'] == 1){
            $_SESSION['ERROR'] = "Impossible de signer un horaire signé";
            header("Location: index.php");
            exit();
        }
        $sql = "UPDATE horaire SET signature = 1 WHERE idHoraire = $id";
        $conn->query($sql);
        $_SESSION['SUCCESS'] = "Horaire signé avec succès";
        exportDatabase();
        header("Location: index.php");
        exit();
    }
?>