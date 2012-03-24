{* Parameters: $act $actType=null $authors=null $monitor=null $versions $shownAv $editLinks=false *}
{assign var="editLinks" value=$editLinks|default:false}
<div class="actTitle">{$act->name}</div>
<div class="actDetails">
  <ul>
    {if $actType}<li>tipul: <b>{$actType->name}</b>{/if}
    {if $act->number}<li>numărul: <b>{$act->number} / {$act->year}</b></li>{/if}
    {if $act->issueDate}<li>data: <b>{$act->issueDate|date_format:"%e %B %Y"}</b></li>{/if}
    {if $monitor}<li>publicat în <a href="monitor?id={$monitor->id}">Monitorul Oficial {$monitor->number} / {$monitor->year}</a></li>{/if}
  </ul>

  {if $authors}
    <div class="author">
      {if count($authors) > 1}autori:<br/>{else}autor: {/if}
      {foreach from=$authors item=author}
        <b>{$author->getDisplayName()}</b><br/>
      {/foreach}
    </div>
  {/if}

  {if count($versions) > 1}
    <form action="act">
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

  {if $editLinks && $user && $user->admin}
    <span class="actEditLinks">
      editează <a href="editare-act?id={$act->id}">actul</a> |
      <a href="editare-versiune-act?id={$shownAv->id}">această versiune</a>
    </span>

    {include file=bits/monitorPdfLink.tpl monitor=$monitor}
  {/if}
</div>
