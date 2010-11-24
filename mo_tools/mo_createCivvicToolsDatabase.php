<?php

require_once('phplib/config.php');
require_once('phplib/db.php');

db_init(CONF_DATABASE_TOOLS);
db_execute('drop table if exists Word');
db_execute('create table Word(id int not null auto_increment, form varchar(100) collate utf8_romanian_ci, formUtf8General varchar(100) collate utf8_general_ci, frequent int not null, ' .
           'primary key(id), key(form), key(formUtf8General));');

$files = array(array('name' => CONF_WORDLIST_FREQUENT, 'frequent' => 1),
               array('name' => CONF_WORDLIST_REGULAR, 'frequent' => 0));

foreach ($files as $fileDesc) {
  $words = gzfile($fileDesc['name']);
  foreach ($words as $word) {
    $word = trim($word);
    db_execute(sprintf("insert into Word set form = '%s', formUtf8General = '%s', frequent = %d", $word, $word, $fileDesc['frequent']));
  }
}

?>
