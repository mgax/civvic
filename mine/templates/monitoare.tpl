<h3>Monitoare Oficiale</h3>

<ul>
  {foreach from=$monitors item=m}
     <li><a href="editare-monitor?id={$m->id}">{$m->number} / {$m->year}</a></li>
  {/foreach}
</ul>

{if $user && $user->admin}
  <a href="editare-monitor">adaugă un monitor nou</a>
{/if}
