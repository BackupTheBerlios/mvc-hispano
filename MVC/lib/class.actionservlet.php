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

  function process(&$request, &$reponse) {
    $this->applyRewriteRules($request);
    $path = $request->getPath();

    do {
      if (!is_file($path)) {
        $action = &$this->config->findAction($path);
        if ($action === null) return false;

        $type = $action->getAttribute('type');
        if ($type === null) return false;

        $mapping = &new ActionMapping($this->config->findForwards($action));
        $path = $this->processAction($type, $mapping, $request, $reponse);
      } else {
        $path = $this->processView($path, $request, $reponse);
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
      if (empty($pattern) || empty($path)) continue;

      $pattern = str_replace('#', '\#', $pattern);
      $request->path = preg_replace('#^'.$pattern.'$#', $path, $request->path);
      if ($request->path !== $aux) return;
    }
  }

  function processAction($type, &$mapping, &$request, &$reponse) {
    if (!(require_once ('actions/action.'.$type.'.php'))) die ('No se pudo cargar el Action');
    $class = ucfirst(strtolower($type)).'Action';
    if (!class_exists($class)) die ("No se encuentra la clase ".$class);

    $exec = &new $class();
    $forward = $exec->execute($mapping, null, $request, $reponse);
    return is_a($forward, 'XMLNode') ? $forward->getAttribute('path') : null;
  }

  function processView($path, &$request, &$reponse) {
    if (!is_file($path)) die ('No existe el fichero '.$file);

    ob_start();
    include $path;
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