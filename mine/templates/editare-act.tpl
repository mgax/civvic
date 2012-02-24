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
  Tip: {include file="bits/actTypeDropdown.tpl" name="actTypeId" id="actTypeId" actTypes=$actTypes selected=$act->actTypeId}&nbsp;
  număr: <input type="text" name="number" value="{$act->number}" size="4"/>&nbsp;
  an: <input type="text" name="year" value="{$act->year}" size="4"/><br/>
  Nume: <input type="text" name="name" value="{$act->name}" size="80"/><br/>
  Data: {include file="bits/datePicker.tpl" id="issueDate" name="issueDate" value=$act->issueDate}<br/>
  Autor: {include file="bits/authorDropdown.tpl" name="authorId" authors=$authors selected=$act->authorId}<br/>
  Publicată în {include file=bits/monitorDropdown.tpl name="monitorId" monitors=$monitors selected=$act->monitorId}<br/>
  Locul: {include file=bits/placeDropdown.tpl name="placeId" places=$places selected=$act->placeId}<br/>

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
        <td><a href="editare-versiune-act?id={$av->id}">editează</a></td>
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
<a href="act?id={$act->id}">înapoi la act</a> |
<a href="acte">înapoi la lista de acte</a>

<script type="text/javascript">
  $('#actTypeId').focus();
</script>
