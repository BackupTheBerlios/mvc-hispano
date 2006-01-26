<?php

chdir('..');

require_once 'lib/class.actionservlet.php';
require_once 'lib/class.request.php';
require_once 'lib/class.response.php';

$request = &new Request();
$response = &new Response();

$servlet = &new ActionServlet();
$servlet->init();
$success = $servlet->process($request, $response);
$servlet->destroy();

if (!$success) {
  $response->sendError(404);
}

?>