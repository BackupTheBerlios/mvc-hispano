<?php

class Response {

  var $cont = true;
  var $headers = array('content-type' => 'text/html');
  var $status = 200;

  function setStatus($num) {
    $status = $this->getStatusString($num);
    if ($status === null) return false;
    $this->status = $num;
    return true;
  }

  function sendError($num) {
    $status = $this->getStatusString($num);
    header($status);
    if (is_file($file = 'errors/'.$num.'.html')) include($file);
    else echo $status;
    die;
  }

  function getStatusString($num=-1) {
    if ($num === -1) $num = $this->status;
    static $http = array (
      100 => 'Continue',
      101 => 'Switching Protocols',
      200 => 'OK',
      201 => 'Created',
      202 => 'Accepted',
      203 => 'Non-Authoritative Information',
      204 => 'No Content',
      205 => 'Reset Content',
      206 => 'Partial Content',
      300 => 'Multiple Choices',
      301 => 'Moved Permanently',
      302 => 'Found',
      303 => 'See Other',
      304 => 'Not Modified',
      305 => 'Use Proxy',
      307 => 'Temporary Redirect',
      400 => 'Bad Request',
      401 => 'Unauthorized',
      402 => 'Payment Required',
      403 => 'Forbidden',
      404 => 'Not Found',
      405 => 'Method Not Allowed',
      406 => 'Not Acceptable',
      407 => 'Proxy Authentication Required',
      408 => 'Request Time-out',
      409 => 'Conflict',
      410 => 'Gone',
      411 => 'Length Required',
      412 => 'Precondition Failed',
      413 => 'Request Entity Too Large',
      414 => 'Request-URI Too Large',
      415 => 'Unsupported Media Type',
      416 => 'Requested range not satisfiable',
      417 => 'Expectation Failed',
      500 => 'Internal Server Error',
      501 => 'Not Implemented',
      502 => 'Bad Gateway',
      503 => 'Service Unavailable',
      504 => 'Gateway Time-out');
    $str = &$http[$num];
    return isset($str) ? 'HTTP/1.1 '.$num.' '.$str : null;
  }

}

?>