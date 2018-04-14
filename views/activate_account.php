<body class="view-bg">
<div class="container" style="margin-top:40px;">
    <div class="login-container">
        <h2 style="margin-bottom:40px;"><img src="images/thermomix.png" style="width:80px;margin-right:15px;vertical-align:bottom;"/>Tm-me</h2>
        <div class="message">Please choose a password for your Account</div>
        <form id="activate_account">
            <label for="activate_password">Password</label>
            <input type="password" name="activate_password" id="activate_password" class="form-control form-spacer" dropdown="false"/>
            <label for="activate_password">Re-confirm password</label>
            <input type="password" name="activate_password_confirm" class="form-control form-spacer" dropdown="false"/>
            <input type="hidden" name="identifier" value="<?=$_GET['reg']?>"/> 
        </form>
            <div class="login-button-container pull-right">
                <button class="btn btn-default btn-md activate-account"><span class="glyphicon glyphicon-ok" style="margin-right:5px;"></span> Activate</button>
            </div>
            <div class="clearfix"></div>
    
        <div id="error" style = "font-size:14px; color:#ff0a0a; margin-top:10px"></div>
    </div>
</body>

  
    <script type="text/javascript">

    $(function() {
        
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

    });

    </script>
</div>

