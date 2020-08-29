<?php
class Controller extends Controller_Base {
    public $stripe_key = 'sk_test_xztsWZHxDggXgwr5Ji5VJGMn'; //sk_live_QYvZdkKtq32myZPDcQWbMYSq'
	public $models = ["User", "Order"];

	private function login_page_logic ($branded) {
		$this->set("calc_checked", "");
		$this->set("auditor_checked", "");
		$this->set("both_checked", "");
		if (isset($_GET["auditor"])) {
			$this->set("auditor_checked", "checked");
		} else if (isset($_GET["both"])) {
			$this->set("both_checked", "checked");
		} else {
			$this->set("calc_checked", "checked");
		}

		$user = new User;
		$affiliation = $user->getAffiliation($branded);
		$this->set("affiliation", $affiliation);
		$multiplier = (1 - ($affiliation["discount_percent"] / 100));
		$this->set("prices", array(
			"load_calc" => number_format(10 * $multiplier, 2),
			"auditor" => number_format(15 * $multiplier, 2),
			"both" => number_format(25 * $multiplier, 2)
		)); //prices are monthly here, in strings without dollar signs
		$this->set("price_multiplier", $multiplier);

		return 'login';
	}

	public function login ($branded=null) {
		$this->set("show_page", "login");
		return $this->login_page_logic($branded);
	}

	public function register ($branded=null) {
		$this->set("show_page", "register");
		return $this->login_page_logic($branded);
	}

	public function forgot_password ($branded=null) {
		$this->set("show_page", "forgot_password");
		return $this->login_page_logic($branded);
    }

	public function signin () {
		$this->redirect("application/login");
	}

	public function login_auth () {
		$user = new User();
        $result = $user->authenticate($_POST["email"], $_POST["password"]);

        if ($result === TRUE) {
			$allowed = array();

			if ($user->stripe_plan != null) {
				if (
					strpos($user->stripe_plan, 'auditor') !== false
					|| strpos($user->stripe_plan, 'golden_ticket') !== false
					|| strpos($user->stripe_plan, 'tec') !== false
				) {
					$allowed[] = "auditor";
				}

				if (
					strpos($user->stripe_plan, 'calc') !== false
					|| strpos($user->stripe_plan, 'tec') !== false
				) {
					$allowed[] = "calc";
				}

				if (strpos($user->stripe_plan, 'pixie') !== false) {
					$allowed[] = "pixie";
				}

				if ($user->admin) {
					$allowed[] = "admin";
				}
			}

			$num = count($allowed);
			if ($num == 0) {
				//temporarily, until all users are assigned "plans" in the db, give users with valid credentials but no plans assigned access to the load calc and the auditor.
				$allowed = array("calc", "auditor");
				$num = 2;

				/* //when all users are assigned plans in the db, uncomment this and remove above
				//somehow they dont have access to any programs, but their credentials are valid?
				return "Error code Alpha: contact support.";
				*/
			}

			if ($num == 1) {
				$_SESSION['verified_email'] = $user->email;
	            $_SESSION['verified_user_id'] = $user->id;
	            $_SESSION['email'] = $user->email;
	            $_SESSION['name'] = $user->first_name . " " . $user->last_name;
	            $_SESSION['user_id'] = $user->id;
				$_SESSION['programs'] = $allowed;
				switch ($allowed[0]) {
					case "calc":
						echo "/contractor/loads";
						break;
					case "auditor":
						echo "/auditor";
						break;
				}
			} else {
				$html = "<div>";
				foreach ($allowed as $program) {
					switch ($program) {
						case "calc":
							$html .= '<input type="radio" name="program" value="calc" form="login_form"><label>Load Calculator</label>';
							break;
						case "auditor":
						 	$html .= '<input type="radio" name="program" value="auditor" form="login_form"><label>Auditor</label>';
							break;
						case "pixie":
							$html .= '<input type="radio" name="program" value="pixie" form="login_form"><label>Pixie</label>';
							break;
						case "admin":
							$html .= '<input type="radio" name="program" value="admin" form="login_form"><label>Admin View</label>';
							break;
					}
				}
				echo $html."</div>";
			}
		} else {
			echo $_SESSION["login_error"];
		}
	}

