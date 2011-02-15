<?php

class User extends AppModel {
  var $name = 'User';
  var $hasMany = array('RawText' => array('className' => 'RawText', 'foreignKey' => 'owner'));
}

?>
