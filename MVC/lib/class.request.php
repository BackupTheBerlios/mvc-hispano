<?php

require_once 'lib/class.session.php';

// Los metodos deberian retornan lo mismo que retornan los metodos de la clase de Java
// Estuve probandolos y creo que lo hace bien...
class Request {

  var $method;
  var $session;

  function Request() {
    $this->method = &$_SERVER['REQUEST_METHOD'];
    $this->session =& new Session();
  }

  function getMethod() {
    return $this->method;
  }

  function getHeaders() {
    $headers = @getallheaders();
    return $headers !== false ? $headers : null;
  }

  function getHeader($key) {
    $headers = $this->getHeaders();
    return $headers !== null && isset($headers[$key]) ? $headers[$key] : null;
  }

  function getHeaderNames() {
    $headers = $this->getHeaders();
    return $headers !== null ? array_keys($headers) : null;
  }

  function getSession() {
    return $this->session;
  }

  function getContextPath() {
    return substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/'));
  }

  function getQueryString() {
    return $_SERVER['QUERY_STRING'];
  }

  function getRequestURI() {
    if (!empty($_SERVER['QUERY_STRING']))
      return substr($_SERVER['REQUEST_URI'], 0, -(strlen($_SERVER['QUERY_STRING'])+1));
    else
      return $_SERVER['REQUEST_URI'];
  }

  function getRequestURL() {
    return 'http://'.$_SERVER['HTTP_HOST'].$this->getRequestURI();
  }

  function getScriptPath() {
    return substr($_SERVER['PHP_SELF'], strrpos($_SERVER['PHP_SELF'], '/'));
  }

  function getPath() {
    return substr($this->getRequestURI(), strlen($this->getContextPath()));
  }

}

?>