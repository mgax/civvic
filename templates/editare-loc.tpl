<h3>
  {if $place->id}
    Editează locul '{$place->name}'
  {else}
    Creează un loc
  {/if}
</h3>
<form action="editare-loc" method="post">
  {if $place->id}
    <input type="hidden" name="id" value="{$place->id}"/>
  {/if}
  Nume: <input type="text" name="name" value="{$place->name}"/>
  <input type="submit" name="submitButton" value="Salvează"/>
</form>

<br/>

{if $place->id}
  <a class="delete" href="editare-loc?deleteId={$place->id}"
     onclick="return confirm('Confirmați ștergerea locului \'{$place->name}\'?');">șterge</a>
  <br/><br/>
{/if}

<a href="locuri">înapoi la lista de locuri</a>
