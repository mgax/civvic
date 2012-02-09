<h3>Acte</h3>

<ul>
  {foreach from=$acts item=a}
     <li><a href="editare-act?id={$a->id}">{$a->name}</a></li>
  {/foreach}
</ul>

{if $user && $user->admin}
  <a href="editare-act">adaugă un act nou</a>
{/if}
