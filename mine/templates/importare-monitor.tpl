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
  <h3>Monitorul Oficial nr. {$monitor->number} / {$monitor->year}</h3>
  <ul class="actDetails">
    <li>publicat la {$monitor->issueDate|date_format:"%e %B %Y"}</li>
  </ul>

  {foreach from=$acts item=act key=i}
    <h3>{$act->name}</h3>
    <ul class="actDetails">
      {if $act->number}<li>numărul: {$act->number}</li>{/if}
      {if $act->year}<li>anul: {$act->year}</li>{/if}
      {if $act->issueDate}<li>data: {$act->issueDate|date_format:"%e %B %Y"}</li>{/if}
    </ul>

    {$actVersions.$i->htmlContents}
    {if count($authorMatrix.$i)}
      <div class="author">
        {if count($authorMatrix.$i) > 1}Autori:{else}Autor:{/if}<br/>
        {foreach from=$authorMatrix.$i item=author}
          {$author->getDisplayName()}<br/>
        {/foreach}
      </div>
    {/if}
    <br/>
  {/foreach}
{/if}
