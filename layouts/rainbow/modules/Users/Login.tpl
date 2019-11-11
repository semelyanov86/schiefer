{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************}
{* modules/Users/views/Login.php *}

{strip}
	<style>
		body {
			background-position: center;
			width: 100%;
			height: 100%;
			background-repeat: repeat;
			background-image: url('layouts/rainbow/modules/Users/resources/circles.png');
		}

		body:before { background-color: #464961; }

		body:before { content: "";    opacity: .3;}

		body:before { position: absolute;    z-index: -1;    height: 100%;    width: 100%;    display: block;}
		
		hr {
		    margin-top: 15px;
			background-color: #7C7C7C;
			height: 2px;
			border-width: 0;
		}
		h3, h4 {
			margin-top: 0px;
		}
		hgroup {
			text-align:center;
			margin-top: 4em;
		}
		input {
			font-size: 16px;
			padding: 10px 10px 10px 0px;
			-webkit-appearance: none;
			display: block;
			color: #636363;
			width: 100%;
			border: none;
			border-radius: 4px;
			border-bottom: 1px solid #757575;
		}
		input:focus {
			outline: none;
		}
		label {
			font-size: 16px;
			font-weight: normal;
			position: absolute;
			pointer-events: none;
			left: 0px;
			top: 10px;
			transition: all 0.2s ease;
		}
		input:focus ~ label, input.used ~ label {
			top: -20px;
			transform: scale(.75);
			left: -12px;
			font-size: 18px;
		}
		input:focus ~ .bar:before, input:focus ~ .bar:after {
			width: 50%;
		}
		#page {
			padding-top: 0%;
		}
		
		
		.marketingDiv {
			color: #303030;
			padding: 10px 20px;
		}
		.separatorDiv {
			background-color: #7C7C7C;
			width: 2px;
			height: 460px;
			margin-left: 20px;
		}
		.user-logo {
			height: 110px;
			margin: 0 auto;
			padding-top: 40px;
			padding-bottom: 20px;
		}
		.blockLink {
			border: 1px solid #303030;
			padding: 3px 5px;
		}
		.group {
			position: relative;
			margin: 20px 20px 40px;
		}
		.failureMessage {
			color: red;
			display: block;
			text-align: center;
			padding: 0px 0px 10px;
		}
		.successMessage {
			color: green;
			display: block;
			text-align: center;
			padding: 0px 0px 10px;
		}
		.inActiveImgDiv {
			padding: 5px;
			text-align: center;
			margin: 30px 0px;
		}
		.app-footer p {
			margin-top: 0px; text-align: center;
    background: rgba(0,0,0,0.6)!important;
    margin-bottom: 0;
    padding: 4px 0;
    color: #fff;
    font-size: 10px;
    border: none;
    position: fixed;
    bottom: 0;
		}
		.footer {
			background-color: #fbfbfb;
			height:26px;
		}
		.bar {
			position: relative;
			display: block;
			width: 100%;
		}
		.bar:before, .bar:after {
			content: '';
			width: 0;
			bottom: 1px;
			position: absolute;
			height: 1px;
			background: #35aa47;
			transition: all 0.2s ease;
		}
		.bar:before {
			left: 50%;
		}
		.bar:after {
			right: 50%;
		}
		.button {
			position: relative;
			display: inline-block;
			padding: 16px 26px;
			margin: .3em 0 1em 0;
			/*width: 100%;*/
			vertical-align: middle;
			color: #fff;
			font-size: 15px;
			line-height: 20px;
			-webkit-font-smoothing: antialiased;
			text-align: center;
			letter-spacing: 1px;
			background: transparent;
			border: 0;
			border-radius: 50px;
			cursor: pointer;
			transition: all 0.15s ease;
		}
		.button:focus {
			outline: 0;
		}
		.buttonPurple {
	color: #fff;
    background-color: #5c6bc0;
    border-color: #5c6bc0;
		}
		
		.ripples {
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			overflow: hidden;
			background: transparent;
		}
		.input-group {
			margin-bottom: 10px;
		}
		.bg {
    	background-repeat: no-repeat;
   	 	background-size: cover;
    	background-position: center center;}
    	.img-caption {
    position: absolute;
    bottom: 0px;
    left: 0px;
    padding: 40px;
    max-width: 600px;
}		
.full-height {
	min-height: 100vh;
}

