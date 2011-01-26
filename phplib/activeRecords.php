<?php

define('ADODB_ASSOC_CASE', 2);
$ADODB_ASSOC_CASE = 2;
ADOdb_Active_Record::$_changeNames = false; // Do not pluralize table names

class BaseObject extends ADOdb_Active_Record {
  public function save() {
    if ($this->createDate === null) {
      $this->createDate = $this->modDate = time();
    }
    if (is_string($this->modDate)) {
      $this->modDate = time();
    }
    parent::save();
  }
}

class PdfDocument extends BaseObject {
  public static function get($where) {
    $obj = new PdfDocument();
    $obj->load($where);
    return $obj->id ? $obj : null;
  }
}

class RawText extends BaseObject {
  public static function get($where) {
    $obj = new RawText();
    $obj->load($where);
    return $obj->id ? $obj : null;
  }
}

class Word extends BaseObject {
}

?>
