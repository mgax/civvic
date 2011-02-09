<h1>Monitorul Oficial <?php echo $rawText['RawText']['issue'] . "/" . $rawText['RawText']['year'] ?></h1>

<p>
  Procesat: <?php echo $rawText['RawText']['created'] ?><br/>
  Tipul documentului PDF: <?php echo $rawText['RawText']['script_type'] ?><br/>
  Versiunea scriptului de procesare: <?php echo $rawText['RawText']['script_version'] ?><br/>
</p>

<div id="rawOcrText">
  <?php echo $html->link('vezi strict textul (pentru copiere)', '/raw_texts/view_text_only/' . $rawText['RawText']['id']); ?> |
  <?php echo $html->link('descarcă PDF original', '/pdf/view/' . $rawText['RawText']['id']); ?>
  <pre><?php echo $rawText['RawText']['extracted_text'] ?></pre>
</div>