.caption-title {
    color: #ffffff;
    font-size: 35px;
    font-weight: 300;
}
.caption-text {
    color: #e6e6e6;
}
.bg-white {
    background-color: #ffffff8c !important;
}
p {
    font-family: Roboto, -apple-system, system-ui, BlinkMacSystemFont, "Segoe UI", "Helvetica Neue", Arial, sans-serif;
    color: #fff;
    line-height: 1.9;
}
.login-footer {
    width: 100%;
}
.purple{ color:#5c6bc0;}
.purple:hover { color: #3a4896; }
footer { display:none;}
.inline-block {    display: inline-block !important;}
.form-control {
        height: 40px;
    font-size: 14px;
    border-radius: 90px;
    padding: 26px 20px!important;
    background-color: rgba(0,0,0,.04)!important;
    border-color: #e8e8e8;}
.form-control::placeholder {
  color: white;}

		//Animations
		@keyframes inputHighlighter {
			from {
				background: #4a89dc;
			}
			to 	{
				width: 0;
				background: transparent;
			}
		}
		@keyframes ripples {
			0% {
				opacity: 0;
			}
			25% {
				opacity: 1;
			}
			100% {}
				width: 200%;
				padding-bottom: 200%;
				opacity: 0;
			}
		}
div > .loginDiv { margin-top:20%!important; }
.loginDiv { padding:3rem!important; margin-top:20%!important; border-radius: 10px;}
h3 { color:#428bca }
	</style>
	<!-- Jquery Core Js --> 




	<span class="app-nav"></span>
	<div class="main-container">
		<div class="row">


          <div class="col-lg-4 col-lg-offset-4 col-md-4 col-sm-4 col-xs-12" style="z-index:999999999999;">
			<div class="loginDiv bg-white">
				<div class="vertical-align ">
				<div class="login-heading">
                    <img class="img-responsive user-logo" src="layouts/v7/resources/Images/vtiger.png">
                </div>

				<div>
					<span class="{if !$ERROR}hide{/if} failureMessage" id="validationMessage">{$MESSAGE}</span>
					<span class="{if !$MAIL_STATUS}hide{/if} successMessage">{$MESSAGE}</span>
				</div>
<h3>Login</h3>
<p class="mrg-btm-15 font-size-13">Please enter your user name and password to login</p>
				<div id="loginFormDiv">
					<form method="POST" action="index.php" >
						<input type="hidden" name="module" value="Users"/>
						<input type="hidden" name="action" value="Login"/>
<div class="form-group">
<label class="sr-only" for="inlineFormInputGroup">Username</label>
    <input type="text" class="form-control" id="inlineFormInputGroup username" placeholder="Username" name="username" >
</div>
<div class="form-group">
<label class="sr-only" for="inlineFormInputGroup">Password</label>
    <input type="password" class="form-control" id="inlineFormInputGroup password" placeholder="Password" name="password" >
	</div>
<div class="text-center"><button type="submit" class="button buttonPurple">Sign in</button></div>
							
							<a class="purple forgotPasswordLink pull-right">Forgot password?</a>
							
					</form>
				</div>

				<div id="forgotPasswordDiv" class="hide">
					<form  action="forgotPassword.php" method="POST" >
<div class="form-group">
<label class="sr-only" for="inlineFormInputGroup">Username</label>
    <input type="text" class="form-control" id="username" placeholder="Username" name="username">
</div>

<div class="form-group">
<label class="sr-only" for="inlineFormInputGroup">Email</label>
    <input type="email" class="form-control" id="inlineFormInputGroup" placeholder="Email" name="emailId">
	</div>
<div class="text-center">
							<button type="submit" class="button buttonPurple forgot-submit-btn">Submit</button></div>
							<a class="purple forgotPasswordLink pull-right">Back</a>

					</form>
				</div>


			</div><!--vertical align-->
			<div class="login-footer text-center">
            <p>Made with <i class="fa fa-heart"></i> by MakeYourCloud</p>
                            </div>

		</div>
</div>
		</div>


		<script>
			jQuery(document).ready(function () {
				var validationMessage = jQuery('#validationMessage');
				var forgotPasswordDiv = jQuery('#forgotPasswordDiv');

				var loginFormDiv = jQuery('#loginFormDiv');
				loginFormDiv.find('#password').focus();

				loginFormDiv.find('a').click(function () {
					loginFormDiv.toggleClass('hide');
					forgotPasswordDiv.toggleClass('hide');
					validationMessage.addClass('hide');
				});

				forgotPasswordDiv.find('a').click(function () {
					loginFormDiv.toggleClass('hide');
					forgotPasswordDiv.toggleClass('hide');
					validationMessage.addClass('hide');
				});

				loginFormDiv.find('button').on('click', function () {
					var username = loginFormDiv.find('#username').val();
					var password = jQuery('#password').val();
					var result = true;
					var errorMessage = '';
					if (username === '') {
						errorMessage = 'Please enter valid username';
						result = false;
					} else if (password === '') {
						errorMessage = 'Please enter valid password';
						result = false;
					}
					if (errorMessage) {
						validationMessage.removeClass('hide').text(errorMessage);
					}
					return result;
				});

				forgotPasswordDiv.find('button').on('click', function () {
					var username = jQuery('#forgotPasswordDiv #username').val();
					var email = jQuery('#email').val();

					var email1 = email.replace(/^\s+/, '').replace(/\s+$/, '');
					var emailFilter = /^[^@]+@[^@.]+\.[^@]*\w\w$/;
					var illegalChars = /[\(\)\<\>\,\;\:\\\"\[\]]/;

					var result = true;
					var errorMessage = '';
					if (username === '') {
						errorMessage = 'Please enter valid username';
						result = false;
					} else if (!emailFilter.test(email1) || email == '') {
						errorMessage = 'Please enter valid email address';
						result = false;
					} else if (email.match(illegalChars)) {
						errorMessage = 'The email address contains illegal characters.';
						result = false;
					}
					if (errorMessage) {
						validationMessage.removeClass('hide').text(errorMessage);
					}
					return result;
				});
				jQuery('input').blur(function (e) {
					var currentElement = jQuery(e.currentTarget);
					if (currentElement.val()) {
						currentElement.addClass('used');
					} else {
						currentElement.removeClass('used');
					}
				});

				var ripples = jQuery('.ripples');
				ripples.on('click.Ripples', function (e) {
					jQuery(e.currentTarget).addClass('is-active');
				});

				ripples.on('animationend webkitAnimationEnd mozAnimationEnd oanimationend MSAnimationEnd', function (e) {
					jQuery(e.currentTarget).removeClass('is-active');
				});
				loginFormDiv.find('#username').focus();
			});
		</script>
		</div>
	{/strip}