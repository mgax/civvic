<div class="actTitle">Monitorul Oficial nr. {$monitor->number} / {$monitor->year}</div>
<div class="actDetails">
  publicat la {$monitor->issueDate|date_format:"%e %B %Y"}
</div>

<ul class="actList">
  {foreach from=$acts item=a}
     <li>{include file=bits/actLink.tpl act=$a} {$a->name}</li>
  {/foreach}
</ul>
