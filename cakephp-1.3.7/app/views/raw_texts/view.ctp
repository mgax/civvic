<h1>Monitorul Oficial <?php echo $rawText['RawText']['issue'] . "/" . $rawText['RawText']['year'] ?></h1>

<p>
  <?php echo $html->link('înapoi la lista Monitoarelor Oficiale', 'index'); ?>
</p>

<table>
  <tr>
    <td>Revendicat:</td>
    <td>
      <?php if ($rawText['RawText']['owner']): ?>
        <?php echo User::displayValue($rawText['User']); ?>
      <?php else: ?>
        nu
      <?php endif; ?>

      <?php if ($canClaim): ?>
        <?php echo $html->link('revendică', '/raw_texts/claim/' . $rawText['RawText']['id']); ?>
      <?php endif; ?>

      <?php if ($owns): ?>
        <?php echo $html->link('renunță', '/raw_texts/unclaim/' . $rawText['RawText']['id']); ?>
      <?php endif; ?>
    </td>
  </tr>
  <?php if ($owns || $admin): ?>
    <tr>
      <td>Stadiu:</td>
      <td>
        <?php foreach($progresses as $p => $displayValue): ?>
          <?php if ($p != RawText::PROGRESS_NEW) { ?>|<?php } ?>
          <?php if ($rawText['RawText']['progress'] == $p): ?>
            <b><?=$displayValue?></b>
          <?php else: ?>
            <?php echo $html->link($displayValue, '/raw_texts/set_progress/' . $rawText['RawText']['id'] . '/' . $p); ?>
          <?php endif; ?>
        <?php endforeach; ?>
      </td>
    </tr>
    <tr>
      <td>Dificultate:</td>
      <td>
        <?php foreach($difficulties as $d => $displayValue): ?>
          <?php if ($d != RawText::DIFFICULTY_LOW) { ?>|<?php } ?>
          <?php if ($rawText['RawText']['difficulty'] == $d): ?>
            <b><?=$displayValue?></b>
          <?php else: ?>
            <?php echo $html->link($displayValue, '/raw_texts/set_difficulty/' . $rawText['RawText']['id'] . '/' . $d); ?>
          <?php endif; ?>
        <?php endforeach; ?>
      </td>
    </tr>
  <?php else: ?>
    <tr>
      <td>Stadiu:</td>
      <td><?php echo RawText::progresses($rawText['RawText']['progress']); ?></td>
    </tr>
    <tr>
      <td>Dificultate:</td>
      <td><?php echo RawText::difficulties($rawText['RawText']['difficulty']); ?></td>
    </tr>
  <?php endif; ?>
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
  </tr>
  <tr>
    <td>Pagina Civvic.ro:</td>
    <td><a href="<?php echo $wikiUrl ?>" target="_blank"><?php echo $wikiUrl ?></a></td>
  </tr>
</table>

<div id="rawOcrText">
  <?php echo $html->link('vezi strict textul (pentru copiere)', '/raw_texts/view_text_only/' . $rawText['RawText']['id']); ?> |
  <?php echo $html->link('descarcă PDF original', '/pdf/view/' . $rawText['RawText']['id']); ?>
  <pre><?php echo $rawText['RawText']['extracted_text'] ?></pre>
</div>

