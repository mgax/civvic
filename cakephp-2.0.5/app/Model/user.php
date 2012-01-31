<?php

class User extends AppModel {
  var $name = 'User';
  var $hasMany = array('RawText' => array('className' => 'RawText', 'foreignKey' => 'owner'));

  static function displayValue($data) {
    if (array_key_exists('nickname', $data) && $data['nickname']) {
      return $data['nickname'];
    } else if (array_key_exists('email', $data) && $data['email']) {
      return $data['email'];
    } else {
      return preg_replace(array('/^http(s?):\/\//i', '/\/$/'), array('', ''), $data['openid']);
    }
  }
}

?>
