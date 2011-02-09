<?php

class PdfController extends AppController {
  var $uses = array('PdfDocument', 'RawText');

  /*
   * Renders a PDF document. No view is necessary.
   */
  function view($rawTextId) {
    $rawText = $this->RawText->read(array('issue', 'year'), $rawTextId);
    $pdfDocument = $this->PdfDocument->find('first', array('conditions' => array('raw_text_id' => $rawTextId)));
    header('Content-type: application/pdf'); 
    header("Content-Disposition: attachment; filename=mo-{$rawText['RawText']['issue']}-{$rawText['RawText']['year']}.pdf");
    print $pdfDocument['PdfDocument']['contents'];
    exit;
  }
}

?>
