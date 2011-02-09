<?php

define('ADODB_ASSOC_CASE', 2);
$ADODB_ASSOC_CASE = 2;
ADOdb_Active_Record::$_changeNames = false; // Do not pluralize table names

class BaseObject extends ADOdb_Active_Record {
  public function save() {
    if ($this->created === null) {
      $this->created = $this->modified = date('Y-m-d H:i:s');
    }
    if (is_string($this->modified)) {
      $this->modified = date('Y-m-d H:i:s');
    }
    parent::save();
  }
}

class PdfDocument extends BaseObject {
  var $_table = 'pdf_documents';

  public static function get($where) {
    $obj = new PdfDocument();
    $obj->load($where);
    return $obj->id ? $obj : null;
  }
}

class RawText extends BaseObject {
  var $_table = 'raw_texts';

  public static function get($where) {
    $obj = new RawText();
    $obj->load($where);
    return $obj->id ? $obj : null;
  }
}

class Word extends BaseObject {
  var $_table = 'Word';
}

?>
