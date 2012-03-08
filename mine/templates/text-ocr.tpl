<h3>Text OCR pentru Monitorul Oficial {$rawText->number}/{$rawText->year}</h3>

<ul>
  <li>Pagini: {$rawText->pageCount}</li>
  <li>Dificultate: {$difficulty}</li>
  <li>Stadiu: {$progress}</li>
  <li>Deținător: {if $owner}{$owner->identity}{else}nimeni{/if}
</ul>

Text (click <a href="text-ocr?id={$rawText->id}&amp;plain=1">aici</a> pentru textul pe o pagină separată):

<div class="rawText"><pre>{$rawText->extractedText}</pre></div>
