<h3>Monitoare Oficiale</h3>

{foreach from=$yearMap item=monitors key=year}
  <h4>{$year}</h4>
  <ul>
    {foreach from=$monitors item=m}
      <li>
        <a href="monitor?id={$m->id}">{$m->number} / {$m->year}</a>
          {if $user && $user->admin}
            <a href="editare-monitor?id={$m->id}">editează</a>
          {/if}
      </li>
    {/foreach}
  </ul>
{/foreach}

{if $user && $user->admin}
  <a href="editare-monitor">adaugă un monitor</a> |
  <a href="importare-monitor">importă un monitor de pe wiki</a>
{/if}
