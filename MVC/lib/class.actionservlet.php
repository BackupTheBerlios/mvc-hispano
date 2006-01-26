<?php

require_once 'lib/class.xmlparser.php';
require_once 'lib/class.config.php';
require_once 'lib/class.action.php';
require_once 'lib/class.actionmapping.php';

class ActionServlet {

  var $config;

  function init() {
    $this->loadConfig();
  }

  function process(&$request, &$response) {
    if ($this->config === null || !$this->applyRewriteRules($request))
      $response->sendError(500);

    $path = $request->getPath();

    do {
      if (!is_file($path)) {
        $action = &$this->config->findAction($path);
        if ($action === null) return false;

        $type = $action->getAttribute('type');
        if ($type === null) return false;

        $mapping = &new ActionMapping($this->config->findForwards($action));
        $path = $this->processAction($type, $mapping, $request, $response);
      } else {
        $path = $this->processView($path, $request, $response);
      }
    } while ($path !== null);

    return true;
  }

  function applyRewriteRules(&$request) {
    $rules = &$this->config->findRewriteRules();
    $len = count($rules);
    $aux = $request->path;

    for ($i=0; $i<$len; $i++) {
      $pattern = &$rules[$i]->getAttribute('pattern');
      $path = &$rules[$i]->getAttribute('path');
      if (is_null($pattern) || is_null($path)) return false;

      $pattern = str_replace('#', '\#', $pattern);
      $request->path = @preg_replace('#^'.$pattern.'$#', $path, $request->path);

      if ($request->path === null) return false;
      if ($request->path !== $aux) break;
    }

    if (($pos = strpos($request->path, '?')) !== false) {
      parse_str(substr($request->path, $pos+1), $_GET);
      $request->path = substr($request->path, 0, $pos);
    }
    return true;
  }

  function processAction($type, &$mapping, &$request, &$response) {
    if (!(require_once ('actions/action.'.$type.'.php'))) die ('No se pudo cargar el Action');
    $class = ucfirst(strtolower($type)).'Action';
    if (!class_exists($class)) die ("No se encuentra la clase ".$class);

    $exec = &new $class();
    $forward = $exec->execute($mapping, null, $request, $response);
    return is_a($forward, 'XMLNode') ? $forward->getAttribute('path') : null;
  }

  function processView($path, &$request, &$response) {
    if (!is_file($path)) die ('No existe el fichero '.$file);

    ob_start();
    include $path;
//    $content = ob_get_contents();
    ob_end_flush();

//    echo $content;
    return null;
  }

  function destroy() {
    unset($this->config);
  }

  function loadConfig() {
    $parser = &new XMLParser('etc/config.xml');
    if ($parser->parse()) $this->config = &new Config($parser->root->childs[0]);
    else $this->config = null;
    unset($parser);
  }

}

?>