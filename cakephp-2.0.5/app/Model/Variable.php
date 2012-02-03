<?php

App::uses('AppModel', 'Model');

class Variable extends AppModel {
  public $name = 'Variable';

  function peek($name, $default = false) {
    $v = $this->find('first', array('conditions' => array('name' => $name)));
    return $v ? $v['Variable']['value'] : $default;
  }

  function poke($name, $value) {
    $v = new Variable();
    $v->set('name', $name);
    $v->set('value', $value);
    $existing = $this->find('first', array('conditions' => array('name' => $name)));
    if ($existing) {
      $v->set('id', $existing['Variable']['id']);
    }
    $v->save();
  }
}

?>
