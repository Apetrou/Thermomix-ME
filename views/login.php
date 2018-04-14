 
<?php
    include_once($_SERVER["DOCUMENT_ROOT"]."/config/config.php"); 
?>


<body class="view-bg">
<div class="container" style="margin-top:40px;">
    <div class="login-container">
        <h2 style="margin-bottom:40px;">zLogin</h2>
        <div class="message"></div>
        <form id="login">
            <label for="username">Username</label>
            <input type="text" name="username" id="username" class="form-control form-spacer"/>
            <label for="password">Password</label>
            <input type="password" name="password" id="password" class="form-control form-spacer"/>
            <label for="email" style="display: none;">Email address</label>
            <input type="text" name="email" id="email" class="form-control form-spacer" value="" style="display: none;"/>
        </form>
            <div class="login-button-container pull-right">
                <button class="btn btn-default btn-md send-password-email" style="display: none;"><span class="glyphicon glyphicon-envelope" style="margin-right:5px;"></span> Send Email</button>
                <button class="btn btn-default btn-md login"><span class="glyphicon glyphicon-log-in" style="margin-right:5px;"></span> Login</button>
            </div>
            <div class="clearfix"></div>
       
        <a href="#" id="forgot-password" class="ignore" style = "font-size:15px; color:#fff; margin-top:10px">Forgot Password?</a>
        <div id="error" style = "font-size:14px; color:#ff0a0a; margin-top:10px"></div>
    </div>
</body>

  
    <script type="text/javascript">

    $(function() {
            
                   
        // POSITION LOGIN AREA ***********************************************************************************************
        positionLoginBox();

        window.onresize = function(event) {
            positionLoginBox();
        }

        function positionLoginBox() {

            $('.login-container').css({
                position: 'absolute',
                left: ($(window).width() - $('.login-container').outerWidth()) / 2,
                top: ($(window).height() - $('.login-container').outerHeight()) / 2
            });

        }

        // FOCUS THE USERNAME FIELD ***************************************************************************************
        $("#username").focus();

    });

    </script>
</div>

