<?php
$authorized = array(
	'cleancache'
);
$action = isset($argv[1]) ? $argv[1] : '';
if(!in_array($action, $authorized)){
	die("Unknown action '$action'".PHP_EOL);
}

/*
 * Action code
 */ 
switch($action){
	case 'cleancache':
		$execString = 'curl "http://www.mountainboard.fr/admin/clear-apc-cache?authCheck=ra45HuiB@&mode=user"';
		exec($execString, $return);
		echo var_export($return, true).PHP_EOL;

		$execString = 'curl "http://www.mountainboard.fr/admin/clear-apc-cache?authCheck=ra45HuiB@&mode=opcode"';
        exec($execString, $return);
        echo var_export($return, true).PHP_EOL;

		break;
}
