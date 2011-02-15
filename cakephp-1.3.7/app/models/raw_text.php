<?php

class RawText extends AppModel {
  var $name = 'RawText';
  var $hasOne = 'PdfDocument';
  var $belongsTo = array('User' => array('className' => 'User', 'foreignKey' => 'owner')); 

  const PROGRESS_NEW = 0;
  const PROGRESS_ASSIGNED = 1;
  const PROGRESS_COMPLETE = 2;
  const PROGRESS_ERROR = 3;

  static function progresses($value = null) {
    $options = array(self::PROGRESS_NEW => _('progressNew'),
                     self::PROGRESS_ASSIGNED => _('progressAssigned'),
                     self::PROGRESS_COMPLETE => _('progressComplete'),
                     self::PROGRESS_ERROR => _('progressError'),
                     );
    return parent::enum($value, $options);
  }
}
?>
