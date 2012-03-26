<h3>{$actType->artName} {$number} / {$year}</h3>

{if $acts}
  Civvic.ro nu are textul original al acestui act, dar el este menționat în următoarele acte postdecembriste:
{else}
  Civvic.ro nu are textul original al acestui act, nici nu a depistat vreo mențiune a lui în vreun act postdecembrist.
{/if}

<ul class="actList">
  {foreach from=$acts item=act key=i}
    <li>{include file=bits/actLink.tpl act=$act} versiunea {$actVersions.$i->versionNumber} din
     {$modifyingActs.$i->issueDate|date_format:"%e %B %Y"}<br/>
     <span class="deemphActName">{$act->name}</span>
    </li>
  {/foreach}
</ul>
