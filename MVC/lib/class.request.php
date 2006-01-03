<?php

require_once 'lib/class.session.php';

// Los metodos deberian retornan lo mismo que retornan los metodos de la clase de Java
// Estuve probandolos y creo que lo hace bien...
class Request {

  var $session;
  var $baseUrl;
  var $basePath;
  var $path;

  function Request() {
    $this->session = &new Session();
    $this->basePath = substr($_SERVER['PHP_SELF'], 0, -11); // Quitamos el lib/run.php
    $this->baseUrl = 'http://'.$_SERVER['HTTP_HOST'].$this->basePath;
    $this->path = substr($_SERVER['REQUEST_URI'], strlen($this->basePath)-1);
  }

  function getMethod() {
    return $_SERVER['REQUEST_METHOD'];
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

  function getBaseUrl() {
    return $this->baseUrl;
  }

  function getBasePath() {
    return $this->basePath;
  }

  function getPath() {
    return $this->path;
  }

}

?>