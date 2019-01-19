<!DOCTYPE html>

<?php 
    include_once($_SERVER["DOCUMENT_ROOT"]."/config/config.php"); 

    $error_msg = filter_input(INPUT_GET, 'error', $filter = FILTER_SANITIZE_STRING);
    $success_msg = filter_input(INPUT_GET, 'success', $filter = FILTER_SANITIZE_STRING);

    if (login_check($dbConnected) == true) {
        $loggedin = true;
        $_SESSION['last_activity'] = time();
        $_SESSION['expire_time'] = 60*60; //1hour timeout
    } else {
        $loggedin = false;
    }


    if (isset($_GET['url_k']) && $_GET['url_k'] != "") {
    //Key present
    $supplied_url_key = strtolower($_GET['url_k']);

    //Assemble URL;
    $url_val = "http".(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on" ? "s" : "")."://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

    //Now remove key from URL
    $url_val = preg_replace('/([?&])url_k=[^&]+(&|$)/','',$url_val);
    $generated_url_key = hash('sha512',$url_val);
      if ($supplied_url_key != $generated_url_key) {
          //Potential URL manipulation, redirect to error page
        include($_SERVER["DOCUMENT_ROOT"]."/views/redirect_page.php");
          // die("<h2>Error accessing page</h2>");
      }
    } else {
        //No key
        if ($thermox->enforce_key_url_validation) {
            die("<h2>Error accessing page</h2>");
        }
    }
?>

<html>
  <head>
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta charset="utf-8">
    <meta content="IE=edge" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-status-bar-style" content="black"/>
    <link rel="shortcut icon" type="image/png" href="images/thermomix.ico"/>
    
    <script src="js/jquery.js"></script>
    <script src="js/jquery-ui.js"></script>
    
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="css/font-awesome.css">
    <link rel="stylesheet" type="text/css" href="css/timeline.css">
    <link rel="stylesheet" type="text/css" href="css/timeline-style-responsive.css">
    <link rel="stylesheet" type="text/css" href="css/styles.css">
    <link rel="stylesheet" type="text/css" href="css/toastr.css">
    <link href="css/yamm.css" rel="stylesheet">

    <script src="js/bootstrap.min.js"></script>
    <script src="js/bootbox.min.js"></script>
    <script src="js/bootbox.js"></script>
    
    <script src="js/components/core-min.js"></script>
    <script src="js/rollups/sha512.js"></script>
    <script src="js/sha512.js"></script>
    <script src="js/rollups/aes.js"></script>
    <script src="js/components/enc-base64-min.js"></script>
    <script src="js/components/enc-utf16-min.js"></script>
    
    <script src="js/scripts.js"></script>
    
    <!--HICHARTS-->
    <script src="libraries/Highcharts-5.0.10/code/highcharts.js"></script>

    <!-- DATATABLES -->
    <link rel="stylesheet" type="text/css" href="libraries/DataTables/datatables.css"/>
    <script type="text/javascript" src="libraries/DataTables/datatables.min.js"></script>

    <!-- FANCYBOX -->
    <script type="text/javascript" src="plugins/source/jquery.fancybox.pack.js"></script>
    <script type="text/javascript" src="plugins/source/jquery.fancybox.js"></script>
    <link rel="stylesheet" href="plugins/source/jquery.fancybox.css" type="text/css" media="screen" />
    
    <script src="js/jquery.sessionTimeout.js"></script>
    
    <!-- MASK -->
    <script type="text/javascript" src="plugins/igorescobar-jQuery-Mask-Plugin-535b4e4/jquery.mask.js"></script>
    
    <!-- TIMELINE -->
    <script src="js/modernizr.js"></script> <!-- Modernizr -->

    <!-- Block UI -->
    <script src="js/blockUI.js"></script> 

    <!-- toastr -->
    <script src="js/toastr.js"></script>


<?php
    if ($loggedin) {
?>

  <script type="text/javascript">
    $(function(){
      // $.sessionTimeout({ warnAfter: 100, redirAfter: 300000 });
    });
  </script>
    <title>Thermomix Middle East Online System</title>

  </head>

  <body>

     <div class="row info-bar hidden-print" style="width:100%;z-index:999;">
        <div class="row">
          <div class="container">
            <div class="navbar yamm" style="margin-bottom:0px;">
                <div class="pull-left">
                    <ul class="nav navbar-nav" style="padding-left:40px;">
                        <li style="line-height:50px;width:100px;">
                            <img src="images/thermomix.png" style="width:48px;"/>
                        </li>
                        <li class="dropdown">
                            <a href="welcome_screen" class="search dropdown-toggle <?php if($_GET['view'] == 'welcome_screen') { ?>active<?php } ?>" id="home" title="Home"><span class="glyphicon glyphicon-home"></span></a>
                        </li>
                        <li class="dropdown">
                          <a href="#" class="ignore search dropdown-toggle" title="Search" data-toggle="dropdown"><span class="glyphicon glyphicon-search"></span></a>
                          <ul class="dropdown-menu" style="width: 80%">
                            <li>
                              <div style="padding: 20px 30px;">
                                <div class="row">
                                  <div class="input-group" style="padding-top:20px;padding-bottom:20px;" >
                                      <input type="text" id="main-search"  style="height:36px" class="col-md-12 form-control" id="item-search" placeholder="Search for user or customer via name or machine serial"></input>
                                      <span class="input-group-btn">
                                          <button class="btn btn-default" style="height:36px;" id="btn-tm5-search" type="submit"><i class="fa fa-search"></i></button>
                                      </span>
                                  </div>
                                </div>
                              </div>
                            </li>
                          </ul>
                        </li>
                      <?php 
                        if(isset($_SESSION['selected_person'])) {
                          $selected_person = json_decode($_SESSION['selected_person'],true);
                      ?>
                        <li>
                            <a href="timeline" class="dropdown-toggle <?php if($_GET['view'] == 'timeline' && $_GET['type'] != 'muser') {?> active <?php } ?>" id="timeline-tab" title="Timeline"><span class="glyphicon glyphicon-time"></span></a>
                        </li>
                      <?php
                        }
                      ?>
                        <li>
                            <a href="timeline&type=muser" class="dropdown-toggle <?php if($_GET['view'] == 'timeline' && $_GET['type'] == 'muser') {?> active <?php } ?>"  title="My Timeline" ><span class="glyphicon glyphicon-dashboard"></span></a>             
                        </li>
                        <li>
                            <a href="#" class="ignore status-data dropdown-toggle" id="add-customer-dialogue" title="Add Customer" class="ignore" data-toggle="dropdown"><span class="glyphicon glyphicon-plus"></span></a>
                        </li>
                      <?php if (strstr($_SERVER['HTTP_USER_AGENT'], 'iPad')) { ?>
                        <li class="dropdown">
                           <a href="#" class="ignore dropdown-toggle" data-toggle="dropdown" title="View Additional Tiles" ><span class="glyphicon glyphicon-option-vertical"></span></a>
                            <ul class="dropdown-menu">
                            <?php if(isset($_SESSION["user"]["user_type"]) && $_SESSION["user"]["user_type"] == 1) { ?>
                              <li>
                                  <a href="add_user" class="<?php if($_GET['view'] == 'add_user') { ?>active<?php } ?>" id="user-panel" title="Add User" ><span class="glyphicon glyphicon-user"></span></a>
                              </li>
                            <?php } ?>
                              <li>
                                  <a href="inventory" class="<?php if($_GET['view'] == 'inventory') { ?>active<?php } ?>" id="stock-menu" title="Inventory" ><span class="glyphicon glyphicon-tasks"></span></a>          
                              </li>
                              <li>
                                  <a href="#" class="ignore" id="reports-dialogue" title="Reports"><span class="glyphicon glyphicon-stats"></span></a>
                              </li>   
                            </ul>
                        </li>
                      <?php } else { ?>
                         <?php if(isset($_SESSION["user"]["user_type"]) && $_SESSION["user"]["user_type"] == 1) { ?>
                              <li>
                                  <a href="add_user" class="<?php if($_GET['view'] == 'add_user') { ?>active<?php } ?>" id="user-panel" title="Add User" ><span class="glyphicon glyphicon-user"></span></a>
                              </li>
                            <?php } ?>
                              <li>
                                  <a href="inventory" class="<?php if($_GET['view'] == 'inventory') { ?>active<?php } ?>" id="stock-menu" title="Inventory" ><span class="glyphicon glyphicon-tasks"></span></a>          
                              </li>
                              <li>
                                  <a href="#" class="ignore" id="reports-dialogue" title="Reports"><span class="glyphicon glyphicon-stats"></span></a>
                              </li> 
                      <?php } ?>
                     
                      <?php if(isset($_SESSION["user"]["user_type"]) && $_SESSION["user"]["user_type"] == 1) { ?>
                        <!-- <li class="dropdown">
                          <a href="#" class="ignore search dropdown-toggle" title="Reports" data-toggle="dropdown"><span class="glyphicon glyphicon-stats"></span></a>
                          <ul class="dropdown-menu" style="width: 80%">
                            <li>
                              <div style="padding: 20px 30px;">
                                <div class="row">
                                  <?php// include_once($_SERVER["DOCUMENT_ROOT"]."/views/reports.php"); ?>
                                </div>
                              </div>
                            </li>
                          </ul>
                        </li>   -->
                      <?php } ?>
                    </ul>
                </div>
                <div class="pull-right">
                    <ul class="nav navbar-nav">
                        <li class="dropdown-notify" style="margin-right: 10px">
                          <a href="#" class="ignore dropdown-toggle" title="Notifications" data-toggle="dropdown"><span class="glyphicon glyphicon-bell"></span><span class="badge badge-notify"></span></a>
                          <ul class="dropdown-menu notifications">
                            <li>
                            <?php include($_SERVER["DOCUMENT_ROOT"]."/ajax/notifications_panel.php"); ?>
                            </li>
                          </ul>
                        </li>  
                        <li style="line-height: 20px; padding-bottom:0px; padding-top:3px;">
                          <span class="glyphicon glyphicon-user" style="vertical-align:middle; "></span> <?php if(isset($_SESSION['user']['username'])){echo $_SESSION['user']['username'];} ?> 
                            <button type="button" class="btn btn-default logout" type= "logout" id="logout" style="margin-left:40px; margin-right:40px; padding: 13px;"><span class="glyphicon glyphicon-log-out"></span></button>   
                        </li>
                    </ul>
                </div>
              </div>
            </div>
        </div>
        <?php if($_GET["view"] == 'timeline') { ?>
        <div class="row hidden-print" style="background-color:#F3F3F3;padding:5px 0px; color:#333;border-bottom:1px solid lightgray;">
            <div class="container" id="timeline-filters">
                <div style="height:55px;text-align:center;">
                    <div class="row timeline-filters">
                        <div class="col-xs-3">
                            <p style="margin-bottom:5px;">TM5 Purchase</p>
                            <div class="checkbox-switch">
                                <input id="cd-purchase_timeline_filter" class="cmn-toggle cmn-toggle-tm5-purchase" type="checkbox" data-filter-val="purchase" checked>
                                <label for="cd-purchase_timeline_filter"></label>
                            </div>
                        </div>
                         <div class="col-xs-3">
                            <p style="margin-bottom:5px;">TM5 Repair</p>
                            <div class="checkbox-switch">
                                <input id="cd-repair_timeline_filter" class="cmn-toggle cmn-toggle-tm5-repair" type="checkbox" data-filter-val="repair" checked>
                                <label for="cd-repair_timeline_filter"></label>
                            </div>
                        </div>
                        <div class="col-xs-3">
                            <p style="margin-bottom:5px;">Purchase Books/Parts</p>
                            <div class="checkbox-switch">
                                <input id="cd-books-parts_timeline_filter" class="cmn-toggle cmn-toggle-books-parts" type="checkbox" data-filter-val="books_parts" checked>
                                <label for="cd-books-parts_timeline_filter"></label>
                            </div>
                        </div>  
                        <div class="col-xs-3" id="stats-view-container" style="padding:0;"> 
                            <div class="btn-group btn-group-sm" data-toggle="buttons" style="margin-top:13px;">
                                <a class="btn btn-success" id="stats-view" <?php if($_GET["type"] == "muser") { ?> data-id="<?=$_SESSION['user']['user_id']?>" <?php } else { ?> data-id="<?=$selected_person['id']?>" <?php } ?>>Stock View</a>
                            </div>
                        </div>
                        <div class="col-xs-3 hidden" id="timeline-view-container" style="padding:0;"> 
                            <div class="btn-group btn-group-sm" data-toggle="buttons" style="margin-top:13px;">
                                <a class="btn btn-success" id="timeline-view" <?php if(isset($_GET['type'])) { ?> data-type="<?=$_GET['type']?>" <?php } ?>>Timeline View</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
      </div>
            
          <?php
            if(isset($_SESSION["user"]["user_id"])) {
          ?>

            <script type="text/javascript">
              $(function(){
                getUserNotifications();
              });
            </script>

          <?php
            }
          ?>

          <?php
            if(isset($_GET["action"])) {
          ?>
            <script type="text/javascript">
              $(function(){
                $.blockUI({ message: '<h1>Loading</h1>' });
                execAction(<?=$_GET["action"]?>,<?=$_GET["acid"]?>,<?=$_GET["uid"]?>,<?=$_GET["nid"]?>);
              });
            </script>

          <?php
            }
          ?>
          
            <div class="container output" style="padding-top:70px;">
              <div class="row outer-row ">
                  <?php
                    if($loggedin) {
                      if(isset($_GET['view'])) {
                        $view_details = $thermox->getViewDetails($_GET['view']);
                        include_once("views/".$view_details['data']['view_name'].".php");
                      } else {
                        include_once("views/welcome_screen.php"); 
                       // echo '<div class="container output"><div class="row"><div class="col-xs-12 text-center"><div class="in_dev"></div></div></div></div>';
                      }
                    } else {
                      include("views/login.php");
                    }
                  ?>
              </div>
            </div>
  </body>


  <?php } else if(isset($_GET['reg']) && $_GET['reg'] != '') {
            include($_SERVER["DOCUMENT_ROOT"]."/views/activate_account.php");
        } else if(isset($_GET['ref']) && $_GET['ref'] != '') {
            include($_SERVER["DOCUMENT_ROOT"]."/views/password_reset.php");
        } else {
          include($_SERVER["DOCUMENT_ROOT"]."/views/login.php");
  } ?>
  <footer>
    <span id="siteseal"><script async type="text/javascript" src="https://seal.godaddy.com/getSeal?sealID=kQVERi7ZqDGwWSmEAtrwVo6KQkSRXHCP0aoAOKvCKotmYT3fHZgr4cun6ZwK"></script></span>
  </footer>
</html>

