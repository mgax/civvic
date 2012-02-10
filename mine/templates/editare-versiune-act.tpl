<h3>
  Editează versiunea {$av->versionNumber}
</h3>

<form action="editare-versiune-act" method="post">
  <input type="hidden" name="id" value="{$av->id}"/>

  Cauzată de {include file=bits/actDropdown.tpl name="modifyingActId" acts=$acts actTypes=$actTypes selected=$av->modifyingActId}<br/>

  Starea: {include file=bits/actStatusDropdown.tpl name="status" actStatuses=$actStatuses selected=$av->status}<br/>

  Conținutul:<br/>
  <textarea name="contents" rows="20">{$av->contents}</textarea><br/>

  <input type="submit" name="submitButton" value="Salvează"/>
</form>

<br/>
<a class="delete" href="editare-versiune-act?deleteId={$av->id}"
   onclick="return confirm('Confirmați ștergerea versiunii {$av->versionNumber}?');">șterge</a>

<br/>
<a href="editare-act?id={$av->actId}">înapoi la act</a>
