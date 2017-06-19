<?php
    session_name('hmssid');
    session_set_cookie_params(3600,"/");
    session_start();
    if(!isset($_SESSION["login"])){
        header("Location: https://hypermanager.net/login");
    }
    function getServers(){
      include("lib/db.php");
      $userid = $_SESSION["userid"];
      $liformat = '<li class="dcjq-parent-li" id="serverid_%1$d"><a onclick="showServerInfo(this);" href="javascript:;" serverid="%1$d"><i class="fa fa-desktop"></i><span>%2$s</span></a> <i onclick="editServer(\'serverid_%1$d\');" class="fa fa-pencil edit-btn"></i><i onclick="deleteServer(\'serverid_%1$d\');" class="fa fa-times delete-btn"></i></li>';
      $sql = "SELECT id, ipaddress, alias FROM servers_hm WHERE userid = $userid";
      foreach ($db->query($sql) as $row) {
        if ($row['alias'] == null)
          echo sprintf($liformat, $row['id'], strtoupper($row['ipaddress']));
        else
           echo sprintf($liformat, $row['id'], strtoupper($row['alias']));
      }
    }
    function getMonth(){
      $months = [];
      for ($i = 0; $i < 7; $i++) {
        array_push($months, strtoupper(date('M', strtotime("-$i month"))));
      }
      return array_reverse($months);
    }
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Monitorizador de recursos en servidores Unix">
    <meta name="author" content="Iván del Cura">
    <meta name="keyword" content="Monitorizar, Ssh, Ubuntu, Recursos, Responsive, HTML5, CSS3">

    <title>Hypermanager</title>
    <link rel="icon" href="img/favicon.png">
    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.css" rel="stylesheet">
    <!--external css-->
    <link rel="stylesheet" type="text/css" href="css/font-awesome/css/font-awesome.css"/>
    <link rel="stylesheet" type="text/css" href="css/lineicons/style.css"/>    
    
    <!-- Custom styles for this template -->
    <link href="css/style.css" rel="stylesheet">
    <link href="css/main.css" rel="stylesheet">
    <link href="css/style-responsive.css" rel="stylesheet">

    <script src="js/chart-master/Chart.js"></script>
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>

  <section id="container" >
      <!-- **********************************************************************************************************************************************************
      TOP BAR CONTENT & NOTIFICATIONS
      *********************************************************************************************************************************************************** -->
      <!--header start-->
      <header class="header black-bg">
              <div class="sidebar-toggle-box">
                  <div class="fa fa-bars tooltips" data-placement="right" data-original-title="Cambiar modo de navegación"></div>
              </div>
            <!--logo start-->
            <a href="" class="logo"><b>Hyper Manager</b></a>
            <!--logo end-->
            <div class="nav notify-row" id="top_menu">
                <!--  notification start -->
                <ul class="nav top-menu">
                    <!-- inbox dropdown start-->
                    <li id="header_inbox_bar" class="dropdown">
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                            <i class="fa fa-envelope-o"></i>
                            <span class="badge bg-theme" id="badge-count">0</span>
                        </a>
                        <ul class="dropdown-menu extended inbox" id="notification-dropdown">
                            <div class="notify-arrow notify-arrow-green"></div>
                            <li>
                                <p class="green" id="notifications-count">Tienes 0 notificaciones sin leer</p>
                            </li>
                        </ul>
                    </li>
                    <!-- inbox dropdown end -->
                </ul>
                <!--  notification end -->
            </div>
            <div class="top-menu">
                <ul class="nav pull-right top-menu">
                    <li><a class="logout" id="logout">Salir</a></li>
                </ul>
            </div>
        </header>
      <!--header end-->
      
      <!-- **********************************************************************************************************************************************************
      MAIN SIDEBAR MENU
      *********************************************************************************************************************************************************** -->
      <!--sidebar start-->
      <aside>
          <div id="sidebar"  class="nav-collapse ">
              <!-- sidebar menu start-->
              <ul class="sidebar-menu" id="nav-accordion">
              
                  <p class="centered"><a href="#"><img src="img/ui-admin.jpg" class="img-circle" width="60"></a></p>
                  <h5 class="centered"><?php echo $_SESSION["username"]; ?></h5>
                  <li><!-- class="mt" -->
                      <a data-toggle="modal" data-target="#add-server-modal" id="a-add-server">
                          <i class="fa fa-plus"></i>
                          <span>Añadir servidor</span>
                      </a>
                  </li>
                  <?php
                    getServers();
                  ?>
              </ul>
              <!-- sidebar menu end-->
          </div>
      </aside>
      <!--sidebar end-->
      
      <!-- **********************************************************************************************************************************************************
      MAIN CONTENT
      *********************************************************************************************************************************************************** -->
      <!--main content start-->
      <section id="main-content">
          <section class="wrapper">
          <div class="col-lg-12 row" id="landing-page">
              <div class="col-lg-12">
                <div class="row mtbox-landing">
                    <div class="col-md-12 col-sm-12 first-info">
                      <div >

                        <h3 id="add-server-landing-info"><i class="fa fa-arrow-left" aria-hidden="true"></i> Añade un servidor desde el panel de la izquierda para continuar</h3>
                    </div>

                </div><div class="col-md-12 col-sm-12 second-info">
                <div >

                    <h4 class="select-server-landing-info"><i class="fa fa-arrow-left" aria-hidden="true"></i> Selecciona un servidor de la lista y empieza a monitorizar sus recursos</h4>
                </div>

                </div><div class="col-md-12 col-sm-12 second-info">
                <div >

                    <h4 class="select-server-landing-info"><i class="fa fa-pencil" aria-hidden="true"></i> Pulsando encima del icono podremos actualizar la información de nuestro servidor.</h4>
                </div>

            </div><div class="col-md-12 col-sm-12 second-info last-info">
            <div >

                <h4 class="select-server-landing-info"><i class="fa fa-times" aria-hidden="true"></i> Pulsando encima del icono eliminaremos de nuestra cuenta el servidor que corresponda con el mismo.</h4>
            </div>

        </div></div>
        <!-- /row -->




        <!-- /row --> 

        <div class="row">





            <div class="col-md-12 col-sm-12 first-info">
              <div>

                <h3 id="add-server-popup-info">Requisitos mínimos</h3>
            </div>

        </div><div class="col-md-12 col-sm-12 second-info">
        <div>

            <h4 class="select-server-landing-info"><i class="fa fa-minus" aria-hidden="true"></i><b> Versión mínima de SO:: </b>Ubuntu 14.04 LTS, Debian 7.0, Centos 7.0</h4>
        </div>

    </div><div class="col-md-12 col-sm-12 second-info">
    <div>


      <h4 class="select-server-landing-info"><i class="fa fa-minus" aria-hidden="true"></i><b> Usuario-Servidor: </b>Únicamente tiene que tener acceso al interprete de ordenes Bash </h4></div>

  </div><div class="col-md-12 col-sm-12 second-info">
  <div>

    <h4 class="select-server-landing-info"><i class="fa fa-minus" aria-hidden="true"></i><b> Distribución-Cliente: </b>Cualquiera que sea capaz de ejecutar un navegador soportado en su versión recomendada</h4>
