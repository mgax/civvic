<h3>Importă un monitor oficial de pe wiki</h3>

<form action="importare-monitor" method="post">
  {if $monitor}
    <input type="hidden" name="previewedNumber" value="{$number}"/>
    <input type="hidden" name="previewedYear" value="{$year}"/>
  {/if}
  Numărul <input type="text" name="number" value="{$number}" size="4" autofocus="autofocus"/>&nbsp;
  Anul <input type="text" name="year" value="{$year}" size="4"/>
  <input type="submit" name="submitButton" value="Importă"/>
</form>

{if $monitor}
  <div class="actTitle">Monitorul Oficial nr. {$monitor->number} / {$monitor->year}</div>
  <div class="actDetails">
    publicat la {$monitor->issueDate|date_format:"%e %B %Y"}
  </div>

  {foreach from=$acts item=act key=i}
    {include file=bits/actHeader.tpl act=$act actType=$actTypes.$i monitor=null authors=$authorMatrix.$i}
    {$actVersions.$i->htmlContents}
  {/foreach}
{/if}
