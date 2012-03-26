<?php

class User extends BaseObject {

  function getDisplayName() {
    if ($this->nickname) {
      return $this->nickname;
    }
    if ($this->name) {
      return $this->name;
    }
    if ($this->email) {
      return $this->email;
    }
    return $this->identity;
  }

  /**
   * Creates a new user or updates an existing one with the ones provided.
   **/
  static function updateFromOpenId($openidData) {
    $user = self::get_by_identity($openidData['identity']);
    if (!$user) {
      $user = Model::factory('User')->create();
    }
    if (!$user->identity) {
      $user->identity = $openidData['identity'];
    }
    if (isset($openidData['nickname'])) {
      $user->nickname = $openidData['nickname'];
    }
    if (isset($openidData['fullname'])) {
      $user->name = $openidData['fullname'];
    }
    if (isset($openidData['email'])) {
      $user->email = $openidData['email'];
    }
    $user->save();
    return $user;
  }
}

?>
