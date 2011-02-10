<?php

class RawTextsController extends AppController {
  var $uses = array('PdfDocument', 'RawText', 'User');

  function index() {
    $this->set('rawTexts', $this->RawText->find('all'));
  }

  function view($id) {
    $this->RawText->id = $id;
    $rawText = $this->RawText->read();
    $this->set('rawText', $rawText);
    $owner = $rawText['RawText']['owner'] ? $this->User->find('first', array('conditions' => array('id' => $rawText['RawText']['owner']))) : null;
  }

  function view_text_only($id) {
    $this->autoLayout = false; 
    $this->RawText->id = $id;
    $this->set('rawText', $this->RawText->read());
  }
}

?>
