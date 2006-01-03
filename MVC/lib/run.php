<?php

error_reporting(E_ALL);

chdir('..');

require_once 'lib/class.actionservlet.php';
require_once 'lib/class.request.php';

$request = &new Request();
$repose = null;

$servlet = &new ActionServlet();
$servlet->init();
$servlet->process($request, $reponse);
$servlet->destroy();

?>