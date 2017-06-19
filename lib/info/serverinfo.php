<?php
    session_name('hmssid');
    session_set_cookie_params(3600,"/");
    session_start();
    if($_SERVER["REQUEST_METHOD"] === "POST"){
        if (isset($_POST["serverid"]) && isset($_SESSION["userid"])){
            set_include_path('..' . DIRECTORY_SEPARATOR);
            include("security.php");
            include("db.php");
            $sec = new Sec;
            $userid = $_SESSION["userid"];
            $serverid = $_POST["serverid"];
            $isUserServer = $sec->checkUserHasServer($userid, $serverid);
            if($isUserServer){
                $stmt = $db->prepare('SELECT ipaddress, username, password FROM servers_hm WHERE id = :serverid');
                $stmt->bindParam(':serverid', $serverid, PDO::PARAM_INT);
                if($stmt->execute()){
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    set_include_path('..' . DIRECTORY_SEPARATOR . 'SSH2' . DIRECTORY_SEPARATOR);
                    include('Net/SSH2.php');
                    if(filter_var(gethostbyname($row['ipaddress']), FILTER_VALIDATE_IP))
                    {
                        $ssh = new Net_SSH2($row['ipaddress']);
                    }else{
                        exit('["offline"]');
                    }
                    $decPass = $sec->decryptPassword($row['password']);
                    if (!$ssh->login($row['username'], $decPass)) {
                        exit('["offline"]');
                    }

                    //$res = $ssh->exec('free -m');
                    $res = $ssh->exec('free | sed -n 2p | tr -s " " | cut -d" " -f2-4');
                    $mem = array_filter(explode(" ", $res));
                    //$mem = array_values(array_filter(explode(" ", explode("Mem: ", $res)[1])));
                    //echo "RAM total: " . $mem[0] . "MB RAM usada: " . $mem[1] . "MB RAM libre: " . $mem[2] . "MB";
                    // $res = $ssh->exec('df -k /');
                    // //echo "<br/>";
                    // $hdd = array_values(array_filter(explode(" ",strrev($res))));
                    //$hdddddd = $ssh->exec('df -k | awk \'{if (NR!=1) {printf $2 "x" $3 "-" $6 "#"}}\'');
                    //$hdddddd = $ssh->exec('df -k | grep "^/dev" | awk \'{print $2 "x" $3 "-" $1 "#"}\'');
                    $hdddddd = $ssh->exec('df -k $(df | grep -e "^/" |cut -d" " -f1 | while read candidato; do extraible=$(lsblk -no RM $candidato); [ $extraible -eq 0 ] && echo $candidato; done) | tail -n+2 | awk \'{print $2 "x" $3 "-" $6 "#"}\'');
		    $hdddddd = array_filter(explode("#", $hdddddd));
                    unset($hdddddd[count($hdddddd)-1]);
                    $totalhdd = 0;
                    $usedhdd = 0;
                    for ($i = 0; $i < count($hdddddd); $i++){
                        $totalhdd += intval(explode("x", $hdddddd[$i])[0]);
                        $usedhdd += intval(explode("-", explode("x", $hdddddd[$i])[1])[0]);
                    }
                    $hdd = array(0 => $totalhdd, 1 => $usedhdd, 2 => $totalhdd-$usedhdd);
                    //echo "HDD total: " . substr(strrev($hdd[4]), 0, -1) . "GB HDD usado: " . substr(strrev($hdd[3]), 0, -1) . "GB HDD libre: " .substr(strrev($hdd[2]), 0, -1) . "GB";
                    //res = $ssh->exec("awk '/cpu /{print 100*($2+$4)/($2+$4+$5)}' /proc/stat");
                    //$procU = preg_replace('/\s+/', '', $res);
                    $res = $ssh->exec("lscpu |grep 'CPU MHz:'");
                    //echo "<br/>";
                    $procV = preg_replace('/\s+/', '',explode(": ", $res)[1]);
                    //echo "Velocidad del procesador: " . $procV . "MHz" ;
                    // $res = $ssh->exec("top -bn2 | grep \"Cpu(s)\" | sed \"s/.*, *\([0-9.]*\)%* id.*/\1/\" | awk '{print 100 - $1\"%\"}' | tail -n 1");
                    // $procU = substr_replace($res, "", -2);
                    //$res = $ssh->exec("top -b -n2 -p 1 | fgrep \"Cpu(s)\" | tail -1 | awk -F'id,' -v prefix=\"\$prefix\" '{ split($1, vs, \",\"); v=vs[length(vs)]; sub(\"%\", \"\", v); printf \"%s%.1f\", prefix, 100 - v }'");
                    //$res = $ssh->exec("top -d 0.5 -b -n2 | grep \"Cpu(s)\"|tail -n 1 | awk '{print $2 + $4}'");
                    $res = $ssh->exec('sep="."; if [ ${LANGUAGE:0:2} == "es" ]; then sep=","; fi; top -bn3 -d0"$sep"5 | grep "%Cpu(s):" | tail -n1 | awk \'{print $2 + $4}\';');
		            //$res = $ssh->exec('echo $( top -bn1 | grep "%Cpu(s):" | tr -s " " | cut -d" " -f8 | tr "," "." )');
		    //$res = $ssh->exec("cat <(grep 'cpu ' /proc/stat) <(sleep 0.5 && grep 'cpu ' /proc/stat) | awk -v RS=\"\" '{print ($13-$2+$15-$4)*100/($13-$2+$15-$4+$16-$5)}'");
		    //$res = 0;
                    $procU = $res;
		            //$procU = 100-intval($res);
                    //echo "<br/>";
                    //echo "Uso del procesador: " . $procU . "%";
                    $retn = array(0 => $mem[0], 1 => $mem[1], 2 => intval($mem[0])-intval($mem[1]) , 3 => $hdd[0], 4 => $hdd[1], 5 => $hdd[2], 6 => $procU, 7 => $procV);
                     //$retn = array(0 => $mem[0], 1 => $mem[1], 2 => $mem[2], 3 => substr(strrev($hdd[4]), 0, -1), 4 => substr(strrev($hdd[3]), 0, -1), 5 => substr(strrev($hdd[2]), 0, -1), 6 => $procU, 7 => $procV);
                    //0 => RAM total, 1 => RAM usada, 2 => RAM libre, 3 => HDD total, 4=> HDD usado, 5 => HDD libre, 6 => Uso procesador, 7 => Velocidad procesador
                    $stmt = $db->prepare('SELECT month(`exec_date`) as month, count(`exec_date`) as visits from server_history_hm WHERE serverid = :serverid GROUP BY month(`exec_date`) ORDER by month ASC');
                    $stmt->bindParam(':serverid', $serverid, PDO::PARAM_INT);
                    if($stmt->execute()){
                        $fetch = $stmt->fetchAll();
                        $f = 0;
                        for($i = 0; $i < 7; $i++){
                            if(getMonth()[$i] == $fetch[$f]["month"]){
                                array_push($retn, $fetch[$f++]["visits"]);
                            }else{
                                array_push($retn, 0);
                            }
                                    
                        }
                    }
                    $stmt = $db->prepare('SELECT * from user_prop_hm WHERE serverid = :serverid AND userid = :userid');
                    $stmt->bindParam(':serverid', $serverid, PDO::PARAM_INT);
                    $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
                    if($stmt->execute()){
                        $row = $stmt->fetch(PDO::FETCH_ASSOC);
                        array_push($retn, $row["refresh_time"]);
                        array_push($retn, $row["hdd_status"]);
                        array_push($retn, $row["ram_status"]);
                        array_push($retn, $row["proc_status"]);
                        array_push($retn, $row["web_alert"]);
                        array_push($retn, $row["email_alert"]);
                        array_push($retn, $row["telegram_alert"]);
                    }
                    //Pushing every hdd partition info
                    array_push($retn,$hdddddd);
                    echo json_encode($retn);
                    unset($ssh);
                    insertStaticsIntoDB($serverid, $retn);
                }
            }
        }
    }

    function insertStaticsIntoDB($servId, $props){
        set_include_path('..' . DIRECTORY_SEPARATOR);
        include("db.php");
        $stmt = $db->prepare('INSERT INTO server_history_hm (serverid, exec_date, total_ram, used_ram, free_ram, total_hdd, used_hdd, free_hdd, used_proc, speed_proc) VALUES(:serverid, now(), :total_ram, :used_ram, :free_ram, :total_hdd, :used_hdd, :free_hdd, :used_proc, :speed_proc)');
        $stmt->bindParam(':serverid', $servId, PDO::PARAM_INT);
        $stmt->bindParam(':total_ram', $props[0], PDO::PARAM_STR);
        $stmt->bindParam(':used_ram', $props[1], PDO::PARAM_STR);
        $stmt->bindParam(':free_ram', $props[2], PDO::PARAM_STR);
        $stmt->bindParam(':total_hdd', $props[3], PDO::PARAM_STR);
        $stmt->bindParam(':used_hdd', $props[4], PDO::PARAM_STR);
        $stmt->bindParam(':free_hdd', $props[5], PDO::PARAM_STR);
        $stmt->bindParam(':used_proc', $props[6], PDO::PARAM_STR);
        $stmt->bindParam(':speed_proc', $props[7], PDO::PARAM_STR);
        $stmt->execute();
    }
    function getMonth(){
        $months = [];
        for ($i = 0; $i < 7; $i++) {
            array_push($months, intval(date('n', strtotime("-$i month"))));
        }
        return array_reverse($months);
    }
?>

