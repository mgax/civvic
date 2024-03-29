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
  Tip: {include file="bits/actTypeDropdown.tpl" name="actTypeId" actTypes=$actTypes selected=$act->actTypeId autofocus=true}&nbsp;
  număr: <input type="text" name="number" value="{$act->number}" size="4"/>&nbsp;
  an: <input type="text" name="year" value="{$act->year}" size="4"/><br/>
  Nume: <input type="text" name="name" value="{$act->name}" size="80" autocomplete="off"/><br/>
  Data: {include file="bits/datePicker.tpl" id="issueDate" name="issueDate" value=$act->issueDate}<br/>
  Autor(i): {include file="bits/authorAutocompleteMultiple.tpl" name="authors" authors=$authors}<br/>
  Publicat în {include file=bits/monitorDropdown.tpl name="monitorId" monitors=$monitors selected=$act->monitorId}<br/>
  Locul: {include file=bits/placeDropdown.tpl name="placeId" places=$places selected=$act->placeId}<br/>
  Comentariu: <br/>
  <textarea name="comment" rows="3">{$act->comment}</textarea><br/>
  <input type="submit" name="submitButton" value="Salvează"/>
</form>

<br/>
{if $act->id}
  <a class="delete" href="editare-act?deleteId={$act->id}"
     onclick="return confirm('Confirmați ștergerea actului \'{$act->name}\'?');">șterge</a>
  <br/>

  <h3>Lista de versiuni</h3>

  <table class="actVersionTable">
    <tr>
      <th>nr.</th>
      <th>acțiuni</th>
    </tr>
    {foreach from=$actVersions item=av}
      <tr>
        <td>{$av->versionNumber}</td>
        <td><span class="actEditLinks"><a href="editare-versiune-act?id={$av->id}">editează</a></span></td>
      </tr>
    {/foreach}
  </table>

  <form action="editare-act" method="post">
    <input type="hidden" name="id" value="{$act->id}"/>
    Adaugă o versiune [
    <input type="radio" id="versionPlacementBefore" name="versionPlacement" value="before"/>
    <label for="versionPlacementBefore">înainte</label>
    <input type="radio" id="versionPlacementAfter" name="versionPlacement" value="after" checked="checked"/>
    <label for="versionPlacementAfter">după</label>
    ] versiunea
    <input type="text" name="otherVersionNumber" value="{$numVersions}"/>
    <input type="submit" name="addVersionButton" value="Adaugă"/>
  </form>
{/if}

<br/>
<a href="act?id={$act->id}">înapoi la act</a>