	public function logout() {
		unset($_SESSION['verified_user_id']);
		header('Location: /application/login/');
	}

	public function send_to_program () {
		$user = new User();
        $result = $user->authenticate($_POST["email"], $_POST["password"]);

        if ($result === TRUE) {
			$allowed = array();

			if ($user->stripe_plan != null) {
				if (
					strpos($user->stripe_plan, 'auditor') !== false
					|| strpos($user->stripe_plan, 'golden_ticket') !== false
					|| strpos($user->stripe_plan, 'tec') !== false
				) {
					$allowed[] = "auditor";
				}

				if (
					strpos($user->stripe_plan, 'calc') !== false
					|| strpos($user->stripe_plan, 'tec') !== false
				) {
					$allowed[] = "calc";
				}

				if (strpos($user->stripe_plan, 'pixie') !== false) {
					$allowed[] = "pixie";
				}

				if ($user->admin) {
					$allowed[] = "admin";
				}
			}

			if (in_array($_POST["program"], $allowed)) {
				$_SESSION['verified_email'] = $user->email;
				$_SESSION['verified_user_id'] = $user->id;
				$_SESSION['email'] = $user->email;
				$_SESSION['name'] = $user->first_name . " " . $user->last_name;
				$_SESSION['user_id'] = $user->id;
				$_SESSION['programs'] = $allowed;

				switch ($_POST["program"]) {
					case "calc":
						header('Location: /contractor/loads');
						break;
					case "auditor":
						header('Location: /auditor/contractor');
						break;
					case "pixie":
						header('Location: http://pixie.eds.tech');
						break;
					case "admin":
						header('Location: /application/admin');
						break;
				}
			} else {
				$_SESSION["login_error"] = "It looks like there's a problem with your account. Please contact us with the chat bubble at the bottom right to fix your account.";
				header('Location: /application/login');
			}
		} else {
			$this->layout = 'auditor';
			echo $_SESSION["login_error"];
			return 'echo';
		}
	}

	public function set_pending_order () {
		$_POST["zip"] = trim($_POST["zip"]);
		$_POST["phone"] = str_split($_POST["phone"]);
		$phone_nums = [];
		foreach ($_POST["phone"] as $num) {
			if (ctype_digit($num)) {
				$phone_nums[] = $num;
			}
		}
		$phone = implode("", $phone_nums);
		if (strlen($_POST["zip"]) != 5 || !ctype_digit($_POST["zip"])) {
			echo "[name=zip][form=set_order_form]/That's not a valid American zip code. If you're in another country, please click the blue chat bubble at the bottom right to contact us about registering.";
		} else if (strlen($phone) != 10) {
			echo "[name=phone][form=set_order_form]/That's not a valid phone number. Please don't include any international codes.";
		} else {
			$_SESSION["pending_order"] = [
				"program" => $_POST["program"],
				"company_name" => $_POST["company_name"],
				"address_1" => $_POST["address_1"],
				"address_2" => $_POST["address_2"],
				"city" => $_POST["city"],
				"state" => $_POST["state"],
				"zip" => $_POST["zip"],
				"phone" => "(".substr($phone, 0, 3).") ".substr($phone, 3, 3)."-".substr($phone, 6, 4),
				"password" => isset($_POST["password"]) ? $_POST["password"] : "",
				"affiliation_code" => isset($_POST["affiliation_code"]) ? $_POST["affiliation_code"] : ""
			];
			echo "done";
		}
		return 'echo';
	}

	public function register_one_form () {
		$user = new User();

		if ($user->isEmailTaken($_POST["email"])) {
			echo "[name=email][form=register_one_form]/That email is taken. Click the links below to log in or reset your password.";
		} else {
			$user->affiliation_code = isset($_POST["affiliation_code"]) ? $_POST["affiliation_code"] : "";
			$affiliation = $user->createPendingOrder([
				[
					"email" => $_POST["email"],
					"first_name" => $_POST["first_name"],
					"last_name" => $_POST["last_name"]
				]
			]);
			if ($affiliation["requires_approval"]) {
				$_SESSION["redirect_to"] = "/application/pending_approval";
				echo "/application/pending_approval";
			} else {
				echo "/application/take_payment";
			}
		}
		return 'echo';
	}