</div>

</div>
<div class="col-md-12 col-sm-12 second-info">
  <div>

    <h4 class="select-server-landing-info"><i class="fa fa-minus" aria-hidden="true"></i><b> Servicio: </b>Servidor SSH activo.</h4>
</div>

</div><div class="col-md-12 col-sm-12 second-info last-info">
<div>

    <h4 class="select-server-landing-info"><i class="fa fa-minus" aria-hidden="true"></i><b> Navegador web: </b>Chrome (58.0.3029.110) Mozilla(53.0.3) Edge (38.14393.1066.0). Se recomiendan sus últimas versiones.</h4>
</div>

</div></div><div class="row">





<div class="col-md-12 col-sm-12 first-info">
  <div>

    <h3 id="add-server-popup-info">Información necesaria para añadir un servidor</h3>
</div>

</div><div class="col-md-12 col-sm-12 second-info">
<div>

    <h4 class="select-server-landing-info"><i class="fa fa-minus" aria-hidden="true"></i><b> Dirección IP: </b>Dirección IP o nombre DNS de nuestro servidor SSH</h4>
</div>

</div><div class="col-md-12 col-sm-12 second-info">
<div>


  <h4 class="select-server-landing-info"><i class="fa fa-minus" aria-hidden="true"></i><b> Usuario SSH: </b>Nombre de usuario que se utiliza para acceder al servidor remotamente. (No hace falta que tenga permisos especiales).</h4></div>

