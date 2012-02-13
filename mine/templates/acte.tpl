<h3>Acte</h3>

<ul>
  {foreach from=$acts item=a}
     <li><a href="act?id={$a->id}">{$a->number} / {$a->year}</a> {$a->name}</li>
  {/foreach}
</ul>

{if $user && $user->admin}
  <a href="editare-act">adaugă un act nou</a>
{/if}
