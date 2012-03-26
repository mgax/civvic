<?php

class RawText extends BaseObject {

  const PROGRESS_NEW = 0;
  const PROGRESS_ASSIGNED = 1;
  const PROGRESS_COMPLETE = 2;
  const PROGRESS_ERROR = 3;
  const PROGRESS_VERIFIED = 4;
  static $progresses = array('nou', 'repartizat', 'complet', 'eroare', 'verificat');

  const DIFFICULTY_LOW = 1;
  const DIFFICULTY_MEDIUM = 2;
  const DIFFICULTY_HIGH = 3;
  static $difficulties = array('', 'scăzută', 'medie', 'ridicată');

}

?>
