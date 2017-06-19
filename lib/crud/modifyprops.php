<?php
	session_name('hmssid');
	session_set_cookie_params(3600,"/");
	session_start();
	if($_SERVER["REQUEST_METHOD"] === "POST"){
		if(isset($_POST["serverid"]) && isset($_SESSION["userid"])){
			include("../db.php");
	        include("../security.php");
	        $sec = new Sec;
	        $userid = $_SESSION["userid"];
	        $serverid = $_POST["serverid"];

	        if(isset($_POST['rtime']) && (!empty($_POST["rtime"]) | $_POST["rtime"] == 0))
	        	$refresh_time = $_POST['rtime']; 
	        else
	        	$refresh_time = null;
	        if(isset($_POST['hdd']) && (!empty($_POST["hdd"]) | $_POST["hdd"] == 0))
	        	$hdd_status = (intval($_POST['hdd']) > 100) ? 100 : $_POST['hdd'];
	        else
	        	$hdd_status = null;
	        if(isset($_POST['ram']) && (!empty($_POST["ram"]) | $_POST["ram"] == 0))
	        	$ram_status =   (intval($_POST['ram']) > 100) ? 100 : $_POST['ram'];
	        else
	        	$ram_status = null;
	        if(isset($_POST['proc']) && (!empty($_POST["proc"]) | $_POST["proc"] == 0))
	        	$proc_status = (intval($_POST['proc']) > 100) ? 100 : $_POST['proc']; 
	        else
	        	$proc_status = null;
	        if(isset($_POST['web']) && (!empty($_POST["web"]) | $_POST["web"] == 0))
	        	$web_alert = $_POST['web'];  
	        else
	        	$web_alert = null;
	        if(isset($_POST['useremail']) && !empty($_POST['useremail']))
	        	$email_alert = $_POST['useremail'];
	        else
	        	$email_alert = null;
	        if(isset($_POST['tgusername']) && !empty($_POST['tgusername']))
	        	$telegram_alert = $_POST['tgusername']; 
	        else
	        	$telegram_alert = null;
	        $response = array(0 => "0", 1 => "Error al establecer las configuraciones");
	        $isUserServer = $sec->checkUserHasServer($userid, $serverid);
	        if($isUserServer){
	        	$stmt = $db->prepare('SELECT id FROM user_prop_hm WHERE userid = :userid AND serverid = :serverid');
	        	$stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
            	$stmt->bindParam(':serverid', $serverid, PDO::PARAM_INT);
            	if($stmt->execute()){
            		$row = $stmt->fetch(PDO::FETCH_ASSOC);
            		if($stmt->rowCount() > 0){
            			$stmt = $db->prepare('UPDATE user_prop_hm SET refresh_time = COALESCE(:rtime, refresh_time), 
	            			hdd_status = COALESCE(:hdd, hdd_status), 
	            			ram_status = COALESCE(:ram, ram_status), 
	            			proc_status = COALESCE(:proc, proc_status), 
	            			web_alert = COALESCE(:web, web_alert), 
	            			email_alert = COALESCE(:useremail, email_alert), 
	            			telegram_alert = COALESCE(:tgusername, telegram_alert)
            			WHERE id = :id');
            			$stmt->bindParam(':id', $row['id'], PDO::PARAM_INT);
            			$stmt->bindParam(':rtime', $refresh_time, PDO::PARAM_INT);
            			$stmt->bindParam(':hdd', $hdd_status, PDO::PARAM_INT);
            			$stmt->bindParam(':ram', $ram_status, PDO::PARAM_INT);
            			$stmt->bindParam(':proc', $proc_status, PDO::PARAM_INT);
            			$stmt->bindParam(':web', $web_alert, PDO::PARAM_INT);
            			$stmt->bindParam(':useremail', $email_alert, PDO::PARAM_STR);
            			$stmt->bindParam(':tgusername', $telegram_alert, PDO::PARAM_STR);
            			if($stmt->execute()){
            				$response = array(0 => "1", 1 => "Configuración actualizada correctamente.");
            			}
            		}else{
            			$stmt = $db->prepare('INSERT INTO user_prop_hm (userid, serverid, refresh_time, hdd_status, ram_status, proc_status, web_alert, email_alert, telegram_alert) VALUES (:userid, :serverid, :rtime, :hdd, :ram, :proc, :web, :useremail, :tgusername)');
            			$stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
            			$stmt->bindParam(':serverid', $serverid, PDO::PARAM_INT);
            			$stmt->bindParam(':rtime', $refresh_time, PDO::PARAM_INT);
            			$stmt->bindParam(':hdd', $hdd_status, PDO::PARAM_INT);
            			$stmt->bindParam(':ram', $ram_status, PDO::PARAM_INT);
            			$stmt->bindParam(':proc', $proc_status, PDO::PARAM_INT);
            			$stmt->bindParam(':web', $web_alert, PDO::PARAM_INT);
            			$stmt->bindParam(':useremail', $email_alert, PDO::PARAM_STR);
            			$stmt->bindParam(':tgusername', $telegram_alert, PDO::PARAM_STR);
            			if($stmt->execute()){
            				$response = array(0 => "1", 1 => "Configuración añadida correctamente.");
            			}
            		}
            	}
	        }
	        echo json_encode($response);
		}
	}
?>
