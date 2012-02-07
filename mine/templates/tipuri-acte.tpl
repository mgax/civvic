<h3>Tipuri de acte</h3>

<ul>
  {foreach from=$actTypes item=at}
     <li><a href="tip-act?id={$at->id}">{$at->name}</a></li>
  {/foreach}
</ul>

<a href="tip-act">adaugă un nou tip de act</a>

