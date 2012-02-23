<h3>Monitorul Oficial nr. {$monitor->number} / {$monitor->year}</h3>

<ul>
  {foreach from=$acts item=a}
     <li><a href="act?id={$a->id}">{$a->getDisplayId()}</a> {$a->name}</li>
  {/foreach}
</ul>
