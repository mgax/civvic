<h3>
  {if $act->id}
    Editează actul '{$act->name}'
  {else}
    Creează un act
  {/if}
</h3>

<form action="editare-act" method="post">
  {if $act->id}
    <input type="hidden" name="id" value="{$act->id}"/>
  {/if}
  Nume: <input type="text" name="name" value="{$act->name}"/><br/>
  An: <input type="text" name="year" value="{$act->year}"/><br/>

  Tip: {include file="bits/actTypeDropdown.tpl" name="actTypeId" actTypes=$actTypes selected=$act->actTypeId}<br/>
  Stare: {include file="bits/actStatusDropdown.tpl" name="status" actStatuses=$actStatuses selected=$act->status}<br/>

  <input type="submit" name="submitButton" value="Salvează"/>
</form>

<br/>

{if $act->id}
  <a class="delete" href="editare-act?deleteId={$act->id}"
     onclick="return confirm('Confirmați ștergerea actului \'{$act->name}\'?');">șterge</a>
  <br/><br/>
{/if}

<a href="acte">înapoi la lista de acte</a>
