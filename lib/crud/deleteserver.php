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
        $response = array(0 => "0", 1 => "Error al eliminar");
        $isUserServer = $sec->checkUserHasServer($userid, $serverid);
        if ($isUserServer){
            $stmt = $db->prepare('DELETE FROM servers_hm WHERE id = :serverid');
            $stmt->bindParam(':serverid', $serverid, PDO::PARAM_INT);
            if($stmt->execute()){
                $response = array(0 => "1", 1 => "Eliminado correctamente");
            }
        }
        echo json_encode($response);
    }
}
?>