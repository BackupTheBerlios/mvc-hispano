<?php

class XMLParser {

  var $parser;
  var $file;
  var $root;
  var $current;

  function XMLParser($file, $encoding='ISO-8859-1') {
    $this->file = &$file;
    $this->parser = &xml_parser_create($encoding);
    $this->root = &new XMLNode('Root', $this);
    $this->current = &$this->root;
  }

  function parse() {
    xml_set_object($this->parser, $this);
    xml_set_element_handler($this->parser, "startElement", "endElement");
    xml_set_character_data_handler($this->parser, "characterData");

    if (!xml_parse($this->parser, file_get_contents($this->file), true)) {
      $err = array();
      $err['code'] = xml_get_error_code($this->parser);
      $err['string'] = xml_error_string($this->parser);
      $err['line'] = xml_get_current_line_number($this->parser);
      $err['col'] = xml_get_current_column_number($this->parser);
      print_r($err);
    }

  }

  function startElement(&$parser, &$name, &$attributes) {
    $aux =& new XMLNode($name, $this->current, $attributes);
    $this->current->addChild($aux);
    $this->current = &$aux;
  }

  function endElement(&$parser, &$name) {
    $this->current = &$this->current->parent;
  }

  function characterData(&$parser, &$data) {
    $this->current->content .= $data;
    return;
  }

  function printTree(&$node, $d=0) {
    for ($i=0; $i<$d; $i++) echo "\t";
    $att = '';
    foreach ($node->attributes as $key => &$value) $att .= ' '.$key.'="'.$value.'"';
    echo $node->name, $att,"\n";
    foreach ($node->childs as &$child)
      $this->printTree($child, $d+1);
  }
}

class XMLNode {
  var $name;
  var $attributes;
  var $content;
  var $parent;
  var $childs;

  function XMLNode($name, &$parent, &$attributes=array(), &$content='') {
    $this->name = &$name;
    $this->parent = &$parent;
    $this->attributes = &$attributes;
    $this->content = &$content;
    $this->childs = array();
  }

  function addChild(&$child) {
    $this->childs[] = &$child;
  }

  function &getAttribute($attribute) {
    $value = $this->attributes[strtoupper($attribute)];
    return isset($value) ? $value : null;
  }

  function &getElementsByName($name) {
    $nodes = array();
    $this->getChilds($name, $this, $nodes);
    return $nodes;
  }

  function getChilds(&$name, &$node, &$nodes) {
    for ($i=0; $i<count($node->childs); $i++) {
      if (strcasecmp($node->childs[$i]->name, $name) == 0)
        $nodes[] = &$node->childs[$i];
        $this->getChilds($name, $node->childs[$i], $nodes);
    }
  }
}

?>