	public function register_multi_form () {
		$user_obj = new User;
		$new_users = json_decode($_POST["users"], true);
		$issue = false;
		foreach ($new_users as $new_user_data) {
			if ($user_obj->isEmailTaken($new_user_data["email"])) {
				echo "[value='".$new_user_data["email"]."'][form=register_multi_email_validation]/That email is taken. Please remove this user or contact support.";
				$issue = true;
				break;
			}
		}

		if (!$issue) {
			$user_obj->affiliation_code = isset($_POST["affiliation_code"]) ? $_POST["affiliation_code"] : "";
			$affiliation = $user_obj->createPendingOrder($new_users);
			if ($affiliation["requires_approval"]) {
				$_SESSION["redirect_to"] = "/application/pending_approval";
				echo "/application/pending_approval";
			} else {
				echo "/application/take_payment";
			}
		}

		return 'echo';
	}

	public function pending_approval () {
		if (isset($_SESSION['verified_user_id'])) {
			$user = new User($_SESSION['verified_user_id']);
			if ($user->admin) {
				$this->redirect("application/admin");
				return;
			}
		}
		$this->set("pending_approval", true);
		$this->set("take_payment", false);
		$this->register_step_2();
	}

	public function take_payment () {
		$this->set("pending_approval", false);
		$this->set("take_payment", true);
		$this->register_step_2();
	}

	public function register_step_2 () {
		if (isset($_SESSION["redirect_to"])) {
			$rd = $_SESSION["redirect_to"];
			unset($_SESSION["redirect_to"]);
			$this->redirect($rd);
		} else if (!isset($_SESSION["pending_order"])) {
			$this->redirect("/application/register");
		} else {
			return 'register_step_2';
		}
	}

