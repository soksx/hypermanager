<?php
	if($_SERVER["REQUEST_METHOD"] === "POST"){
		if(isset($_POST["ruser"]) && isset($_POST["remail"]) && isset($_POST["rpassword"]) && isset($_POST["rpassword2"])){
			include("../db.php");
            include("../security.php");
            $sec = new Sec;
            $registerUser = $_POST["ruser"];
            $registerMail = $_POST["remail"];
            $registerPassword = $_POST["rpassword"];
            $registerPassword2 = $_POST["rpassword2"];
            $response = array(0 => "0", 1 => "El usuario ya existe.");
            $userInfo = $sec->getUserInfo($registerUser, $registerMail);
            if($userInfo == false){
            	if($registerPassword == $registerPassword2){
            		$cryptedPass = $sec->encryptUserPassword($registerPassword);
            		$stmt = $db->prepare('INSERT INTO users_hm (user, pass, email) VALUES (:user, :pass, :email)');
            		$stmt->bindParam(':user', $registerUser, PDO::PARAM_STR);
            		$stmt->bindParam(':pass', $cryptedPass, PDO::PARAM_STR);
            		$stmt->bindParam(':email', $registerMail, PDO::PARAM_STR);
            		if($stmt->execute()){
                		$response = array(0 => "1", 1 => "Usuario registrado correctamente.");
            		}else{
            			$response = array(0 => "0", 1 => "Error al registrar.");
            		}
            	}else{
            		$response = array(0 => "0", 1 => "Las contraseñas no coinciden.");
            	}
            }
            echo json_encode($response);
		}
	}
?>