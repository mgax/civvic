<h3>Autori</h3>

<ul>
  {foreach from=$authors item=a}
     <li><a href="editare-autor?id={$a->id}">{$a->getDisplayName()}</a></li>
  {/foreach}
</ul>

<a href="editare-autor">adaugă un autor nou</a>

