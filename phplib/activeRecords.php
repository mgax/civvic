<?php

define('ADODB_ASSOC_CASE', 2);
$ADODB_ASSOC_CASE = 2;
ADOdb_Active_Record::$_changeNames = false; // Do not pluralize table names

class Word extends ADOdb_Active_Record {
}

?>
