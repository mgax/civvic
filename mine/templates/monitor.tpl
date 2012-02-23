<h3>Monitorul Oficial nr. {$monitor->number} / {$monitor->year}</h3>

<ul>
  {foreach from=$acts item=a}
     <li>{include file=bits/actLink.tpl act=$a} {$a->name}</li>
  {/foreach}
</ul>
