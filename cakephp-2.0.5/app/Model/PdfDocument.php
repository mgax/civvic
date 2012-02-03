<?php

App::uses('AppModel', 'Model');

class PdfDocument extends AppModel {
  public $name = 'PdfDocument';
  public $belongsTo = 'RawText';
}

?>
