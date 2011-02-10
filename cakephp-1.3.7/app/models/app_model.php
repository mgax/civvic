<?php

class AppModel extends Model {
  /**
   * static enums
   * @access static
   */
  static function enum($value, $options, $default = '') {
    if ($value !== null) {
      if (array_key_exists($value, $options)) {
        return $options[$value];
      }
      return $default;
    }
    return $options;
  }
}

?>
