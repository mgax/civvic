<h1>Monitorul Oficial <?php echo $rawText['RawText']['issue'] . "/" . $rawText['RawText']['year'] ?></h1>

<table>
  <tr>
    <td>Stadiu:</td>
    <td>
      <?php echo RawText::progresses($rawText['RawText']['progress']) ?>
      <?php if ($rawText['RawText']['progress'] == RawText::PROGRESS_ASSIGNED): ?>
        (<?php echo $rawText['User']['openid']; ?>)
      <?php endif; ?>

      <?php if ($canClaim): ?>
        <?php echo $html->link('revendică', '/raw_texts/claim/' . $rawText['RawText']['id']); ?>
      <?php endif; ?>

      <?php if ($canUnclaim): ?>
        <?php echo $html->link('renunță', '/raw_texts/unclaim/' . $rawText['RawText']['id']); ?>
      <?php endif; ?>
    </td>
  <tr>
    <td>Pagini:</td>
    <td><?php echo $rawText['PdfDocument']['page_count'] ?></td>
  </tr>
  <tr>
    <td>Procesat:</td>
    <td>
      <?php echo $rawText['RawText']['created'] ?>,
      scriptul <?php echo $rawText['RawText']['script_type'] ?>,
      versiunea <?php echo $rawText['RawText']['script_version'] ?>
    </td>
</table>

<div id="rawOcrText">
  <?php echo $html->link('vezi strict textul (pentru copiere)', '/raw_texts/view_text_only/' . $rawText['RawText']['id']); ?> |
  <?php echo $html->link('descarcă PDF original', '/pdf/view/' . $rawText['RawText']['id']); ?>
  <pre><?php echo $rawText['RawText']['extracted_text'] ?></pre>
</div>

