<?php

class DBConnector {

    /**
     * Connect to database     
     * @return instance of PDO 
     */
    public function connect() {
	$connector = $this->connectConfig("app/config/config.local.neon");
	if ($connector == NULL) {
	    $connector = $this->connectConfig("app/config/config.neon");
	}
	return $connector;
    }

    private function connectNette() {
	$container = require 'app/bootstrap.php';
	/* $dsn = $container->nette->database->dsn;
	  $user = $container->nette->database->user;
	  $password = $container->nette->database->password; */

	print_r($container->parameters);
	/* print_r($user);
	  print_r($password); */
    }

    /**
     * Connect to database from Nette Config   
     * @param string $file file where is config 
     * @return instance of PDO 
     */
    private function connectConfig($file) {
	$str = file_get_contents($file);
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
