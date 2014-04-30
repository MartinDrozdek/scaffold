<?php

class DBConnector {   
    
    
    public function connect() {
	$connector = $this->connectLocal();

	if ($connector == NULL) {
	    $connector = $this->connectGlobal();
	}
	
	return $connector;
	
	/*$this->connectNette();
	
	
	return NULL;*/
    }
    private function connectNette(){
	$container = require 'app/bootstrap.php'; 
	/*$dsn = $container->nette->database->dsn;
	$user = $container->nette->database->user;
	$password = $container->nette->database->password;*/
	
	print_r($container->parameters);
	/*print_r($user);
	print_r($password);*/
    }
    
    private function connectLocal() {
	$str = file_get_contents("app/config/config.local.neon");
	$str = explode("\t\t", $str);

	foreach ($str as $value) {
	    $value = explode(":", $value, 2);
	    if (trim($value[0]) == "user") {
		$user = trim($value[1]);
	    }
	    if (trim($value[0]) == "dsn") {
		$dsn = str_replace("'", "", trim($value[1]));
	    }
	    if (trim($value[0]) == "password") {
		$password = trim($value[1]);
	    }
	}

	if ($dsn != "" && $user != "" && $password != "") {
	    $db = new PDO($dsn, $user, $password);
	    return $db;
	} else {
	    return NULL;
	}
    }

    private function connectGlobal() {
	$str = file_get_contents("app/config/config.neon");
	$str = explode("\t\t", $str);

	foreach ($str as $value) {
	    $value = explode(":", $value, 2);
	    if (trim($value[0]) == "user") {
		$user = trim($value[1]);
	    }
	    if (trim($value[0]) == "dsn") {
		$dsn = str_replace("'", "", trim($value[1]));
	    }
	    if (trim($value[0]) == "password") {
		$password = trim($value[1]);
	    }
	}

	if ($dsn != "" && $user != "" && $password != "") {
	    $db = new PDO($dsn, $user, $password);
	    return $db;
	} else {
	    return NULL;
	}
    }

}
