<?php           
            $servername = 'localhost';
            $username = 'root';
            $password = '';
            $database = 'horairesstage';
            $conn = new mysqli($servername,$username,$password,$database);
            if($conn->connect_error){
                die("Connection failed: " . $conn->connect_error);
            }
            
            function exportDatabase(){
                shell_exec("C:\\xampp\\mysql\\bin\\mysqldump.exe -u root horairesstage > horairesstage.sql"); 
            }
        
?>