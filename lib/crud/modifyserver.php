<?php
    session_name('hmssid');
    session_set_cookie_params(3600,"/");
    session_start();
    if($_SERVER["REQUEST_METHOD"] === "POST"){
        if(isset($_SESSION["userid"])){
            include("../db.php");
            include("../security.php");
            $sec = new Sec;
            $userid = $_SESSION["userid"];
            $ipaddr = (isset($_POST["ipaddress"]) && !empty($_POST["ipaddress"])) ? $_POST["ipaddress"] : null;
            $username = (isset($_POST["username"]) && !empty($_POST["username"])) ? $_POST["username"] : null;
            //$pass = $_POST["password"];
            $pass = (isset($_POST["password"]) && !empty($_POST["password"])) ? $sec->encryptPassword($_POST["password"]) : null;
            $alias = (isset($_POST["alias"]) && !empty($_POST["alias"])) ? $_POST["alias"] : null;
            $response = array(0 => "0", 1 => "Error al añadir/editar un servidor");
            $serverid = (isset($_POST["serverid"]) && !empty($_POST["serverid"])) ? $_POST["serverid"] : null;
            if ($serverid != null){
                //edit server
                $isUserServer = $sec->checkUserHasServer($userid, $serverid);
                $response = array(0 => "0", 1 => "Error al editar");
                if($isUserServer){
                    $curpass = null;
                    if($pass == null){
                        $sql = "SELECT password FROM servers_hm WHERE id = $serverid";
                        foreach ($db->query($sql) as $row) {
                            if ($row['password'] != null){
                                $curpass = $row['password'];
                            }
                        }
                    }
                    $stmt = $db->prepare('UPDATE servers_hm SET 
                            ipaddress = COALESCE(:ipaddress, ipaddress), 
                            username = COALESCE(:username, username), 
                            password = COALESCE(:pass, :curpass), 
                            alias = COALESCE(:alias, alias)
                        WHERE id = :serverid');
                    $stmt->bindParam(':ipaddress', $ipaddr, PDO::PARAM_STR);
                    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
                    $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
                    $stmt->bindParam(':curpass', $curpass, PDO::PARAM_STR);
                    $stmt->bindParam(':alias', $alias, PDO::PARAM_STR);
                    $stmt->bindParam(':serverid', $serverid, PDO::PARAM_INT);
                    $response = array(0 => "0", 1 => "Error al editar");
                    if($stmt->execute()){
                        $response = array(0 => "1", 1 => "Editado correctamente");
                    }
                }
            }else{
                //insert server
                $stmt = $db->prepare('INSERT INTO servers_hm (userid, ipaddress, username, password, alias) VALUES (:userid, :ipaddress, :username, :pass, :alias)');
                $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
                $stmt->bindParam(':ipaddress', $ipaddr, PDO::PARAM_STR);
                $stmt->bindParam(':username', $username, PDO::PARAM_STR);
                $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
                $stmt->bindParam(':alias', $alias);
                $response = array(0 => "0", 1 => "Error al añadir");
                if($stmt->execute()){
                    $response = array(0 => "1", 1 => "Añadido correctamente", 2 => $db->lastInsertId());
                }
            }
            
            echo json_encode($response);
        }
    }
?>