	public function approve_pending_order () {
		//$_POST must include password (if there isn't a default one for the affiliation), pending_order_id, amount (defaults to 0), stripeToken (if approval isn't required), comments, and program_title
		require_once ('stripe/init.php');

		//set defaults
		if (!isset($_POST["amount"])) {
			$amount = 0;
		} else {
			$amount = $_POST["amount"];
		}
		if (!isset($_POST["program_title"])) {
			$program_title = 'Load Calculator';
		} else {
			$program_title = $_POST["program_title"];
		}

		//Create order
		$order = new Order;
		$results = $order->createFromPending($_POST["pending_order_id"], $amount, $program_title);
		$user = new User;
		$user_results = $user->getPendingUser($results["pending_user_ids"][0]);
		$affiliation = $user->getAffiliation($user_results["affiliation_code"]);
		$plan = $user_results["stripe_plan"];
		$stripe_plans = array_merge(...array_fill(0, count($results["pending_user_ids"]), explode(" ", $plan)));

		if (!!$affiliation["typical_password"]) {
			$password = $affiliation["typical_password"];
		} else if (!!$affiliation["requires_approval"]) {
			$password = $user_results["password"];
		} else {
			$password = $_POST["password"];
		}

		if (!!$affiliation["requires_approval"]) {
			$stripe_id = null;
			$user = new User;
			$user_data = $user->getUserById($_SESSION['verified_user_id']);
			$comments = str_replace("user_name",$user_data["first_name"]." ".$user_data["last_name"], $_POST["comments"]);
		} else {
			\Stripe\Stripe::setApiKey($this->stripe_key);
			try {
				$customer = \Stripe\Customer::create([
					'email' => $user_results["email"],
					'source' => $_POST['stripeToken'],
					'address' => [
						'line1' => $user_results["address_1"],
						'line2' => $user_results["address_2"],
						'city' => $user_results["city"],
						'state' => $user_results["state"],
						'postal_code' => $user_results["zip"],
						'country' => 'US'
					],
					'description' => "Subscribed through yourvirtualhvac.com",
					'name' => $user_results["first_name"]." ".$user_results["last_name"],
					'phone' => $user_results["phone"]
				]);
				foreach ($stripe_plans as $p) {
					\Stripe\Subscription::create([
						'customer' => $customer->id,
						'items' => [['plan' => $p]],
						'trial_period_days' => 14
					]);
				}

				$stripe_id = $customer->id;
				$comments = $_POST["comments"];
			} catch (Exception $e) {
				$error='Unable to subscribe to '.$program_title.' '.$user_results["email"].', error: '.$e->getMessage();
				error_log($error);
				echo $error;
				return 'echo';
			}
		}

		//Create users
		$emails = [];
		$num_of_users = count($results["pending_user_ids"]);
		for ($i = 0; $i < $num_of_users; $i++) {
			$user = new User;
			$user_info = $user->createFromPending($results["pending_user_ids"][$i], $results["order_id"], $i!=1, $password, $comments, $stripe_id, $plan);
			$this->send_mail("welcome", $user_info["email"], [
				"subject" => "Welcome to EDS ".$program_title,
				"name" => $user_info['first_name'],
				"program" => $program_title,
				"from_rep" => $affiliation["requires_approval"]
			]);
			$emails[] = $user_info["email"];

			if ($num_of_users == 1 && !$affiliation["requires_approval"]) { //we don't want an admin logged in to this user's account and redirected
				$_SESSION['verified_email'] = $user_info["email"];
				$_SESSION['verified_user_id'] = $user_info["user_id"];
	            $_SESSION['email'] = $user_info["email"];
	            $_SESSION['name'] = $user_info["first_name"] . " " . $user_info["last_name"];
	            $_SESSION['user_id'] = $user_info["user_id"];
				if (substr($plan, 0, 4) === "calc") {
					echo "/contractor/loads";
				} else {
					echo "/auditor/contractor";
				}
			}
		}

		if (count($emails) > 1 && !$affiliation["requires_approval"]) {
			\Stripe\Customer::update(
				$customer->id,
				['description' => 'Subscribed through yourvirtualhvac.com. This is a multi-user account that pays for '.implode(", ", $emails)]
			);
		}

		if ($num_of_users != 1) {
			$_SESSION["login_error"] = "All users were added. You can now log in.";
			echo "/application/login";
		}
		return 'echo';
	}

	public function admin () {
		if (!isset($_SESSION['verified_user_id'])) {
			$this->redirect("/application/login");
		} else {
			$user = new User($_SESSION['verified_user_id']);
			if ($user->admin) {
				$pending_orders = $user->getPendingOrdersByAffiliation();
				$pending_user_table_body = '';
				foreach ($pending_orders as $pending_order) {
					$pending_users = $pending_order["users"];
					$pending_user_table_body .= '<tr data-pending_order_id="'.$pending_order["order_id"].'" data-pending_users=\''.str_replace("'", "&apos;", json_encode($pending_users)).'\'>
						<td>'.$pending_users[0]["company_name"].'</td>
						<td>'.trim($pending_users[0]["address_1"].', '.$pending_users[0]["address_2"], ', ').'</td>
						<td>'.trim($pending_users[0]["city"].' '.$pending_users[0]["state"].', '.$pending_users[0]["zip"], ', ').'</td>
						<td>'.$pending_users[0]["phone"].'</td>
						<td><a onclick="view_order('.$pending_order["order_id"].')">View Order</a></td>
					</tr>';
				}

				$active_users = $user->getUsersWithSameAffiliation();
				$active_users_table_body = '';
				foreach ($active_users as $active_user) {
					$active_users_table_body .= '<tr data-user_id="'.$active_user["id"].'">
						<td>'.$active_user["email"].'</td>
						<td>'.$active_user["first_name"]." ".$active_user["last_name"].'</td>
						<td>'.$active_user["company_name"].'</td>
						<td>'.trim($active_user["address_1"].', '.$active_user["address_2"], ', ').'</td>
						<td>'.trim($active_user["city"].", ".$active_user["state"]." ".$active_user["zip"], ', ').'</td>
						<td>'.$active_user["phone"].'</td>
						<td>'.$active_user["last_seen"].'</td>
						<td><a onclick="delete_active_user('.$active_user["id"].')">Delete User</a></td>
					</tr>';
				}

				$affiliation = $user->getAffiliation($user->affiliation_code);

				$this->set("affiliation_code", $user->affiliation_code);
				$this->set("pending_user_table_body", $pending_user_table_body);
				$this->set("active_users_table_body", $active_users_table_body);
				$this->set("affiliation_name", $affiliation["company_name"]);
				return 'admin';
			} else {
				$this->redirect("/application/login");
			}
		}
	}

