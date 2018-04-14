//
// jquery.sessionTimeout.js
//
// After a set amount of time, a dialog is shown to the user with the option
// to either log out now, or stay connected. If log out now is selected,
// the page is redirected to a logout URL. If stay connected is selected,
// a keep-alive URL is requested through AJAX. If no options is selected
// after another set amount of time, the page is automatically redirected
// to a timeout URL.
//
//
// USAGE
//
//   1. Include jQuery
//   2. Include jQuery UI (for dialog)
//   3. Include jquery.sessionTimeout.js
//   4. Call $.sessionTimeout(); after document ready
//
//
// OPTIONS
//
//   message
//     Text shown to user in dialog after warning period.
//     Default: 'Your session is about to expire.'
//
//   keepAliveUrl
//     URL to call through AJAX to keep session alive
//     Default: 'keepAlive.asp'
//
//   redirUrl
//     URL to take browser to if no action is take after warning period
//     Default: 'timedOut.asp'
//
//   logoutUrl
//     URL to take browser to if user clicks "Log Out Now"
//     Default: 'logout.asp'
//
//   warnAfter
//     Time in milliseconds after page is opened until warning dialog is opened
//     Default: 900000 (15 minutes)
//
//   redirAfter
//     Time in milliseconds after page is opened until browser is redirected to redirUrl
//     Default: 1200000 (20 minutes)
//
(function( $ ){
	jQuery.sessionTimeout = function( options ) {
		var defaults = {
			message      : 'Your session is about to expire.',
			keepAliveUrl : '/ajax/keep_alive.php',
			redirUrl     : '/ajax/logout.php',
			logoutUrl    : '/ajax/logout.php',
			warnAfter    :  3000000, // 50 minutes
			redirAfter   :  3600000  // 60 minutes
		}
		
		// Extend user-set options over defaults
		var o = defaults
		if ( options ) { var o = $.extend( defaults, options ); };
		
		$('body').on('click', '.keep_me_logged_in', function(){
			$.fancybox.close();
					
			$.ajax({
				type: 'POST',
				url: o.keepAliveUrl
			});
			
			// Stop redirect timer and restart warning timer
			controlRedirTimer('stop');
			controlDialogTimer('start');
		})

		$('body').on('click', '.log_me_out', function(){
			logout("");
		})

		
		function controlDialogTimer(action){
			switch(action) {
				case 'start':
					// After warning period, show dialog and start redirect timer
					dialogTimer = setTimeout(function(){
						controlRedirTimer('start');
						$('#sessionTimeout-dialog').dialog('open');
						$.fancybox.open({
				            padding: 20,
				            width: 1000,
				            type: 'ajax',
				            href: '/components/logout_expiry_dialog.php',
				            modal: true
			            });
					}, o.warnAfter);
					break;
				
				case 'stop':
					clearTimeout(dialogTimer);
					break;
			}
		}
		
		function controlRedirTimer(action){
			switch(action) {
				case 'start':
					// Dialog has been shown, if no action taken during redir period, redirect
					redirTimer = setTimeout(function(){
						logout("Your login session has expired.");
					}, o.redirAfter - o.warnAfter);
					break;
				
				case 'stop':
					clearTimeout(redirTimer);
					break;
			}
		}

		function logout(message){
			$.ajax({
		        type:"POST",
		        url:"/ajax/logout.php",
		        cache: false,
		        headers: { "cache-control": "no-cache" },
		        success: function(data){
		        	localStorage.logout_message = message;
					window.location.reload();
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) { 
					console.log("Data could not be retrieved");
					return false;
				}
			});
		}
		
		// Begin warning period
		controlDialogTimer('start');
	};
})( jQuery );