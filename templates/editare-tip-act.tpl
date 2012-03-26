<h3>
  {if $actType->id}
    Editează tipul de act '{$actType->name}'
  {else}
    Creează un tip de act
  {/if}
</h3>
<form action="editare-tip-act" method="post">
  {if $actType->id}
    <input type="hidden" name="id" value="{$actType->id}"/>
  {/if}
  Nume: <input type="text" name="name" value="{$actType->name}"/><br/>
  Nume scurt: <input type="text" name="shortName" value="{$actType->shortName}"/><br/>
  Articulat: <input type="text" name="artName" value="{$actType->artName}"/><br/>
  Genitiv: <input type="text" name="genArtName" value="{$actType->genArtName}"/><br/>
  <input type="submit" name="submitButton" value="Salvează"/>
</form>

<br/>

{if $actType->id}
  <a class="delete" href="editare-tip-act?deleteId={$actType->id}"
     onclick="return confirm('Confirmați ștergerea tipului de act \'{$actType->name}\'?');">șterge</a>
  <br/><br/>
{/if}

<a href="tipuri-acte">înapoi la lista de tipuri</a>