	public function reject_pending_user () {
		if (!isset($_SESSION['verified_user_id'])) {
			echo "You're not logged in, so you're not authorized to do this.";
		} else {
			$user = new User($_SESSION['verified_user_id']);
			if ($user->admin) {
				echo $user->rejectPendingUser($_POST["pending_user_id"]);
			} else {
				echo "You're not an admin, so you're not authorized to do this.";
			}
		}
		return 'echo';
	}

	public function delete_user () {
		if (isset($_SESSION['verified_user_id'])) {
			$user_admin = new User($_SESSION['verified_user_id']);
			$user_to_delete = new User($_POST["user_id"]);
			if ($user_admin->admin && $user_admin->affiliation_code == $user_to_delete->affiliation_code) {
				$user_to_delete->delete_user($user_to_delete->id, "Deleted by ".$user_admin->first_name." ".$user_admin->last_name." on ".date("F j, Y"));
				echo "done";
			} else {
				echo "Unauthorized";
			}
		} else {
			echo "Not logged in";
		}
		return "echo_output";
	}

	public function get_admin_csv () {
		if (isset($_SESSION['verified_user_id'])) {
			$user = new User($_SESSION['verified_user_id']);
			if ($user->admin) {
				$user->queryToCSV(
					$user->affiliation_code."_users",
					"SELECT
						email,
						CONCAT_WS(' ', IF(first_name='', null, first_name), IF(last_name = '', null, last_name)) as name,
						company_name as company,
						CONCAT_WS(', ', IF(address_1 = '', null, address_1), IF(address_2 = '', null, address_2)) as `street address`,
						CONCAT_WS(' ', CONCAT_WS(', ', IF(city='', null, city), IF(state='', null, state)), IF(zip='', null, zip)) as locale,
						phone,
						created,
						last_seen as `last seen`,
						IF (admin, 'Admin', '') as `is an admin`
						FROM users
						WHERE affiliation_code = :affiliation_code",
					[":affiliation_code" => $user->affiliation_code]
				);
			} else {
				echo "Unauthorized";
			}
		} else {
			echo "Not logged in";
		}
		return "echo_output";
	}

