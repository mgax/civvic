<h3>Acte inexistente</h3>

{foreach from=$refs item=ref key=i}
  {assign var=actTypeId value=$ref->actTypeId}
  {$actTypes[$actTypeId]->artName} {$ref->number} / {$ref->year},
  menționat în {include file=bits/actLink.tpl act=$acts[$i]}
  <br/>
{/foreach}
