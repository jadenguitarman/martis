<!DOCTYPE html>
<html>
	<head>
		<title>Login</title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link href="https://fonts.googleapis.com/css?family=Fira+Sans+Condensed|Raleway:400,700&display=swap" rel="stylesheet">
		<link rel='icon' href='/favicon.ico?h=<?=hash_file('sha256', realpath(__DIR__."\\public\\favicon.ico"))?>' type='image/x-icon'>
		<style>
			* {
				font-family: Raleway, sans-serif;
			}

			body {
				min-height: 100vh;
				width: 100vw;
				margin: 0;
				background: linear-gradient(0deg, rgba(0, 0, 80, 0.7), rgba(0, 0, 80, 0.9)), url("/images/ductwork.jpg");
				background-position: center;
				background-size: cover;
				background-repeat: no-repeat;
				display: flex;
				align-items: center;
				justify-content: center;
			}

			main {
				width: 90vw;
				max-width: 450px;
				height: calc(500px + 18vh);
				background: #f8f8f8;
				border-radius: 15px;
				margin: 0;
				box-sizing: border-box;
				box-shadow: #000 2px 2px 22px;
				overflow: hidden;
				position: relative;
			}

			@media (min-height:600px) {
				main {
					max-height: 90vh;
				}
			}

			#logo {
				max-width: 100%;
				max-height: calc(15% - 10px);
				position: absolute;
				left: 50%;
				transform: translateX(-50%);
				top: 5px;
			}

			section {
				width: 100%;
				height: 85%;
				position: absolute;
				bottom: 0;
				transition: left 0.5s;
				padding: 5px 15px;
				box-sizing: border-box;
				display: flex;
				align-items: center;
				justify-content: space-evenly;
				flex-direction: column;
			}

			h1 {
				text-align: center;
				margin: 0;
			}

			.active_section {
				left: 0;
			}

			.to_right {
				left: 100%;
			}

			.to_left {
				left: -100%;
			}

			.error, .required {
				margin: 0;
				width: 100%;
				color: red;
				text-align: center;
				font-weight: 700;
			}

			.wrong-form {
				text-decoration: none;
				width: 100%;
				font-weight: 700;
				font-style: italic;
				cursor: pointer;
				color: #111;
			}

			.container {
				display: flex;
				align-items: center;
				justify-content: space-between;
				flex-direction: column;
				width: 100%;
			}

			.form-field {
				display: flex;
				width: 100%;
				position: relative;
				justify-content: center;
				text-align: center;
			}

			.form-field.split-field {
				justify-content: space-between;
			}

			.split-field > .form-field ~ .form-field {
				margin-left: 10px;
			}

			.form-field > input[type=text],
			.form-field > input[type=password],
			.form-field > select {
				width: 100%;
				height: 28px;
				outline: none;
				padding: 4px;
				line-height: 20px;
				font-size: 18px;
				font-family: 'Fira Sans Condensed', sans-serif;
				border-radius: 5px;
				border: 2px solid #aaa;
				box-shadow: #444 2px 2px 5px;
				box-sizing: border-box;
				background: #fff;
				margin-bottom: 13px;
			}

			.form-field > select {
				padding-top: 1px;
			}

			.form-field > label {
				color: #555;
				position: absolute;
				top: 5px;
				left: 5px;
				pointer-events: none;
				font-style: italic;
				font-size: 16px;
				text-align: left;
				font-weight: 700;
				transition: 0.3s;
			}

			.form-field > input:not(:placeholder-shown) + label,
			.form-field > input:focus + label {
				color: darkslategrey;
				top: -10px;
				left: 0;
				font-size: 11px;
			}

			.form-field > input:-webkit-autofill:focus + label {
				color: darkslategrey;
				top: -10px;
				left: 0;
				font-size: 11px;
			}

			.form-submit {
				height: 40px;
			}

			.form-submit > button {
				width: 100%;
				background: #0d0d4f;
				border: 0;
				outline: 0;
				margin: 0;
				padding: 0;
				height: 100%;
				border-radius: 15px;
				color: #fff;
				font-family: "Fira Sans Condensed";
				font-size: 20px;
				cursor: pointer;
			}

			section > .form-field.back {
				justify-content: left !important;
			}

			section > .form-field.back > .wrong-form {
				margin-bottom: 20px;
				width: fit-content;
			}

			#login > .container {
				height: 80%;
			}

			#tool_choose > .container {
				height: 40%;
			}

			.choose_container {
				width: 90%;
				display: flex;
				padding-bottom: 10px;
			}

			.choose_container > div {
				display: flex;
				width: 100%;
			}

			.choose_container label {
				padding: 10px 0;
				cursor: pointer;
				border-right: 2px solid #ccc;
				display: flex;
				flex-grow: 1;
				justify-content: center;
			}

			#login_choose_container label:first-of-type {
				border-radius: 5px 0px 0px 5px;
			}

			#login_choose_container label:last-of-type {
				border-radius: 0px 5px 5px 0px;
				border-right: 0;
			}

			.choose_container input[type=radio] {
				display: none;
			}

			#register_choose_container label {
				color: #111;
				border: 0;
				position: relative;
				z-index: 1;
				border-radius: 5px;
				font-weight: 700;
			}

			#login_choose_container label,
			#register_choose_container input[type=radio]:checked + label {
				color: white;
				background: #0d0d4f;
				font-weight: 300;
			}

			#state_field {
				width: 110px;
			}

			#zip_field {
				width: 170px;
			}

			section > span {
				font-size: 10px;
				text-align: center;
				margin: 10px 0;
			}

			#password_icon {
				height: 14px;
				position: absolute;
				top: 7px;
				right: 10px;
				width: 20px;
				overflow: visible;
				cursor: pointer;
				z-index: 1;
			}

			#password_cross_out {
				transition: opacity 0.15s;
			}

			#multi_user_table {
				flex: 1;
				margin: 10px 0;
				overflow-y: auto;
			}

			#multi_user_table > table {
				width: 100%;
				border-collapse: collapse;
				border-radius: 10px;
				overflow: hidden;
			}

			#multi_user_table > table a {
				text-decoration: underline;
				font-style: italic;
				cursor: pointer;
			}

			#multi_user_table > table > thead > tr {
				background: #333;
				height: 35px;
			}

			#multi_user_table > table > thead > tr > th {
				font-size: 18px;
				color: #fff;
				line-height: 1.2;
				font-weight: unset;
			}

			#multi_user_table > table > tbody > tr {
				font-size: 15px;
				height: 25px;
			}

			#multi_user_table > table > tbody > tr:nth-child(even) {
				background-color: rgba(255, 255, 255, 0.7);
			}

			#multi_user_table > table > tbody > tr > td:nth-child(2) {
				cursor: text;
			}

			#multi_user_table > table > tbody > tr > td > .disabled {
				background: none;
				outline: none;
				border: none;
				padding: 0;
				margin: 0;
				text-align: center;
				font-size: 15px;
			}

			#multi_user_explanation {
				margin-top: 25%;
			}

			.invisible {
				position:absolute!important;
				opacity: 0;
				width: 0;
				height: 0;
				border: 0;
				padding: 0;
				margin: 0;
				pointer-events: none;
			}

			#one_user_registration_program_notice, #multi_user_explanation {
				text-align: center;
				font-size: 14px;
			}

			#register_multi_submit {
				font-size: 16px;
				padding: 1px 10px;
			}

			#register_multi_submit span {
				font-family: "Fira Sans Condensed";
			}
		</style>
	</head>
	<body>
		<main>
			<?php if (!!$affiliation["affiliation_code"]) { ?>
				<img id="logo" src="/images/logos/<?=$affiliation["logo"]?>" alt="<?=$affiliation["company_name"]?>">
			<?php } else { ?>
				<img id="logo" src="/images/logos/eds_logo.png" alt="Energy Design Systems">
			<?php } ?>
			<section id="login" data-title="Login" class="<?php
				if ($show_page == "login") {
					echo "active_section";
				} else if ($show_page == "forgot_password") {
					echo "to_right";
				} else {
					echo "to_left";
				}
			?>">
				<div class="container">
					<h1>Log In to EDS Design Tools</h1>

					<div class="form-field">
						<input type="text" name="email" placeholder=" " form="login_form">
						<label>Email Address</label>
					</div>

					<div class="form-field">
						<input type="password" name="password" placeholder=" " form="login_form">
						<label>Password</label>
					</div>

					<p class="error"><?php
						if (isset($_SESSION["login_error"]) && !!$_SESSION["login_error"]) {
							echo $_SESSION["login_error"];
							unset($_SESSION["login_error"]);
						}
					?></p>

					<div class="form-field form-submit">
						<button id="login_submit">Log In</button>
					</div>

					<div class="form-field">
						<a class="wrong-form" onclick="move_section('forgot_password')">Forgot Password</a>
						<a class="wrong-form" onclick="move_section('register')">Register</a>
					</div>
				</div>
			</section>

			<section id="tool_choose" data-title="Choose Your Tool" class="to_right">
				<div class="container">
					<h1>Which tool do you want to access?</h1>
					<div id="login_choose_container" class="choose_container"></div>
				</div>
			</section>

			<section id="register" data-title="Register" class="<?php
				if ($show_page == "register") {
					echo "active_section";
				} else {
					echo "to_right";
				}
			?>">
				<?php if (!!$affiliation["allow_program_choice"]) { ?>
					<div class="form-field">
						<div id="register_choose_container" class="choose_container">
							<input id="radio_lc_register" type="radio" data-program="Load Calculator" name="program" value="calculator_14_yearly" form="set_order_form" <?=$calc_checked?>>
							<label for="radio_lc_register">Load Calculator</label>

							<input id="radio_auditor_register" type="radio" data-program="Home Auditor" name="program" value="auditor_14_yearly" form="set_order_form" <?=$auditor_checked?>>
							<label for="radio_auditor_register">Home Auditor</label>

							<input id="radio_both_register" type="radio" data-program="Load Calculator and Home Auditor package" name="program" value="calculator_14_yearly auditor_14_yearly" form="set_order_form" <?=$both_checked?>>
							<label for="radio_both_register">Both</label>
						</div>
					</div>
				<?php } ?>

				<div class="form-field">
					<input type="text" name="company_name" placeholder=" " form="set_order_form" required>
					<label>Company Name<span class="required">*</span></label>
				</div>
				<div class="form-field">
					<input type="text" name="address_1" placeholder=" " form="set_order_form" required>
					<label>Street Address 1<span class="required">*</span></label>
				</div>
				<div class="form-field">
					<input type="text" name="address_2" form="set_order_form" placeholder=" ">
					<label>Street Address 2</label>
				</div>
				<div class="form-field split-field">
					<div class="form-field">
						<input type="text" name="city" placeholder=" " form="set_order_form" required>
						<label>City<span class="required">*</span></label>
					</div>
					<div class="form-field" id="state_field">
						<select name="state" form="set_order_form" required>
							<option value="" selected disabled hidden></option>
							<option>AL</option>
							<option>AK</option>
							<option>AZ</option>
							<option>AR</option>
							<option>CA</option>
							<option>CO</option>
							<option>CT</option>
							<option>DE</option>
							<option>DC</option>
							<option>FL</option>
							<option>GA</option>
							<option>HI</option>
							<option>ID</option>
							<option>IL</option>
							<option>IN</option>
							<option>IA</option>
							<option>KS</option>
							<option>KY</option>
							<option>LA</option>
							<option>ME</option>
							<option>MD</option>
							<option>MA</option>
							<option>MI</option>
							<option>MN</option>
							<option>MS</option>
							<option>MO</option>
							<option>MT</option>
							<option>NE</option>
							<option>NV</option>
							<option>NH</option>
							<option>NJ</option>
							<option>NM</option>
							<option>NY</option>
							<option>NC</option>
							<option>ND</option>
							<option>OH</option>
							<option>OK</option>
							<option>OR</option>
							<option>PA</option>
							<option>RI</option>
							<option>SC</option>
							<option>SD</option>
							<option>TN</option>
							<option>TX</option>
							<option>UT</option>
							<option>VT</option>
							<option>VA</option>
							<option>WA</option>
							<option>WV</option>
							<option>WI</option>
							<option>WY</option>
						</select>
					</div>
					<div class="form-field" id="zip_field">
						<input type="text" name="zip" placeholder=" " form="set_order_form" required>
						<label>ZIP Code<span class="required">*</span></label>
					</div>
				</div>
				<div class="form-field">
					<input type="text" name="phone" placeholder=" " form="set_order_form" required>
					<label>Phone<span class="required">*</span></label>
				</div>
				<?php if ($affiliation["requires_approval"] && !$affiliation["typical_password"]) { ?>
					<div class="form-field">
						<input type="text" name="username" value=" " class="invisible" tabindex="-1"><!--hopefully force browser to offer to save password with empty email, to be determined later -->
						<svg viewBox="0 0 176.978 100.861" id="password_icon" preserveAspectRatio="none">
							<g transform="translate(-10.556 -129.274)">
								<circle cx="99.045" cy="201.036" r="29.099"/>
								<path d="M26.297 160.138c-8.638 11.386-15.015 23.981-15.741 37.921h29.54c4.906-25.603 35.313-42.374 58.398-42.538 23.194.371 53.983 17.058 59.509 42.733 6.191.105 22.227-.195 29.53-.195-.588-16.025-6.894-26.273-16.536-37.379-20.13-20.09-48.538-31.585-72.175-31.404-23.233.301-55.013 9.358-72.525 30.862z"/>
							</g>
							<g fill="none" id="password_cross_out" style="opacity:0;">
								<path stroke="#000" stroke-width="18.521" d="M-17.18-12.957l204.868 131.928h0"/>
								<path stroke="#fff" stroke-width="10.583" d="M-24.66-1.8l206.11 132.806h0"/>
							</g>
						</svg>
						<input name="password" type="password" placeholder=" " required form="set_order_form">
						<label>Password<span class="required_mark">*</span></label>
					</div>
				<?php } ?>
				<div class="form-field form-submit">
					<button id="one_order_submit">Register One User</button>
				</div>
				<div class="form-field form-submit">
					<button id="multi_order_submit">Register Multiple Users</button>
				</div>
				<span id="register_notice"><?=$affiliation["register_notice"]?></span>
				<div class="form-field">
					<a class="wrong-form" onclick="move_section('forgot_password')">Forgot Password</a>
					<a class="wrong-form" onclick="move_section('login')">Login</a>
				</div>
			</section>

			<section id="one_user_registration" data-title="Register A User" class="to_right">
				<div class="form-field back">
					<a class="wrong-form" onclick="move_section('register')">Back</a>
				</div>
				<p id="one_user_registration_program_notice"><?=$affiliation["register_notice"]?></p>
				<div class="form-field">
					<input type="text" name="first_name" placeholder=" " form="register_one_form" required>
					<label>First Name<span class="required">*</span></label>
				</div>
				<div class="form-field">
					<input type="text" name="last_name" placeholder=" " form="register_one_form" required>
					<label>Last Name<span class="required">*</span></label>
				</div>
				<div class="form-field">
					<input type="text" name="email" placeholder=" " form="register_one_form" required>
					<label>Email<span class="required">*</span></label>
					<input type="password" value=" " class="invisible" tabindex="-1"><!--hopefully force browser to offer to save email with empty password, to be determined later -->
				</div>
				<div class="form-field form-submit">
					<button id="register_one_submit">Register</button>
				</div>
			</section>

			<section id="multi_user_registration" data-title="Register Multiple Users" class="to_right">
				<div class="form-field back">
					<a class="wrong-form" onclick="move_section('register')">Back</a>
				</div>
				<div class="form-field split-field">
					<div class="form-field">
						<input type="text" name="first_name" placeholder=" " form="register_multi_form" required>
						<label>First Name<span class="required">*</span></label>
					</div>
					<div class="form-field">
						<input type="text" name="last_name" placeholder=" " form="register_multi_form" required>
						<label>Last Name<span class="required">*</span></label>
					</div>
				</div>
				<div class="form-field">
					<input type="text" name="email" placeholder=" " form="register_multi_form" required>
					<label>Email<span class="required">*</span></label>
				</div>
				<div class="form-field form-submit">
					<button id="add_multi_user">Add User</button>
				</div>
				<p id="multi_user_explanation">Start by entering the information of the person in charge of this account. When you click Add User, a table will appear here. Continue adding all of the users, then click Register. Click an email you've already added to edit it in place.</p>
				<div class="form-field" id="multi_user_table">
					<table>
						<thead style="display:none;">
							<tr>
								<th>Name</th>
								<th>Email</th>
								<th>&nbsp;</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
				<div class="form-field form-submit" id="register_multi_field" style="display: none;">
					<button id="register_multi_submit">Register for <span class="program_notice">Load Calculator at $10 per month billed annually</span> per user</button>
				</div>
			</section>

			<section id="forgot_password" data-title="Forgot Password" class="<?php
				if ($show_page == "forgot_password") {
					echo "active_section";
				} else {
					echo "to_left";
				}
			?>">
				<div class="form-field">
					<h2>Forgot Password?</h2>
				</div>
				<div class="form-field">
					<p>You can reset it by filling in the form below.</p>
				</div>
				<div class="form-field">
					<input type="text" name="email" placeholder=" " form="reset_password_form" required>
					<label>Email Address<span class="required">*</span></label>
				</div>
				<div class="form-field">
					<span id="reset_password_errors"><?php
						if (isset($_SESSION["reset_password_message"])) {
							echo $_SESSION["reset_password_message"];
							unset($_SESSION["reset_password_message"]);
						}
					?></span>
				</div>
				<div class="form-field form-submit">
					<button id="reset_password">Reset Password</button>
				</div>
				<div class="form-field">
					<a class="wrong-form" onclick="move_section('register')">Register</a>
					<a class="wrong-form" onclick="move_section('login')">Login</a>
				</div>
			</section>
		</main>

		<form id="login_form" action="/application/send_to_program" method="post" novalidate></form>
		<form id="set_order_form" novalidate>
			<?php if (!!$affiliation["affiliation_code"]) { ?>
				<input type="hidden" name="affiliation_code" value="<?=$affiliation["affiliation_code"]?>">
			<?php }
			if (!$affiliation["allow_program_choice"]) { ?>
				<input type="hidden" name="program" value="<?=$affiliation["affiliation_code"]?>">
			<?php } ?>
		</form>
		<form id="register_one_form" novalidate>
			<?php if (!!$affiliation["affiliation_code"]) { ?>
				<input type="hidden" name="affiliation_code" value="<?=$affiliation["affiliation_code"]?>">
			<?php } ?>
		</form>
		<form id="register_multi_form" novalidate>
			<?php if (!!$affiliation["affiliation_code"]) { ?>
				<input type="hidden" name="affiliation_code" value="<?=$affiliation["affiliation_code"]?>">
			<?php } ?>
			<input type="hidden" name="users">
		</form>
		<form id="register_multi_email_validation"></form>
		<form id="reset_password_form"></form>

		<script>
			let pw_icon = document.getElementById("password_icon");
			if (pw_icon) {
				pw_icon.addEventListener("click", ev=>{
					let options = ["password", "text"];
					pw_icon.nextElementSibling.type = options[1 - options.indexOf(pw_icon.nextElementSibling.type)];
					options = ["0", "1"];
					let pco = document.getElementById("password_cross_out");
					pco.style.opacity = options[1 - options.indexOf(pco.style.opacity)];
				});
			}

			[
				[document.getElementById("one_order_submit"), "one_user_registration"],
				[document.getElementById("multi_order_submit"), "multi_user_registration"]
			].forEach(y=>{
				y[0].addEventListener("click", function () {
					let form = document.getElementById("set_order_form");
					let good_to_submit = [...form.elements].filter(x=>{
						return (
							x.type != "hidden"
							&&
							!x.id
							&&
							!!x.name
							&&
							(
								!x.nextElementSibling
								||
								!!x.nextElementSibling.getElementsByClassName("required")[0]
							)
						);
					}).every(x=>{
						if (!x.value) {
							x.setCustomValidity("This field is required.");
							return false;
						} else {
							x.setCustomValidity("");
							return true;
						}
					});
					if (good_to_submit) {
						let xhttp = new XMLHttpRequest();
						xhttp.onreadystatechange = function () {
							if (xhttp.readyState === XMLHttpRequest.DONE) {
								if (xhttp.responseText == "done") {
									move_section(y[1]);
								} else {
									[query, msg] = xhttp.responseText.split("/");
									document.querySelector(query).setCustomValidity(msg);
									form.reportValidity();
								}
							}
						};
						let formdata = new FormData(form);
						xhttp.open("POST", "/application/set_pending_order", true);
						xhttp.send(formdata);
					} else {
						form.reportValidity();
					}
				});
			});

			document.getElementById("login_submit").addEventListener("click", function () {
				let form = document.getElementById("login_form");
				let good_to_submit = [...form.elements].filter(x=>{
					return (
						x.type != "hidden"
						&&
						!x.id
						&&
						!!x.name
						&&
						(
							!x.nextElementSibling
							||
							!!x.nextElementSibling.getElementsByClassName("required")[0]
						)
					);
				}).every(x=>{
					if (!x.value) {
						x.setCustomValidity("This field is required.");
						return false;
					} else {
						x.setCustomValidity("");
						return true;
					}
				});
				if (good_to_submit) {
					let xhttp = new XMLHttpRequest();
					xhttp.onreadystatechange = function () {
						if (xhttp.readyState === XMLHttpRequest.DONE) {
							if (xhttp.responseText.startsWith("/")) {
								//they only have access to one program, so just redirect them there.
								location.href = xhttp.responseText;
							} else if (xhttp.responseText.startsWith("<div>")) {
								//they have access to multiple programs, so let them choose first.
								document.getElementById("login_choose_container").innerHTML = xhttp.responseText;
								move_section("tool_choose");
								[...document.querySelectorAll("#login_choose_container label")].forEach(x=>{
									x.addEventListener("click", function () {
										let form = document.getElementById("login_form");
										form.innerHTML = `<input type='hidden' name='program' value='${x.previousElementSibling.value}'>`;
										form.submit();
									});
								})
							} else {
								//there was an error.
								document.querySelector("#login .error").innerHTML = xhttp.responseText;
							}
						}
					};
					let formdata = new FormData(form);
					xhttp.open("POST", "/application/login_auth", true);
					xhttp.send(formdata);
				} else {
					form.reportValidity();
				}
			});

			function update_notice () {
				let program_choice = document.querySelector("#register_choose_container input:checked");
				let program_notice_elements = [...document.getElementsByClassName("program_notice")];
				if (!!program_choice && program_notice_elements.length !== 0) {
					let prices = [
						<?=$prices["load_calc"]?>,
						<?=$prices["auditor"]?>,
						<?=$prices["both"]?>
					];
					if (document.getElementById("multi_user_registration").className == "active_section") {
						let number_of_users = document.querySelector("#multi_user_table tbody").children.length;
						<?php
						if ($affiliation["discount_percent"] < 15) { ?>
							if (number_of_users > 40) {
								prices = prices.map(x=>x*(1/<?=$price_multiplier?>)*0.85);
							}
						<?php }
						if ($affiliation["discount_percent"] < 10) { ?>
							else if (number_of_users > 20) {
								prices = prices.map(x=>x*(1/<?=$price_multiplier?>)*0.9);
							}
						<?php }
						if ($affiliation["discount_percent"] < 5) { ?>
							else if (number_of_users > 5) {
								prices = prices.map(x=>x*(1/<?=$price_multiplier?>)*0.95);
							}
						<?php } ?>
					}

					prices = prices.map(x => (x%1) ? x.toFixed(2) : x);

					program_notice_elements.forEach(program_notice=>{
						program_notice.innerHTML = program_choice.dataset.program + " at $" + {
							"Load Calculator" : prices[0],
							"Home Auditor" : prices[1],
							"Load Calculator and Home Auditor package" : prices[2]
						}[program_choice.dataset.program] + " per month billed annually";
					});
				}
			}

			[...document.querySelectorAll("#register_choose_container input")].forEach(x=>{
				x.addEventListener("click", update_notice);
			});
			update_notice();

			function move_section (id) {
				let next = document.getElementById(id);
				document.querySelector(".active_section").className = next.className == "to_right" ? "to_left" : "to_right";
				next.className = "active_section";
				document.querySelector("title").innerHTML = next.dataset.title;
			}

			document.getElementById("register_one_submit").addEventListener("click", function () {
				let form = document.getElementById("register_one_form");
				let good_to_submit = [...form.elements].filter(x=>{
					return (
						x.type != "hidden"
						&&
						!x.id
						&&
						!!x.name
						&&
						(
							!x.nextElementSibling
							||
							!!x.nextElementSibling.getElementsByClassName("required")[0]
						)
					);
				}).every(x=>{
					if (!x.value) {
						x.setCustomValidity("This field is required.");
						return false;
					} else {
						x.setCustomValidity("");
						return true;
					}
				});
				if (good_to_submit) {
					let xhttp = new XMLHttpRequest();
					xhttp.onreadystatechange = function () {
						if (xhttp.readyState === XMLHttpRequest.DONE) {
							if (xhttp.responseText.startsWith("/")) {
								location.href = xhttp.responseText;
							} else { //error, how to handle?
								let [query, msg] = xhttp.responseText.split("/");
								document.querySelector(query).setCustomValidity(msg);
								form.reportValidity();
							}
						}
					};
					let formdata = new FormData(form);
					xhttp.open("POST", "/application/register_one_form", true);
					xhttp.send(formdata);
				} else {
					form.reportValidity();
				}
			});

			document.getElementById("reset_password").addEventListener("click", function () {
				let form = document.getElementById("reset_password_form")
				let good_to_submit = [...form.elements].filter(x=>{
					return (
						x.type != "hidden"
						&&
						!x.id
						&&
						!!x.name
						&&
						(
							!x.nextElementSibling
							||
							!!x.nextElementSibling.getElementsByClassName("required")[0]
						)
					);
				}).every(x=>{
					if (!x.value) {
						x.setCustomValidity("This field is required.");
						return false;
					} else {
						x.setCustomValidity("");
						return true;
					}
				});
				if (good_to_submit) {
					let xhttp = new XMLHttpRequest();
					xhttp.onreadystatechange = function () {
						if (xhttp.readyState === XMLHttpRequest.DONE) {
							document.getElementById("reset_password_errors").innerHTML = xhttp.responseText;
						}
					};
					let formdata = new FormData(form);
					xhttp.open("POST", "/application/authorize_password_reset", true);
					xhttp.send(formdata);
				} else {
					form.reportValidity();
				}
			});

			[...document.querySelectorAll("input:not([id])[name], select:not([id])[name]")].forEach(x=>{
				["input", "keydown", "keyup", "mousedown", "mouseup", "select", "contextmenu", "drop"].forEach(function(event_name) {
					x.addEventListener(event_name, function () {
						x.setCustomValidity("");
					});
				});
			});

			document.getElementById("add_multi_user").addEventListener("click", function () {
				let inputs = [...document.querySelectorAll("#multi_user_registration input")];
				if (inputs.every(x=>!!x.value)) {
					let table = document.querySelector("#multi_user_table table");
					if (!table.children[1].innerHTML.trim()) {
						document.getElementById("multi_user_explanation").style.display = "none";
						table.children[0].style.display = "";
						document.getElementById("register_multi_field").style.display = "";
					}
					table.children[1].innerHTML += `
						<tr>
							<td data-first_name="${inputs[0].value}" data-last_name="${inputs[1].value}">${inputs[0].value} ${inputs[1].value}</td>
							<td><input type="text" class="disabled" value="${inputs[2].value}" form="register_multi_email_validation"></td>
							<td><a onclick="remove_multi_user(this)">Remove</a></td>
						</tr>
					`;
					inputs.forEach(x=>{x.value="";});
				} else {
					inputs.filter(x=>!!x.value)[0].setCustomValidity("Please enter a value here.");
					document.getElementById("register_multi_form").reportValidity();
				}
				update_notice();
			});

			function remove_multi_user(el) {
				if (el.parentNode.parentNode.parentNode.children.length == 1) {
					el.parentNode.parentNode.parentNode.previousElementSibling.style.display = "none";
					document.getElementById("multi_user_explanation").style.display = "";
					document.getElementById("register_multi_field").style.display = "none";
				}
				el.parentNode.parentNode.parentNode.removeChild(el.parentNode.parentNode);
				update_notice();
			}

			document.getElementById("register_multi_submit").addEventListener("click", function () {
				let form = document.getElementById("register_multi_form");
				form.querySelector("[name=users]").value = JSON.stringify([...document.querySelectorAll("#multi_user_table tbody tr")].map(tr=>({
					"first_name": tr.children[0].dataset.first_name,
					"last_name": tr.children[0].dataset.last_name,
					"email": tr.children[1].children[0].value
				})));

				let xhttp = new XMLHttpRequest();
				xhttp.onreadystatechange = function () {
					if (xhttp.readyState === XMLHttpRequest.DONE) {
						if (xhttp.responseText.startsWith("/")) {
							location.href = xhttp.responseText;
						} else {
							console.log(xhttp.responseText);
							let [query, msg] = xhttp.responseText.split("/");
							document.querySelector(query).setCustomValidity(msg);
							document.getElementById("register_multi_email_validation").reportValidity();
						}
					}
				};
				let formdata = new FormData(form);
				xhttp.open("POST", "/application/register_multi_form", true);
				xhttp.send(formdata);
			});

			document.querySelector("[form=register_multi_form][name=email]").addEventListener("keyup", function(event) {
				if (event.keyCode === 13) {
					event.preventDefault();
					document.getElementById("add_multi_user").click();
					document.querySelector("[form=register_multi_form][name=first_name]").focus();
				}
			});
		</script>

		<script>
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
			ga('create', 'UA-16655912-2', 'auto');
			ga('send', 'pageview');

			window.intercomSettings = {
			  app_id: "s7jpaljw",
			  name: "<?php echo $_SESSION['name']; ?>", // Full name
			  email: "<?php echo $_SESSION['email']; ?>", // Email address
			  phone: "<?php echo $user->phone; ?>", // Phone number
			  user_id: "<?php echo $_SESSION['user_id']; ?>", // User ID
			  user_hash: "<?php echo hash_hmac('sha256', $_SESSION['user_id'], 'HHn-Bo8ZkaE2W8HK4tcifH8_3MSedkWfeQ9qs3v9'); ?>"
			};

			(function(){var w=window;var ic=w.Intercom;if(typeof ic==="function"){ic('reattach_activator');ic('update',intercomSettings);}else{var d=document;var i=function(){i.c(arguments)};i.q=[];i.c=function(args){i.q.push(args)};w.Intercom=i;function l(){var s=d.createElement('script');s.type='text/javascript';s.async=true;s.src='https://widget.intercom.io/widget/s7jpaljw';var x=d.getElementsByTagName('script')[0];x.parentNode.insertBefore(s,x);}if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})()
		</script>
	</body>
</html>
