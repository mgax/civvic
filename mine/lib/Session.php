<?php

class Session {

  static function init() {
    if (isset($_COOKIE[session_name()])) {
      session_start();
    }
  }

  static function get($name, $default = null) {
    return (isset($_SESSION) && array_key_exists($name, $_SESSION)) ? $_SESSION[$name] : $default;
  }

  static function set($var, $value) {
    // Lazy start of the session so we don't send a PHPSESSID cookie unless we have to
    if (!isset($_SESSION)) {
      session_start();
    }
    $_SESSION[$var] = $value;
  }

  static function unsetVariable($var) {
    if (isset($_SESSION)) {
      unset($_SESSION[$var]);
      if (!count($_SESSION)) {
        // Note that this will prevent us from creating another session this same request.
        // This does not seem to cause a problem at the moment.
        self::kill();
      }
    }
  }

  static function getUser() {
    return self::get('user');
  }

  private static function kill() {
    if (!isset($_SESSION)) {
      session_start(); // It has to have been started in order to be destroyed.
    }
    session_unset();
    session_destroy();
    if (ini_get("session.use_cookies")) {
      setcookie(session_name(), '', time() - 3600, '/'); // expire it
    }
  }
}

?>
