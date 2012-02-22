<h3>
  {if $author->id}
    Editează autorul '{$author->getDisplayName()}'
  {else}
    Creează un autor
  {/if}
</h3>
<form action="editare-autor" method="post">
  {if $author->id}
    <input type="hidden" name="id" value="{$author->id}"/>
  {/if}
  Instituție: <input type="text" name="institution" value="{$author->institution}"/> <span class="hint">Curtea Constituțională</span><br/>
  Funcție: <input type="text" name="position" value="{$author->position}"/> <span class="hint">Președintele Curții Constituționale</span><br/>
  Titlu: <input type="text" name="title" value="{$author->title}"/> <span class="hint">prof. univ. dr.</span><br/>
  Persoană: <input type="text" name="name" value="{$author->name}"/> <span class="hint">Ioan Vida</span><br/>
  <input type="submit" name="submitButton" value="Salvează"/>
</form>

<br/>

{if $author->id}
  <a class="delete" href="editare-autor?deleteId={$author->id}"
     onclick="return confirm('Confirmați ștergerea autorului \'{$author->getDisplayName()}\'?');">șterge</a>
  <br/><br/>
{/if}

<a href="autori">înapoi la lista de autori</a>
