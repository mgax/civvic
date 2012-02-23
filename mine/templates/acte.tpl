<h3>Acte</h3>

{if $user && $user->admin}
  <a href="editare-act">adaugă un act nou</a>
{/if}

{foreach from=$actMap item=acts key=year}
  <h4>{$year}</h4>
  <ul class="actList">
    {foreach from=$acts item=a}
     <li>{include file=bits/actLink.tpl act=$a} {$a->name}</li>
    {/foreach}
  </ul>
{/foreach}
