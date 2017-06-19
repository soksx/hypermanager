<?php
    session_name('hmssid');
    session_set_cookie_params(3600,"/");
    session_start();
    if($_SERVER["REQUEST_METHOD"] === "POST"){
    	if (isset($_POST["serverid"]) && isset($_SESSION["userid"]) && isset($_POST["msg"]) && isset($_GET["mode"])){
    		if ($_GET["mode"] == "tg"){
    			include("../db.php");
    			include("../security.php");
    			$sec = new Sec;
    			$userid = $_SESSION["userid"];
            	$serverid = $_POST["serverid"];
            	$msg = $_POST["msg"];
            	$isUserServer = $sec->checkUserHasServer($userid, $serverid);
            	if($isUserServer){
            		$stmt = $db->prepare('SELECT telegram_alert FROM user_prop_hm WHERE serverid = :serverid && userid = :userid');
                	$stmt->bindParam(':serverid', $serverid, PDO::PARAM_INT);
                	$stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
                	if($stmt->execute()){
                    	$row = $stmt->fetch(PDO::FETCH_ASSOC);
                   		$r = @file_get_contents("https://api.telegram.org/bot<token>/sendMessage?chat_id=" . $row["telegram_alert"] ."&text=" . $msg );
                   		$r = array(0 => "1", 1 => "Mensaje enviado correctamente");
                   		if (strpos($http_response_header[0], '403') !== false)
                   			$r = array(0 => "2", 1 => "<span>Abrele chat al bot <a id='bot-tg-link' href='https://t.me/<botname>' target='_blank'>@<botname></a> en telegram.</span>");
                    	echo json_encode($r);
                	}
            	}
    		}
    		else if ($_GET["mode"] == "email"){
    			include("../db.php");
    			include("../security.php");
    			$sec = new Sec;
    			$userid = $_SESSION["userid"];
            	$serverid = $_POST["serverid"];
            	$msg = $_POST["msg"];
            	$isUserServer = $sec->checkUserHasServer($userid, $serverid);
            	if($isUserServer){
	    			$stmt = $db->prepare('SELECT email_alert FROM user_prop_hm WHERE serverid = :serverid && userid = :userid');
	                $stmt->bindParam(':serverid', $serverid, PDO::PARAM_INT);
	                $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
	                if($stmt->execute()){
	                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
	                    $to      = $row["email_alert"];
						$subject = explode(":",$msg)[0];
						$message = explode(":",$msg)[1];
						$headers = 'From: <mail>' . "\r\n" .
						    'Reply-To: <mail>' . "\r\n" .
						    'X-Mailer: PHP/' . phpversion();

						mail($to, $subject, $message, $headers);
	                }
    			}
    		}
    	}
    }
?>
