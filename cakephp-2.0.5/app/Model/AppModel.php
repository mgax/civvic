<?php

App::uses('Model', 'Model');

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

  function save($data = null, $validate = true, $fieldList = array()) {
    //clear modified field value before each save
    if (isset($this->data) && isset($this->data[$this->name])) {
      unset($this->data[$this->name]['modified']);
    }
    if (isset($data) && isset($data[$this->name])) {
      unset($data[$this->name]['modified']);
    }
    return parent::save($data, $validate, $fieldList);
  }
}

?>
