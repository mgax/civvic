<h3>Monitorul Oficial nr. {$monitor->number} / {$monitor->year}</h3>
<ul class="actDetails">
  <li>publicat la {$monitor->issueDate|date_format:"%e %B %Y"}</li>
</ul>

<ul class="actList">
  {foreach from=$acts item=a}
     <li>{include file=bits/actLink.tpl act=$a} {$a->name}</li>
  {/foreach}
</ul>
