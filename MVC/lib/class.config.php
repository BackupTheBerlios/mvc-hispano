<?php

class Config {

  var $root;

  function Config(&$node) {
    $this->root = &$node;
  }

  function &findAction($path) {
    $maps = &$this->root->getElementsByName('action-mappings');
    if (count($maps) === 0) return null;

    $actions = &$maps[0]->getElementsByName('action');
    $len = count($actions);

    for ($i=0; $i<$len; $i++) {
      $action = &$actions[$i];
      $actionpath = $action->getAttribute('path');
      if ($actionpath === null) continue;
      $actionpath = str_replace('#', '\#', $actionpath);
      if (preg_match('#^'.$actionpath.'$#', $path)) // Comprobamos que sea el action bueno
        return $action;
    }
    return null;
  }

  function &findForwards($action) {
    return $action->getElementsByName('forward');
  }
}

?>