</div><div class="col-md-12 col-sm-12 second-info">
<div>

    <h4 class="select-server-landing-info"><i class="fa fa-minus" aria-hidden="true"></i><b> Contraseña SSH: </b>Contraseña del usuario ssh elegidos.</h4>
</div>

</div><div class="col-md-12 col-sm-12 second-info ">
<div>

    <h4 class="select-server-landing-info"><i class="fa fa-minus" aria-hidden="true"></i><b> Alias servidor: </b>Nombre optativo que mostrará el servidor en el menu desplegable. (Si se deja vacio mostrará la dirección ip).</h4>
</div>

</div></div></div><!-- /col-lg-9 END SECTION MIDDLE -->
</div>
            <div class="col-lg-12 row" id="server-show">
                  <div class="col-lg-12 main-chart">
                    <div class="row mtbox">
                      <div class="col-md-4 col-sm-4">
                        <div class="col-md-12 col-sm-12 box0">
                          <div class="box1">
                            <span><i class="fa fa-server" aria-hidden="true"></i></span>
                            <h3 id="server-status">{0}</h3>
                          </div>
                          <p id="more-server-status">El servidor esta {0}</p>
                        </div>
                        <div class="col-md-12 mb">
                          <div class="darkblue-panel pn" id="ss01">
                            <div class="darkblue-header">
                              <h5>USO DEL PROCESADOR</h5>
                            </div><canvas class="server-chart" id="serverstatus01" height="150" width="150" style="width: 120px; height: 120px;"></canvas>
                                      <p id="proc-chart-today"></p>
                                      <footer>
                                          <div class="pull-left">
                                              <h5 id="proc-chart-used"><i class="fa fa-clock-o"></i> {0} GHz</h5>
                                          </div>
                                          <div class="pull-right">
                                              <h5 id="proc-chart-perc">{0}% En uso</h5>
                                          </div>
                                      </footer>
                                    </div><!-- /darkblue panel -->
                                  </div></div><div class="col-md-4 col-sm-4"><div class="col-md-12 col-sm-12 box0">
                                  <div class="box1">
                                    <span><i class="fa fa-microchip" aria-hidden="true"></i></span>
                                    <h3 id="ram-status">{0}{1} / {2}{3}</h3>
                                  </div>
                                  <p id="more-ram-status">{0}{1} de {2}{3} ram usada del servidor</p>
                                </div><div class="col-md-12 mb">
                                <!-- RAM PANEL -->
                                <div class="darkblue-panel pn" id="ss02">
                                  <div class="darkblue-header">
                                    <h5>USO DE LA RAM</h5>
                                  </div><canvas class="server-chart" id="serverstatus02" height="150" width="150" style="width: 120px; height: 120px;"></canvas>
                                      <p id="ram-chart-today"></p>
                                      <footer>
                                          <div class="pull-left">
                                              <h5 id="ram-chart-used"><i class="fa fa-microchip"></i> {0} {1}</h5>
                                          </div>
                                          <div class="pull-right">
                                              <h5 id="ram-chart-perc">{0}% En uso</h5>
                                          </div>
                                      </footer>
                                    </div><!-- /darkblue panel -->
                                  </div></div>



                                  <div class="col-md-4 col-sm-4"><div class="col-md-12 col-sm-12 box0">
                                    <div class="box1">
                                      <span><i class="fa fa-hdd-o" aria-hidden="true"></i></span>
                                      <h3 id="hdd-status">{0}{1} / {2}{3}</h3>
                                    </div>
                                      <p id="more-hdd-status">Usados de HDD {0}{1} de {2}{3}</p>
                                  </div><div class="col-md-12 mb">
                                  <!-- HDD PANEL -->
                                  <div class="darkblue-panel pn tooltips" id="ss03" data-placement="top" data-original-title="Pincha para +info">
                                    <div class="darkblue-header">
                                      <h5>USO DEL DISCO</h5>
                                    </div><canvas class="server-chart" id="serverstatus03" height="150" width="150" style="width: 120px; height: 120px;"></canvas>
                                      <p id="hdd-chart-today"></p>
                                      <footer>
                                          <div class="pull-left">
                                              <h5 id="hdd-chart-used"><i class="fa fa-hdd-o"></i> {0} {1}</h5>
                                          </div>
                                          <div class="pull-right">
                                              <h5 id="hdd-chart-perc">{0}% En uso</h5>
                                          </div>
                                      </footer>
                                    </div><!-- /darkblue panel -->
                                  </div></div>                
                                </div>
                    <div class="row">
                      <!-- SERVER STATUS PANELS -->
                        <div class="col-md-4 mb">
                            <div class="darkblue-panel pn">
                                <div class="darkblue-header">
                                    <h5>Actualización de datos</h5>
                                </div>
                                <form id="refresh-time">
                                  <input type="number" name="rtime" id="rtime" min="5" class="form-control" placeholder="Tiempo de refresco">
                                </form>
                                <footer class="accept-footer">
                                    <div class="pull-center">
                                        <button id="refresh-time-btn" type="button" class="btn btn-default pull-center">Comenzar</button>
                                        <button id="stop-time-btn" type="button" class="btn btn-default pull-center">Parar</button>
                                        <button id="refresh-server-btn" type="button" class="btn btn-default pull-center">Actualizar</button>
                                    </div>
                                </footer>
                            </div><!-- /darkblue panel -->
                        </div><!-- /col-md-4 -->     
                        <div class="col-md-4 mb">
                            <div class="darkblue-panel pn">
                                <div class="darkblue-header">
                                    <h5>Seleccion de notificaciones</h5>
                                </div>
                                <form id="how-notification">
                                  <div class="text-box tooltips" data-placement="right" data-original-title="Disco duro">
                                    <input type="number" name="hdd" id="hdd" min="0" class="form-control" placeholder="Porcentaje de aviso del disco" >
                                  </div>
                                  <div class="text-box tooltips" data-placement="right" data-original-title="Memoria ram">
                                    <input type="number" name="ram" id="ram" min="0" class="form-control" placeholder="Porcentaje de aviso de la ram" >
                                  </div>
                                  <div class="text-box tooltips" data-placement="right" data-original-title="Procesador">
                                    <input type="number" name="proc" id="proc" min="0" class="form-control" placeholder="Porcentaje de aviso del procesador" >
                                  </div>
                                </form>
                                <footer class="accept-footer">
                                  <div class="pull-center">
                                        <button type="button" id="how-notification-btn" class="btn btn-default pull-center">Aceptar</button>
                                    </div>
                                </footer>
                            </div><!-- /darkblue panel -->
                        </div><!-- /col-md-4 -->     
                        <div class="col-md-4 mb">
                            <div class="darkblue-panel pn">
                                <div class="darkblue-header">
                                    <h5>Tipo de notificación</h5>
                                </div>
                                <form id="where-notification">
                                  <div class="checkbox">
                                    <label><input type="checkbox" name="web" id="web" value="1">Notificar via web</label>
                                  </div>
                                  <div class="text-box">
                                    <input type="text" class="form-control" name="useremail" id="useremail" placeholder="Dirección de email">
                                  </div>
                                  <div class="text-box">
                                    <input type="text" class="form-control" maxlength="18" name="tgusername" id="tgusername" placeholder="ID de Telegram">
                                    <a href="https://t.me/userinfobot" target="_blank" id="getidtg">¿Id de telegram?</a>
                                  </div>
                                </form>
                                <footer class="accept-footer">
                                    <div class="pull-center">
                                        <button type="button" id="where-notification-btn" class="btn btn-default pull-center">Aceptar</button>
                                    </div>
                                </footer>
                            </div><!-- /darkblue panel -->
                        </div><!-- /col-md-4 -->     
                    </div><!-- /row -->
                    
                                    
                    <div class="row" id="userinfo-alert" hidden="true"></div>
                    
                    <div class="row">
                      <!--CUSTOM CHART START -->
                      <div class="border-head">
                          <h3>Consultas al servidor</h3>
                      </div>
                      <div class="custom-bar-chart">
                          <ul class="y-axis" id="chart-bar-col-values">
                              <li><span>250</span></li>
                              <li><span>200</span></li>
                              <li><span>150</span></li>
                              <li><span>100</span></li>
                              <li><span>50</span></li>
                              <li><span>0</span></li>
                          </ul>
                          <div class="bar" id="bar0">
                              <div class="title"><?php echo getMonth()[0]; ?></div>
                              <div class="value tooltips" data-original-title="85" data-toggle="tooltip" data-placement="top">85%</div>
                          </div>
                          <div class="bar" id="bar1">
                              <div class="title"><?php echo getMonth()[1]; ?></div>
                              <div class="value tooltips" data-original-title="50" data-toggle="tooltip" data-placement="top">50%</div>
                          </div>
                          <div class="bar" id="bar2">
                              <div class="title"><?php echo getMonth()[2]; ?></div>
                              <div class="value tooltips" data-original-title="60" data-toggle="tooltip" data-placement="top">60%</div>
                          </div>
                          <div class="bar" id="bar3">
                              <div class="title"><?php echo getMonth()[3]; ?></div>
                              <div class="value tooltips" data-original-title="45" data-toggle="tooltip" data-placement="top">45%</div>
                          </div>
                          <div class="bar" id="bar4">
                              <div class="title"><?php echo getMonth()[4]; ?></div>
                              <div class="value tooltips" data-original-title="32" data-toggle="tooltip" data-placement="top">32%</div>
                          </div>
                          <div class="bar" id="bar5">
                              <div class="title"><?php echo getMonth()[5]; ?></div>
                              <div class="value tooltips" data-original-title="62" data-toggle="tooltip" data-placement="top">62%</div>
                          </div>
                          <div class="bar" id="bar6">
                              <div class="title"><?php echo getMonth()[6]; ?></div>
                              <div class="value tooltips" data-original-title="75" data-toggle="tooltip" data-placement="top">75%</div>
                          </div>
                      </div>
                      <!--custom chart end-->
                    </div><!-- /row --> 
                    
                  </div><!-- /col-lg-9 END SECTION MIDDLE -->
              </div>
          </section>
      </section>
      <!--main content end-->
      <!--footer start-->
      <footer class="site-footer">
          <div class="text-center">
              2017 - hypermanager.net
              <a href="#" class="go-top">
                  <i class="fa fa-angle-up"></i>
              </a>
          </div>
      </footer>
      <!--footer end-->
  </section>
    <div class="loading-modal"></div>
    <div id="add-server-modal" class="modal fade" role="dialog">
      <div class="modal-dialog modal-sm ">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title" id="add-edit-server"></h4>
          </div>
          <div class="modal-body">
            <form id="add-server">
              <input type="text" class="form-control input-sm" name="ipaddress" id="ipaddress" placeholder="Dirección IP">
              <input type="text" class="form-control input-sm" name="username" id="username" placeholder="Usuario SSH">
              <input type="password" class="form-control input-sm" name="password"  placeholder="Contraseña SSH">
              <input type="text" class="form-control input-sm" name="alias" id="alias" maxlength="16" placeholder="Alias servidor">
              
              <div id="server-response" class="form-control" hidden="true"></div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default transparent-btn" id="add-server-btn">Aceptar</button>
            <button type="button" class="btn btn-default transparent-btn" id="close-add-server-btn" data-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>
    <div id="hdd-moreinfo-modal" class="modal fade" role="dialog">
      <div class="modal-dialog modal-sm ">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Información de disco</h4>
          </div>
          <div class="modal-body">
            <select class="form-control" id="hdd-select-info"></select>
            <div id="hdd-moreinfo-footer">
              <div class="pull-left">
                <h5 id="hdd-more-chart-used"><i class="fa fa-hdd-o"></i> {0}/{1} {2}</h5>
              </div>
              <div class="pull-right">
                <h5 id="hdd-more-chart-perc">{0}% En uso</h5>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default transparent-btn" id="close-hdd-moreinfo-btn" data-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>
    <!-- js placed at the end of the document so the pages load faster -->
    <script src="js/jquery.js"></script>
    <script src="js/jquery-1.8.3.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script class="include" type="text/javascript" src="js/jquery.dcjqaccordion.2.7.js"></script>
    <script src="js/jquery.scrollTo.min.js"></script>
    <script src="js/jquery.nicescroll.js" type="text/javascript"></script>
    <script src="js/jquery.sparkline.js"></script>


    <!--common script for all pages-->
    <script src="js/common-scripts.js"></script>
    <!--script for this page-->
    <script src="js/sparkline-chart.js"></script>    
    <!-- <script src="js/zabuto_calendar.js"></script> -->
    <script src="js/main.js"></script>
    
  </body>
</html>
