<?php

class RawTextsController extends AppController {
  var $uses = array('PdfDocument', 'RawText', 'User');

  function index() {
    $sessionUser = $this->Session->read('user');
    $conditions = array();
    if (empty($this->request->data)) {
      $conditions['progress'] = RawText::PROGRESS_NEW;
      $this->request->data['RawText']['Progress'] = RawText::PROGRESS_NEW;
      $this->set('selectedOwner', 'anyone');
    } else {
      $data = $this->request->data['RawText'];
      if ($data['year']['year']) {
        $conditions['year'] = $data['year']['year'];
      }
      if ($data['Progress'] !== '') {
        $conditions['progress'] = $data['Progress'];
      }
      if ($data['Difficulty'] !== '') {
        $conditions['difficulty'] = $data['Difficulty'];
      }
      if ($sessionUser && $data['ownerChoices'] == 'mine') {
        $conditions['owner'] = $sessionUser['User']['id'];
      } else if ($data['ownerChoices'] == 'substring' && $data['owner']) {
        $conditions['OR'] = array(
          'User.openid like' => "%{$data['owner']}%",
          'User.email like' => "%{$data['owner']}%",
          'User.nickname like' => "%{$data['owner']}%",
        );
      }
      $this->set('selectedOwner', $data['ownerChoices']);
    }
    $this->set('rawTexts', $this->RawText->find('all', array('conditions' => $conditions, 'order' => array('year asc', 'issue + 0 asc'), 'limit' => 200)));
    $this->set('ownerChoices', $sessionUser
               ? array('anyone' => 'oricine', 'mine' => 'mine', 'substring' => 'persoana (subșir):')
               : array('anyone' => 'oricine', 'substring' => 'persoana (subșir):')); // No "mine" option unless user is logged in
    $this->set('progresses', RawText::progresses());
    $this->set('difficulties', RawText::difficulties());
  }

  function view($id) {
    $sessionUser = $this->Session->read('user');
    $this->RawText->id = $id;
    $rawText = $this->RawText->read();
    $this->set('rawText', $rawText);
    $this->set('wikiUrl', "http://civvic.ro/wiki/Monitorul_Oficial_{$rawText['RawText']['issue']}/{$rawText['RawText']['year']}");
    $this->set('canClaim', !$rawText['RawText']['owner']);
    $this->set('owns', $sessionUser && $rawText['RawText']['owner'] == $sessionUser['User']['id']);
    $this->set('admin', $sessionUser && $sessionUser['User']['admin']);
    $this->set('progresses', RawText::progresses());
    $this->set('difficulties', RawText::difficulties());
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
    if ($sessionUser['User']['admin'] ||
        ($progress != RawText::PROGRESS_VERIFIED && $rawText['RawText']['progress'] != RawText::PROGRESS_VERIFIED && $rawText['RawText']['owner'] == $sessionUser['User']['id'])) {
      $this->RawText->set('progress', $progress);
      $this->RawText->save();
      $this->Session->setFlash(_('Progress updated.'), 'flash_success');
    } else if ($progress == RawText::PROGRESS_VERIFIED || $rawText['RawText']['progress'] == RawText::PROGRESS_VERIFIED) {
      $this->Session->setFlash(_('Only admins can verify / unverify a document.'), 'flash_failure');
    } else if (!$sessionUser) {
      $this->Session->setFlash(_('You need to be authenticated to update a document\'s progress.'), 'flash_failure');
    } else {
      $this->Session->setFlash(_('Only admins and owners can update a document\'s progress.'), 'flash_failure');
    }
    $this->redirect("/raw_texts/view/{$id}");
    exit;
  }

  function set_difficulty($id, $difficulty) {
    $sessionUser = $this->Session->read('user');
    $this->RawText->id = $id;
    $rawText = $this->RawText->read();
    if ($sessionUser['User']['admin'] || $rawText['RawText']['owner'] == $sessionUser['User']['id']) {
      $this->RawText->set('difficulty', $difficulty);
      $this->RawText->save();
      $this->Session->setFlash(_('Difficulty updated.'), 'flash_success');
    } else if (!$sessionUser) {
      $this->Session->setFlash(_('You need to be authenticated to update a document\'s difficulty.'), 'flash_failure');
    } else {
      $this->Session->setFlash(_('Only admins and owners can update a document\'s difficulty.'), 'flash_failure');
    }
    $this->redirect("/raw_texts/view/{$id}");
    exit;
  }

  function report() {
    $sessionUser = $this->Session->read('user');
    if (!$sessionUser || !$sessionUser['User']['admin']) {
      $this->Session->setFlash(_('Only admins can view reports.'), 'flash_failure');
      $this->redirect('/');
    }
    $this->set('rawTexts', $this->RawText->findAllByProgress(RawText::PROGRESS_COMPLETE));
    $totals = $this->RawText->query(sprintf("select User.id, RawText.difficulty, sum(PdfDocument.page_count) as pages from users User, raw_texts RawText, pdf_documents PdfDocument " .
                                            "where User.id = RawText.owner and RawText.id = PdfDocument.raw_text_id and RawText.progress = %d ".
                                            "group by User.id, RawText.difficulty;", RawText::PROGRESS_COMPLETE));
    $users = array();
    $pageCounts = array();
    foreach ($totals as $row) {
      $userId = $row['User']['id'];
      if (!array_key_exists($userId, $users)) {
        $users[$userId] = $this->User->findById($userId);
      }
      if (!array_key_exists($userId, $pageCounts)) {
        $pageCounts[$userId] = array(RawText::DIFFICULTY_LOW => 0, RawText::DIFFICULTY_MEDIUM => 0, RawText::DIFFICULTY_HIGH => 0);
      }
      $pageCounts[$userId][$row['RawText']['difficulty']] = $row[0]['pages'];
    }
    $this->set('users', $users);
    $this->set('pageCounts', $pageCounts);
    $this->set('difficulties', RawText::difficulties());
  }

  function mark_verified($userId) {
    $sessionUser = $this->Session->read('user');
    if (!$sessionUser || !$sessionUser['User']['admin']) {
      $this->Session->setFlash(_('Only admins can verify / unverify a document.'), 'flash_failure');
      $this->redirect('/');
    }
    $this->RawText->updateAll(array('progress' => RawText::PROGRESS_VERIFIED), array('owner' => $userId, 'progress' => RawText::PROGRESS_COMPLETE));
    $this->redirect('/raw_texts/report');
  }
}

?>
