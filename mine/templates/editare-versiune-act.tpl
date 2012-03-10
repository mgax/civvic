<h3>
  Editează versiunea {$av->versionNumber} pentru {$act->name}
</h3>

<form action="editare-versiune-act" method="post">
  <input type="hidden" name="id" value="{$av->id}"/>

  Cauzată de {include file=bits/actAutocomplete.tpl name="modifyingActId" actTypes=$actTypes selected=$modifyingAct autofocus=true}<br/>

  Starea: {include file=bits/actStatusDropdown.tpl name="status" actStatuses=$actStatuses selected=$av->status}<br/>

  Conținutul: <a id="togglePreviewLink" href="#">{if $preview}ascunde HTML{else}arată HTML{/if}</a><br/>

  <div id="wikiHtmlPreview" class="wikiHtmlPreview" {if !$preview}style="display: none"{/if}>{$av->htmlContents}</div>

  <textarea name="contents" rows="20">{$av->contents}</textarea><br/>

  <input type="submit" name="previewButton" value="Previzualizează"/>
  <input type="submit" name="submitButton" value="Salvează"/>
</form>

<br/>
<a class="delete" href="editare-versiune-act?deleteId={$av->id}"
   onclick="return confirm('Confirmați ștergerea versiunii {$av->versionNumber}?');">șterge</a>

<br/>
<a href="editare-act?id={$av->actId}">înapoi la act</a>

<script type="text/javascript">
  {literal}
  $('#togglePreviewLink').click(function(ev) { 
    $('#wikiHtmlPreview').toggle('fast'); 
    $(this).text(($('#togglePreviewLink').text() == 'arată HTML') ? 'ascunde HTML' : 'arată HTML');
   });
  {/literal}
</script>
