<?php

require_once __DIR__ . '/../lib/Util.php';

$OLD_DB = 'civvic_tools';

$oldOwners = ORM::for_table('variable')->raw_query("select * from $OLD_DB.users", null)->find_many();
$oldOwnerMap = array();
foreach ($oldOwners as $row) {
  $oldOwnerMap[$row->id] = $row;
}

$oldPages = ORM::for_table('variable')->raw_query("select raw_text_id, page_count from $OLD_DB.pdf_documents", null)->find_many();
$oldPageMap = array();
foreach ($oldPages as $row) {
  $oldPageMap[$row->raw_text_id] = $row->page_count;
}

$oldData = ORM::for_table('variable')->raw_query("select * from $OLD_DB.raw_texts", null)->find_many();
foreach ($oldData as $row) {
  $rt = Model::factory('RawText')->create();
  $rt->year = $row->year;
  $rt->number = $row->issue;
  $rt->extractedText = $row->extracted_text;
  $rt->pageCount = $oldPageMap[$row->id];
  $rt->progress = $row->progress;
  $rt->difficulty = $row->difficulty;
  $rt->created = strtotime($row->created);

  if ($row->owner) {
    $user = User::get_by_identity($oldOwnerMap[$row->owner]->openid);
    if (!$user) {
      // Migrate the user
      $oldUser = $oldOwnerMap[$row->owner];
      $user = Model::factory('User')->create();
      $user->identity = $oldUser->openid;
      $user->nickname = $oldUser->nickname;
      $user->email = $oldUser->email;
      $user->save();
      print "Imported user {$user->identity}\n";
    }
    $rt->userId = $user->id;
  }
  $rt->save();
}

?>
