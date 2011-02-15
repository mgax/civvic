<?php

class RawTextsController extends AppController {
  var $uses = array('PdfDocument', 'RawText', 'User');

  function index() {
    $this->set('rawTexts', $this->RawText->find('all'));
  }

  function view($id) {
    $sessionUser = $this->Session->read('user');
    $this->RawText->id = $id;
    $rawText = $this->RawText->read();
    $this->set('rawText', $rawText);
    $this->set('canClaim', $rawText['RawText']['progress'] == RawText::PROGRESS_NEW);
    $this->set('canUnclaim', $rawText['RawText']['progress'] == RawText::PROGRESS_ASSIGNED && $rawText['RawText']['owner'] == $sessionUser['User']['id']);
  }

  function view_text_only($id) {
    $this->autoLayout = false; 
    $this->RawText->id = $id;
    $this->set('rawText', $this->RawText->read());
  }

  function claim($id) {
    $this->RawText->id = $id;
    $rawText = $this->RawText->read();
    $user = $this->Session->read('user');
    if (!$user) {
      $this->Session->setFlash(_('You need to be authenticated to claim a PDF document.'), 'flash_failure');
    } else if ($rawText['RawText']['progress'] == RawText::PROGRESS_ASSIGNED) {
      $this->Session->setFlash(_('This PDF document is already assigned.'), 'flash_failure');
    } else if ($rawText['RawText']['progress'] == RawText::PROGRESS_COMPLETE) {
      $this->Session->setFlash(_('This PDF document is already marked as completed.'), 'flash_failure');
    } else if ($rawText['RawText']['progress'] == RawText::PROGRESS_ERROR) {
      $this->Session->setFlash(_('This PDF document is in an error state and needs to be reviewed by an administrator.'), 'flash_failure');
    } else {
      $this->RawText->set('owner', $user['User']['id']);
      $this->RawText->set('progress', RawText::PROGRESS_ASSIGNED);
      $this->RawText->save();
      $this->Session->setFlash(_('Document claimed.'), 'flash_success');
    }
    $this->redirect("/raw_texts/view/{$id}");
    exit;
  }

  function unclaim($id) {
    $sessionUser = $this->Session->read('user');
    $this->RawText->id = $id;
    $rawText = $this->RawText->read();
    $user = $this->Session->read('user');
    if ($rawText['RawText']['progress'] == RawText::PROGRESS_ASSIGNED && $rawText['RawText']['owner'] == $sessionUser['User']['id']) {
      $this->RawText->set('owner', null);
      $this->RawText->set('progress', RawText::PROGRESS_NEW);
      $this->RawText->save();
      $this->Session->setFlash(_('Document unclaimed.'), 'flash_success');
    } else if (!$user) {
      $this->Session->setFlash(_('You need to be authenticated to unclaim a PDF document.'), 'flash_failure');
    } else {
      $this->Session->setFlash(_('You do not own this PDF document.'), 'flash_failure');
    }
    $this->redirect("/raw_texts/view/{$id}");
    exit;
  }
}

?>
