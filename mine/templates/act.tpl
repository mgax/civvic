<h3>{$act->name}</h3>
<ul class="actDetails">
  {if $act->number}<li>numărul: {$act->number}</li>{/if}
  {if $act->year}<li>anul: {$act->year}</li>{/if}
  {if $act->issueDate}<li>data: {$act->issueDate|date_format:"%e %B %Y"}</li>{/if}
  {if $monitor}<li>publicat în <a href="monitor?id={$monitor->id}">Monitorul Oficial {$monitor->number} / {$monitor->year}</a></li>{/if}
</ul>

{if count($versions) > 1}
  <form action="act" method="get">
    <input type="hidden" name="id" value="{$act->id}"/>
    Versiunea:
    <select name="version">
      {foreach from=$versions item=av}
        <option value="{$av->versionNumber}" {if $av->versionNumber == $shownAv->versionNumber}selected="selected"{/if}>
          {$av->versionNumber}{if $av->current} (curentă){/if}
        </option>
      {/foreach}
    </select>
    <input type="submit" name="submitButton" value="Arată"/>
  </form>
{/if}
{if $user && $user->admin}
  Editează <a href="editare-act?id={$act->id}">actul</a> |
  <a href="editare-versiune-act?id={$shownAv->id}">această versiune</a>
{/if}

{if $shownAv->status == $smarty.const.ACT_STATUS_REPEALED}
  <div class="repealedMention">
    Acest act a fost abrogat de {include file=bits/actLink.tpl act=$modifyingAct}.
  </div>
{/if}
{$shownAv->htmlContents}

{if $authors}
  <div class="author">
    {if count($authors) > 1}Autori:{else}Autor:{/if}<br/>
    {foreach from=$authors item=author}
      {$author->getDisplayName()}<br/>
    {/foreach}
  </div>
{/if}
