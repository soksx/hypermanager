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
        $response = array(0 => "0", 1 => "Error al actualizar");
        $isUserServer = $sec->checkUserHasServer($userid, $serverid);
        if ($isUserServer){
            $stmt = $db->prepare('SELECT ipaddress AS ip, username AS user, alias AS sname FROM servers_hm WHERE id = :serverid');
            $stmt->bindParam(':serverid', $serverid, PDO::PARAM_INT);
            if($stmt->execute()){
                $response = $stmt->fetch(PDO::FETCH_ASSOC);
            }
        }
        echo json_encode($response);
    }
    }
?>