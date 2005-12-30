<?php

class ActionMapping {

  var $maps;

  function ActionMapping(&$maps) {
    $this->maps = &$maps;
  }

  function &findForward($name) {
    $maps = &$this->maps;
    $len = count($maps);
    for ($i=0; $i<$len; $i++)
      if ($maps[$i]->getAttribute('name') == $name) return $maps[$i];
    return null;
  }
}

?>