	public function stripe_changed() {
		require_once ('stripe/init.php');
        require_once ('wave/database.php');

		\Stripe\Stripe::setApiKey($this->stripe_key);
		$resp = json_decode(@file_get_contents("php://input"));
		switch ($resp->type) {
			case "customer.deleted":
				//to reactive customer deleted webhook, just add another slash to the line below
				/*
				$customer_id = $resp->data->object->id;
				$customer_obj = \Stripe\Customer::all(array(
					'email' => $resp->data->object->email
				));
				$user = new User();
				if (isset($customer_obj->data[0])){
					$this->set('resp', "Error 1: account was not deleted.");
				} else if ($user->is_on_multi_user_stripe_plan($resp->data->object->email)) {
					$this->set('resp', "Error 2: account was not deleted.");
				} else {
					$user->getUserByEmail($resp->data->object->email);
					$user->delete_user($user->id, 'via Stripe webhook.');
					$this->set('resp', 'The customer under stripe id "'.$customer_id.'" was deleted.');
				}
				//*/$this->set('resp', 'The customer.deleted webhook is temporarily disabled.');
				break;

			case "customer.subscription.updated":
			case "customer.subscription.deleted":
			case "customer.subscription.created":
				$changed_plan = $resp->data->object->items->data->plan->id;
				if (strpos("calc", $changed_plan) !== false || strpos("auditor", $changed_plan) !== false) {
					$customer_id = $resp->data->object->customer;
					$customer_obj = \Stripe\Customer::retrieve($customer_id);
					$user = new User();

					$plans = array();
					foreach($customer_obj->subscriptions->data as $plan) {
						$plans[] = $plan->items->data[0]->plan->id;
					}
					$plans = join(" ", $plans);

					if ($plans == "") {
						$customer_obj = \Stripe\Customer::all(array(
							'email' => $resp->data->object->email
						));
						if (isset($customer_obj->data[0])){
							$this->set('resp', "Error 1: account was not deleted.");
						} else if ($user->is_on_multi_user_stripe_plan($resp->data->object->email)) {
							$this->set('resp', "Error 2: account was not deleted.");
						} else {
							$user->getUserByEmail($resp->data->object->email);
							$user->delete_user($user->id, 'via Stripe webhook.');
							$this->set('resp', 'The customer under stripe id "'.$customer_id.'" was deleted.');
						}
					} else {
						if (!!$user->getUserByEmail($customer_obj->email)) {
							$user->stripe_plan = $plans;
							$user->stripe_id = $customer_id;
							$user->email = $customer_obj->email;
							$user->update();
							$this->set('resp', "Retrieved this account's subscriptions from Stripe and updated the database.");
						} else {
							$id = $user->isUserDeleted($customer_obj->email);
							if (!!$id) {
								$user->restoreDeletedUser($id, 'via Stripe webhook.');
								$user->stripe_plan = $plans;
								$user->stripe_id = $customer_id;
								$user->email = $customer_obj->email;
								$user->update();
								$this->set('resp', "Retrieved this account's subscriptions from Stripe and reinstated the user with update information.");
							} else {
								mail(
									'support@eds.tech',
									'Warning: Stripe Webhook Failed',
									'A subscription was created for '.$customer_obj->email.' in Stripe, and the stripe_changed function in application_controller.php doesn\'t know what to do about it. '.$customer_obj->email.' isn\'t an active account or a deleted account, so there\'s nothing to restore. Please get a developer to fix this.',
									"MIME-Version: 1.0\nContent-type:text/html;charset=UTF-8\nFrom: Energy Design Systems <noreply@eds.tech>\ncc: EDS Sales <sales@eds.tech>\nX-Mailer: PHP/".phpversion()
								);
								$this->set('resp', "Account not changed, see email sent to support@eds.tech.");
							}
						}
					}
				} else {
					$this->set('resp', "Ignored webhook request, not for Load Calc or Auditor");
				}
				break;

			default:
				$this->set('resp', "I don't recognize this webhook type.");
		}
		return 'echo';
	}

	public function sd (){session_destroy();}
	public function t ($email) {
		$user = new User();
		$id = $user->isUserDeleted($email);
		if (!!$id) {
			$user->restoreDeletedUser($id, 'via Stripe webhook.');
			echo "restored ".$id;
		} else {
			echo "nope";
		}
		return 'echo';
	}

	public function authorize_password_reset() {
        if (isset($_POST['email']) && !empty($_POST['email'])) {
            $user = new User();
            $result = $user->getUserByEmailArray($_POST['email']);
            if ($result) {
                $this->send_mail("password_reset", $_POST['email'], [
					"subject" => "Password Reset Request",
					"name" => $result['first_name'] . " " . $result['last_name'],
					"hash" => substr(md5($_POST['email'] . $result[0]['id']), 0, 6),
					"email" => $_POST["email"],
					"program" => (substr("auditor", $result->stripe_plan) !== false || substr("golden_ticket", $result->stripe_plan) !== false) ? "EDS Home Auditor" : "EDS Load Calculator"
				]);
				echo "Please check your email for your password reset request.";
            } else {
				echo "That email doesn't have an account. Please contact support.";
            }
        } else {
			echo "Please enter an email address.";
        }
		return 'echo';
    }

