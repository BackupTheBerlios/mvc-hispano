<?php

require_once 'lib/class.xmlparser.php';
require_once 'lib/class.config.php';

class ActionServlet {

  var $config;

  function init() {
    $this->loadConfig();
  }

  function process(&$request, &$reponse) {
    $action = &$this->config->findAction($request->getPath());
    if ($action === null) return null;

    $type = &$action->getAttribute('type');
    if ($type === null) return null;

    if (!(require_once ('actions/action.'.$type.'.php'))) die ('No se pudo cargar el Action');
    if (!class_exists($type.'Action')) die ("No se encuentra la clase ".$type."Action");
    $class = $type.'Action';
    $action = new $class();
    $action->execute(null, null, $request, $reponse);
  }

  function destroy() {
    unset($this->config);
  }

  function loadConfig() {
    $parser = new XMLParser('etc/config.xml');
    $parser->parse();
    $this->config = &new Config($parser->root->childs[0]);
    unset($parser);
  }

}

?>