<?php

class User extends AppModel {
  var $name = 'User';
  var $hasMany = array('RawText' => array('className' => 'RawText', 'foreignKey' => 'owner'));

  static function displayValue($openid) {
    return preg_replace(array('/^http(s?):\/\//i', '/\/$/'), array('', ''), $openid);
  }
}

?>
