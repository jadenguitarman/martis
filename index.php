<?php
function exception_handler ($e) {
	ob_clean();
	$message = $e->getMessage();
	$code = strval($e->getCode());
	$line = $e->getLine();
	$file = $e->getFile();
	$trace = $e->getTrace();
	if ($code == "0") {
		$code = "500";
	}

	if ($code != "404" && !!$_ENV["ERROR_CONTACTS"]) {
		foreach ($_ENV["ERROR_CONTACTS"] as $contact) {
			mail($contact, 'Potential Vulnerability', 'A message from Gravity, your PHP framework, triggered by line '.$line.' of '.$file.': '.$message, "MIME-Version: 1.0\nContent-type:text/html;charset=UTF-8\nFrom: Energy Design Systems <noreply@eds.tech>\ncc: EDS Sales <sales@eds.tech>\nReply-To: support@eds.tech\nX-Mailer: PHP/".phpversion());
		}
	}
	if (file_exists(__DIR__.'\\views\\errors\\'.$code.'.php')) {
		include(__DIR__.'\\views\\errors\\'.$code.'.php');
	} else { ?>
		Well, there seems to be a problem.<br>
		Error on line <?=$line?> in <?=$file?>:<br>
		<?=$message?>
		<pre>
			<?php print_r($trace); ?>
		</pre>

	<?php }
}

function serve_from_public ($route) {
	$filepath = __DIR__."\\public\\".$route;
	if (strpos(realpath($filepath), __DIR__.'\\public') !== 0) {
		throw new Exception("The requested public asset could not be found: ".$route, 404);
	} else {
		switch (pathinfo($filepath, PATHINFO_EXTENSION)) {
			case 'css':
				header('Content-type: text/css');
				break;
			case 'js':
				header('Content-type: application/javascript');
				break;
			default:
				header('Content-type: '.mime_content_type($filepath));
				break;
		}
		readfile($filepath);
	}
}

function run_controller_function ($controller_name, $function_name, $args) {
	$filepath = __DIR__.'\\controllers\\'.$controller_name.'.php';
	if (file_exists($filepath)) { //if the requested controller exists
		include_once('Controller.php');
		include_once($filepath);
		$controller = new Controller();
		if (method_exists($controller, $function_name)) { //if the requested method exists on that controller
			if (count($controller->models) > 0) {
				include("Model.php");
				foreach ($controller->models as $model) {
					if (file_exists(__DIR__."\\models\\".$model.".php")) {
						include(__DIR__."\\models\\".$model.".php");
					} else {
						throw new Exception("The ".$model." model doesn't exist.", 500);
					}
				}
			}

			$view = call_user_func([&$controller, $function_name], ...$args);
			if (!!$view) { //if the function returned the name of a view, serve it
				if (strpos(realpath(__DIR__.'\\views\\'.$controller_name.'\\'.$view.'.php'), __DIR__.'\\views\\') !== 0) {
					throw new Exception($view." isn't a view. It was called by the ".$function_name." function on the ".$controller_name." controller.", 500);
				} else {
					foreach ($controller->args_for_view as $key => $val) {
						$$key = $val;
					}
					if ($controller->layout) {
						include(__DIR__.'\\views\\layouts\\'.$controller->layout.'.php');
						Layout::header();
					}
					include(__DIR__.'\\views\\'.$controller_name.'\\'.$view.'.php');
					if ($controller->layout) {
						Layout::footer();
					}
				}
			}
		} else {
			throw new Exception("The ".$function_name." function doesn't exist on the ".$controller_name." controller.", 404);
		}
	} else {
		throw new Exception("The ".$controller_name." controller doesn't exist.", 404);
	}
}

function find_path ($route, $controllers) {
	if (in_array($route[0], $controllers)) {
		run_controller_function($route[0], $route[1], array_slice($route, 2));
	} else {
		serve_from_public(implode("\\", $route));
	}
}

function get_config ($filename) {
	$contents = file_get_contents($filename);
	switch (mime_content_type($filename)) {
		case "text/xml":
			$contents = json_encode(simplexml_load_file($filename)); //to convert to array for iteration
			break;
		case "application/json":
		default:
			break;
	}
	$consts = json_decode($contents, true);

	foreach ($consts as $key => $val) {
		$_ENV[$key] = $val;
	}
}

function get_controllers () {
	$controllers = [];
	foreach(glob(__DIR__.'\\controllers\\*.php') as $file) {
		$file = explode("\\", $file);
		$file = explode(".", $file[count($file)-1]);
		$controllers[] = implode(".", array_slice($file, 0, count($file) - 1));
	}
	return $controllers;
}

function get_started ($config_file) {
	get_config($config_file);
	set_exception_handler('exception_handler');
	session_start();
	ob_start();
	$controllers = get_controllers();

	$route = explode('/', trim(strtolower(explode("?", explode("#", $_SERVER["REQUEST_URI"])[0])[0]), '/'));
	if ($route[0] == "") { // this is the root, like example.com/
		switch ($_ENV["ON_ROOT"]["command"]) {
			case "redirect":
				header("Location: ".$_ENV["ON_ROOT"]["destination"]);
				break;
			case "simulate_path":
				$route = explode("#", $_ENV["ON_ROOT"]["path"])[0];
				[$route, $get] = explode("?", $route);
				if (isset($_ENV["ON_ROOT"]["clear_get"]) && !!$_ENV["ON_ROOT"]["clear_get"]) {
					$_GET = [];
				}
				parse_str($get, $_GET);
				find_path(explode('/', trim(strtolower($route), '/')), $controllers);
				break;
		}
	} else {
		find_path($route, $controllers);
	}
}

get_started("config.xml");
