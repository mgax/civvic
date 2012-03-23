<h3>Acte inexistente</h3>

<ul class="actList">
  {foreach from=$refs item=ref key=i}
    {assign var=actTypeId value=$ref->actTypeId}
    {strip}
      <li><a class="actLink undefined" href="{$wwwRoot}act-inexistent?data={$actTypeId}:{$ref->number}:{$ref->year}">
        {$actTypes[$actTypeId]->artName} {$ref->number} / {$ref->year}
      </a></li>
    {/strip}
  {/foreach}
</ul>
