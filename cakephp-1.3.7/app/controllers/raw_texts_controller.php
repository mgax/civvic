<?php

class RawTextsController extends AppController {
  var $uses = array('PdfDocument', 'RawText');

  function index() {
    $this->set('rawTexts', $this->RawText->find('all'));
  }

  function view($id) {
    $this->RawText->id = $id;
    $this->set('rawText', $this->RawText->read());
  }

  function view_text_only($id) {
    $this->autoLayout = false; 
    $this->RawText->id = $id;
    $this->set('rawText', $this->RawText->read());
  }
}

?>
