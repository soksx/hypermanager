<html>
    <head>
        <title>HyperManager</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" href="img/favicon.png">
        <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="css/login.css">
        <script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
        <script type="text/javascript" src="js/bootstrap.min.js"></script>
    </head>
    <body id="large-header" class="large-header">
        <canvas id="connect-panel"></canvas>
        <!-- <h1 class="main-title">{StaticsHeader} <span class="thin">{staticsinfo}</span></h1> -->
        <div class="login-div">
            <form id="login-form" method="POST" action="https://hypermanager.net/login">
            <div class="container"> 
                <div class="input-group">
                  <span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>    
                  <input type="text" name="user" class="form-control transparent-input" placeholder="Usuario">
                </div>
                <div class="input-group">
                  <span class="input-group-addon"><span class="glyphicon glyphicon-ban-circle"></span></span>
                  <input type="password" name="pass" class="form-control transparent-input" placeholder="Contraseña">
                </div>
                <div id="login-response" hidden="true"></div>
                <div class="checkbox">
                    <label>
                      <input type="checkbox" name="cbRemember"> Recuérdame
                    </label>
                </div>
                <button type="submit" class="btn btn-default transparent-btn" id="login-btn">Entrar</button>
                <button type="button" data-toggle="modal" data-target="#register-modal" class="btn btn-default transparent-btn" id="register-btn">Registro</button>
                <?php
                    session_name('hmssid');
                    session_set_cookie_params(3600,"/");
                    session_start();
                    if(isset($_SESSION["login"])){
                        header("Location: https://hypermanager.net/main");
                    }
                    if($_SERVER['REQUEST_METHOD'] === 'POST'){
                        include("lib/security.php"); //Incluimos la clase que obtiene datos criticos de la DB.
                        $sec = new Sec; //Instanciamos la clase como objeto.
                        $user = $_POST['user']; //Definimos el usuario como el resultado de $_POST['user']
                        $userinfo = $sec->getUserInfo($user); //Asignamos el resultado de la función a una variable
                        $upass = $_POST['pass']; //Definimos la contraseña como el resultado de $_POST['pass']
                        $dbpass = $userinfo['pass'];
                        if ($dbpass === crypt($upass, $dbpass)){ //compara que la contraseña obtenida sea igual a la contraseña envryptada
                            if(isset($_POST['cbRemember'])){ //comprobamos que el checkbox de "Recordar usuario" esté marcado
                            $cbRemember = $_POST['cbRemember'];
                            if ($cbRemember == "on"){
                                $params = session_get_cookie_params();
                                setcookie(session_name(), $_COOKIE[session_name()], time() + 60*60*24*30, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
                            }
                        }
                        $_SESSION["login"] = true;
                        $_SESSION["userid"] = $userinfo['id'];
                        $_SESSION["username"] = $userinfo['user'];
                        header("Location: https://hypermanager.net/main");
                        }else{
                            echo "<script>document.getElementById('login-response').style.height = '30px';document.getElementById('login-response').style.background = '#d9534f';document.getElementById('login-response').innerHTML = 'Error: Login incorrecto';</script>"; 
                        }
                    }

                ?>
            </div>
            </form>
        </div>
        <div id="register-modal" class="modal fade" role="dialog">
		    <div class="modal-dialog modal-sm ">
		      <!-- Modal content-->
		      <div class="modal-content">
		        <div class="modal-header">
		          <button type="button" class="close" data-dismiss="modal">&times;</button>
		          <h4 class="modal-title">Registro</h4>
		        </div>
		        <div class="modal-body">
		          <form id="add-user">
		            <div class="input-group register-group">
	                  <span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>    
	                  <input type="text" id="ruser" name="ruser" class="form-control transparent-input" placeholder="Usuario">
	                </div>
	                <div class="input-group register-group">
	                  <span class="input-group-addon"><span class="glyphicon glyphicon-envelope"></span></span>    
	                  <input type="text" id="remail" name="remail" class="form-control transparent-input" placeholder="Correo">
	                </div>
		            <div class="input-group register-group">
	                  <span class="input-group-addon"><span class="glyphicon glyphicon-ban-circle"></span></span>    
	                  <input type="password" id="rpassword" name="rpassword" class="form-control transparent-input" placeholder="Contraseña">
	                </div>
		            <div class="input-group register-group">
	                  <span class="input-group-addon"><span class="glyphicon glyphicon-ban-circle"></span></span>    
	                  <input type="password" id="rpassword2" name="rpassword2" class="form-control transparent-input" placeholder="Repite contraseña">
	                </div>
		            <div id="server-response" class="form-control" hidden="true"></div>
		          </form>
		        </div>
		        <div class="modal-footer">
		          <button type="button" class="btn btn-default transparent-btn" id="add-user-btn">Registrar</button>
		          <button type="button" class="btn btn-default transparent-btn" id="close-add-user-btn" data-dismiss="modal">Cerrar</button>
		        </div>
		      </div>
		    </div>
		</div>
    </body>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/1.19.1/TweenMax.min.js"></script>
    <script type="text/javascript" src="js/login.js"></script>
</html>
