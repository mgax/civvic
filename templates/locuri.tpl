<h3>Locuri</h3>

<ul>
  {foreach from=$places item=p}
     <li><a href="editare-loc?id={$p->id}">{$p->name}</a></li>
  {/foreach}
</ul>

<a href="editare-loc">adaugă un loc nou</a>

