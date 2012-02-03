<?php

App::uses('AppModel', 'Model');

class RawText extends AppModel {
  public $name = 'RawText';
  public $hasOne = 'PdfDocument';
  public $belongsTo = array('User' => array('className' => 'User', 'foreignKey' => 'owner')); 

  const PROGRESS_NEW = 0;
  const PROGRESS_ASSIGNED = 1;
  const PROGRESS_COMPLETE = 2;
  const PROGRESS_ERROR = 3;
  const PROGRESS_VERIFIED = 4;

  const DIFFICULTY_LOW = 1;
  const DIFFICULTY_MEDIUM = 2;
  const DIFFICULTY_HIGH = 3;

  static function progresses($value = null) {
    $options = array(self::PROGRESS_NEW => _('progressNew'),
                     self::PROGRESS_ASSIGNED => _('progressAssigned'),
                     self::PROGRESS_COMPLETE => _('progressComplete'),
                     self::PROGRESS_ERROR => _('progressError'),
                     self::PROGRESS_VERIFIED => _('progressVerified'),
                     );
    return parent::enum($value, $options);
  }

  static function difficulties($value = null) {
    $options = array(self::DIFFICULTY_LOW => _('difficultyLow'),
                     self::DIFFICULTY_MEDIUM => _('difficultyMedium'),
                     self::DIFFICULTY_HIGH => _('difficultyHigh'),
                     );
    return parent::enum($value, $options);
  }
}
?>