	//This function will reset the password based off of the hash link the user clicks
    public function reset_password($hash, $email) {
        $user = new User();
        $result = $user->getUserByEmailArray($email);
        //Check hash
        if ($hash == substr(md5($email . $result[0]['id']), 0, 6)) {
			$alphabet = "abcdefghijkmnpqrstuwxyzABCDEFGHJKLMNPQRSTUWXYZ23456789";
	        $pass = array(); //remember to declare $pass as an array
	        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
	        for ($i = 0;$i < 8;$i++) {
	            $n = rand(0, $alphaLength);
	            $pass[] = $alphabet[$n];
	        }
			$password = implode($pass);
            $user = new User($result[0]['id']);
            $info = $user->reset_password($result[0]['id'], $password);
            $this->send_mail("new_password", $email, [
				"subject" => "Password Reset Successful",
				"name" => $result[0]['first_name'] . " " . $result[0]['last_name'],
				"new_password" => $password
			]);
			$_SESSION["reset_password_message"] = "Your password has been reset. Please check your email.";
        } else {
			$_SESSION["reset_password_message"] = "This password reset link is invalid. Please contact support.";
        }
		header('Location: /application/forgot_password');
    }

    public function heartbeat() {//$hash) {
        $user = new User();
		$this->set('resp', $user->heartbeat($_SESSION["email"]));
		return 'echo';
    }

    public function kick_out() {
        $user = new User();
		$result = $user->getUserByEmailArray($_SESSION["kick_out_email"]);

		$user->kick_out($_SESSION["kick_out_email"]);

		//send them a password reset email
		$this->send_password_reset_email($_SESSION["kick_out_email"], $result[0]['first_name'] . " " . $result[0]['last_name'], substr(md5($_SESSION["kick_out_email"] . $result[0]['id']), 0, 6));

		unset($_SESSION["kick_out_email"]);
		$_SESSION["login_error"] = "All sessions are closed. Please check your email for your new password.";
		header('Location: /application/login');
    }

	public function has_been_kicked_out() {
        $_SESSION["login_error"] = "You have been logged out of your session due to multiple active connections. Please check your email.";
		header('Location: /application/login');
    }

	public function timeout() {
        $_SESSION["login_error"] = "Your session timed out, please try again.";
		header('Location: /application/login');
    }

	public function unknown_error() {
        $_SESSION["login_error"] = "An unknown error occured. If the problem persists, contact support@eds.tech.";
		header('Location: /application/login');
    }

	public function testmail ($user=null, $view_name=null) {
		if (in_array($user, ["jbaptista", "mlane", "pdesai", "knashed", "dcameron"])){
			if (!$view_name) {
				echo '<a href="/application/testmail/'.$user.'/welcome">Welcome Email</a><br><a href="/application/testmail/'.$user.'/password_reset">Password Reset Email</a><br><a href="/application/testmail/'.$user.'/new_password">New Password Email</a>';
				return 'echo';
			} else {
				$this->set("data", array(
					"name" => "Jaden Baptista",
					"program" => "Load Calculator",
					"free_trial" => true,
					"email" => "jbaptista@eds.tech",
					"hash" => "AbCd123.456eFgH",
					"new_password" => "NEW12345"
				));
				return 'emails/'.$view_name.'.php';
			}
		} else {
			echo 'Unauthorized';
			return 'echo';
		}
	}

	function send_mail ($email_view, $to, $data) {
		ob_start();
		include(SERVER_PATH.'/views/emails/'.$email_view.'.php');
		$content = ob_get_contents();
		ob_end_clean();

		if (mail(
			$to,
			$data["subject"],
			$content,
			"MIME-Version: 1.0\nContent-type:text/html;charset=UTF-8\nFrom: Energy Design Systems <noreply@eds.tech>\ncc: EDS Sales <sales@eds.tech>\nReply-To: support@eds.tech\nX-Mailer: PHP/".phpversion()
		)) {
            return true;
        }
	}
}
