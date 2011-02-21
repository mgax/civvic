<?php

class RawTextsController extends AppController {
  var $uses = array('PdfDocument', 'RawText');

  function index() {
    $sessionUser = $this->Session->read('user');
    $conditions = array();
    $limit = 50;
    if (!empty($this->data)) {
      $data = $this->data['RawText'];
      if ($data['year']['year']) {
        $conditions['year'] = $data['year']['year'];
      }
      if ($data['Progress'] !== '') {
        $conditions['progress'] = $data['Progress'];
      }
      if ($data['mine'] && $sessionUser) {
        $conditions['owner'] = $sessionUser['User']['id'];
      }
      if ($data['limit']) {
        $limit = $data['limit'];
      }
    }
    $this->set('rawTexts', $this->RawText->find('all', array('conditions' => $conditions, 'order' => array('year asc', 'issue + 0 asc'), 'limit' => $limit)));
    $this->set('progresses', RawText::progresses());
  }

  function view($id) {
    $sessionUser = $this->Session->read('user');
    $this->RawText->id = $id;
    $rawText = $this->RawText->read();
    $this->set('rawText', $rawText);
    $this->set('canClaim', !$rawText['RawText']['owner']);
    $this->set('owns', $rawText['RawText']['owner'] == $sessionUser['User']['id']);
  }

  function view_text_only($id) {
    $this->autoLayout = false; 
    $this->RawText->id = $id;
    $this->set('rawText', $this->RawText->read());
  }

  function claim($id) {
    $sessionUser = $this->Session->read('user');
    $this->RawText->id = $id;
    $rawText = $this->RawText->read();
    if (!$sessionUser) {
      $this->Session->setFlash(_('You need to be authenticated to claim a PDF document.'), 'flash_failure');
    } else if ($rawText['RawText']['owner']) {
      $this->Session->setFlash(_('This PDF document is already assigned.'), 'flash_failure');
    } else {
      $this->RawText->set('owner', $sessionUser['User']['id']);
      if ($rawText['RawText']['progress'] == RawText::PROGRESS_NEW) {
        $this->RawText->set('progress', RawText::PROGRESS_ASSIGNED);
      }
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
    if ($rawText['RawText']['owner'] == $sessionUser['User']['id']) {
      $this->RawText->set('owner', null);
      // Once the document is complete / error, don't revert to PROGRESS_NEW.
      if ($rawText['RawText']['progress'] == RawText::PROGRESS_ASSIGNED) {
        $this->RawText->set('progress', RawText::PROGRESS_NEW);
      }
      $this->RawText->save();
      $this->Session->setFlash(_('Document unclaimed.'), 'flash_success');
    } else if (!$sessionUser) {
      $this->Session->setFlash(_('You need to be authenticated to unclaim a PDF document.'), 'flash_failure');
    } else {
      $this->Session->setFlash(_('You do not own this PDF document.'), 'flash_failure');
    }
    $this->redirect("/raw_texts/view/{$id}");
    exit;
  }

  function set_progress($id, $progress) {
    $sessionUser = $this->Session->read('user');
    $this->RawText->id = $id;
    $rawText = $this->RawText->read();
    if ($rawText['RawText']['owner'] == $sessionUser['User']['id']) {
      $this->RawText->set('progress', $progress);
      $this->RawText->save();
      $this->Session->setFlash(_('Progress updated.'), 'flash_success');
    } else if (!$sessionUser) {
      $this->Session->setFlash(_('You need to be authenticated to update a document\'s progress.'), 'flash_failure');
    } else {
      $this->Session->setFlash(_('You do not own this PDF document.'), 'flash_failure');
    }
    $this->redirect("/raw_texts/view/{$id}");
    exit;
  }
}

?>
