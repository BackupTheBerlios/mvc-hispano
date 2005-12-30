<?php

error_reporting(E_ALL);

require_once 'lib/class.actionservlet.php';
require_once 'lib/class.request.php';

$request = &new Request();
$repose = null; // Temporaly

$servlet = &new ActionServlet();
$servlet->init();
$servlet->process($request, $reponse);
$servlet->destroy();

?>