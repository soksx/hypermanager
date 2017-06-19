<?php
    class Sec {
        public function checkUserHasServer($userid, $serverid){
            include("db.php");
            $result = false;
            $sql = "SELECT id FROM servers_hm WHERE :userid = $userid";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
            if($stmt->execute()){
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)){ 
                    if($row['id'] == $serverid){
                        $result = true;
                    }
                }
            }
            return $result;
        }
        /*
        * Devuelve la contraseña del usuario
        *
        * @author IDC
        * @return $pass devulve la contraseña
        *
        */
        public function getUserInfo($user, $mail = ""){
            include("db.php");
            $email = $mail;
            if ($email == "")
                $email = $user;
            /*
            * IDC: Instanciamos el objeto de la base de datos
            */
            /*
            * IDC: Creamos una sentencia preparada y asignamos los parametros
            */
            $stmt = $db->prepare('SELECT id,user,pass from users_hm WHERE (user LIKE :user OR email LIKE :email) LIMIT 1');
            $stmt->bindParam(':user', $user, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            /*
            * IDC: Ejecutamos la sentencia y guardamos el resultado en la variable $pass
            */
            $stmt->execute();
            $userinfo = $stmt->fetch(PDO::FETCH_ASSOC);

            return $userinfo;
        }//Fin getUserInfo()
        /*
        * Devuelve la contraseña del usuario encriptada
        *
        * @author IDC
        * @param $curPass contraseña a encriptar
        * @return $password_hash devulve la contraseña hasheada
        *
        */
        public function encryptUserPassword($curPass){
            $salt = '$5$rounds=5000$ejNyMEwxbUBwMWV+$huWLf7OxQ0.UoZ2lzAK.PD1NXTVCNhTewDYUdY3A38.';//'$5$rounds=5000$ejNyMEwxbUBwMWV+$'; //z3r0L1m@p1e~
            $password_hash = crypt($curPass, $salt);
            return $password_hash;
        }
        /*
        * Devuelve la contraseña del servidor encriptada
        *
        * @author IDC
        * @param $curPass contraseña a encriptar
        * @return $encryptedText devulve la contraseña encriptada
        *
        */
        public function encryptPassword($curPass){
            $_keyPhrase = "MXdoNHRTdVAzXzNnVSFzNw==";
            $string = $curPass;
            $iv = mcrypt_create_iv(
                mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC),
                MCRYPT_DEV_URANDOM
            );
            $encrypted = base64_encode(
                $iv .
                mcrypt_encrypt(
                    MCRYPT_RIJNDAEL_128,
                    hash('sha256', $_keyPhrase, true),
                    $string,
                    MCRYPT_MODE_CBC,
                    $iv
                )
            );
            return $encrypted;
        }
        /*
        * Devuelve la contraseña del servidor decrypted
        *
        * @author IDC
        * @param $cryPass contraseña encriptada
        * @return $decryptedText devulve la contraseña decrypted
        *
        */
        public function decryptPassword($cryPass){
            $_keyPhrase = "MXdoNHRTdVAzXzNnVSFzNw==";
            $data = base64_decode($cryPass);
            $iv = substr($data, 0, mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC));
            $decrypted = rtrim(
                mcrypt_decrypt(
                    MCRYPT_RIJNDAEL_128,
                    hash('sha256', $_keyPhrase, true),
                    substr($data, mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC)),
                    MCRYPT_MODE_CBC,
                    $iv
                ),
                "\0"
            );
            return $decrypted;
        }
    }
?>