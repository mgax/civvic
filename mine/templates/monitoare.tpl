<h3>Monitoare Oficiale</h3>

{if $user && $user->admin}
  <a href="editare-monitor">adaugă un monitor</a> |
  <a href="importare-monitor">importă un monitor de pe wiki</a>
{/if}

{foreach from=$yearMap item=monitors key=year}
  <h4>{$year}</h4>
  {foreach from=$monitors item=m}
    <a href="monitor?id={$m->id}">{$m->number}</a>&nbsp;
  {/foreach}
{/foreach}
