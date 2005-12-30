<?php

require_once 'lib/class.xmlparser.php';
require_once 'lib/class.config.php';
require_once 'lib/class.actionmapping.php';

class ActionServlet {

  var $config;

  function init() {
    $this->loadConfig();
  }

  function process(&$request, &$reponse) {
    $path = $request->getPath();

    $action = &$this->config->findAction($path);
    if ($action === null) return false;

    $type = $action->getAttribute('type');
    if ($type === null) return false;

    do {

      if (strpos($type, 'action.') === 0) {
        $mapping = new ActionMapping($this->config->findForwards($action));
        $type = $this->processAction(substr($type,7), $mapping, $request, $reponse);
      } else {
        $this->processView($type, $request, $reponse);
        $type = null;
      }

    } while ($type !== null);
    return true;
  }

  function processAction($type, &$mapping, &$request, &$reponse) {

    if (!(require_once ('actions/action.'.$type.'.php'))) die ('No se pudo cargar el Action');
    $class = $type.'Action';
    if (!class_exists($class)) die ("No se encuentra la clase ".$type."Action");

    $exec = &new $class();
    $forward = $exec->execute($mapping , null, $request, $reponse);
    return is_a($forward, 'XMLNode') ? $forward->getAttribute('path') : null;
  }

  function processView($type, &$request, &$reponse) {
    $file = 'views/'.$type;
    if (!is_file($file)) die ('No existe el fichero '.$file);

    ob_start();
    include $file;
    $content = ob_get_contents();
    ob_end_clean();

    echo $content;
    return null;
  }

  function destroy() {
    unset($this->config);
  }

  function loadConfig() {
    $parser = &new XMLParser('etc/config.xml');
    $parser->parse();
    $this->config = &new Config($parser->root->childs[0]);
    unset($parser);
  }

}

?>