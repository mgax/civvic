<h3>
  {if $monitor->id}
    Editează Monitorul Oficial {$monitor->number} / {$monitor->year}
  {else}
    Creează un monitor oficial
  {/if}
</h3>

<form action="editare-monitor" method="post">
  {if $monitor->id}
    <input type="hidden" name="id" value="{$monitor->id}"/>
  {/if}
  An: <input type="text" name="year" value="{$monitor->year}"/><br/>
  Număr: <input type="text" name="number" value="{$monitor->number}"/><br/>
  Data: {include file="bits/datePicker.tpl" id="issueDate" name="issueDate" value=$monitor->issueDate}<br/>

  <input type="submit" name="submitButton" value="Salvează"/>
</form>

<br/>
{if $monitor->id}
  <a class="delete" href="editare-monitor?deleteId={$monitor->id}"
     onclick="return confirm('Confirmați ștergerea monitorului {$monitor->number} / {$monitor->year}?');">șterge</a>
  <br/>

{/if}

<br/>
<a href="monitoare">înapoi la lista de monitoare</a>
