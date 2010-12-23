<?php

require_once(CONF_ADODB_CLASS);
require_once(CONF_ADODB_ACTIVE_RECORD_CLASS);

function db_init($connector) {
  $db = NewADOConnection($connector) or die("Connection to resource $connector failed");
  ADOdb_Active_Record::SetDatabaseAdapter($db);
  $db->Execute('set names utf8');
  $GLOBALS['db'] = $db;
  // $db->debug = true; //just for debug
}

function db_execute($query) {
  return $GLOBALS['db']->execute($query);
}

// One-line syntactic sugar for find()
function db_find($obj, $where) {
  return $obj->find($where);
}

function db_getSingleValue($query) {
  $recordSet = db_execute($query);
  return $recordSet->fields[0];
}

?>
