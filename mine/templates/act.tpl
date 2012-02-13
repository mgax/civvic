<h3>{$actType->name|capitalize} {$act->number} / {$act->year}: {$act->name}</h3>

<form action="act" method="get">
  <input type="hidden" name="id" value="{$act->id}"/>
  Versiunea:
  <select name="version">
    {foreach from=$versions item=av}
      <option value="{$av->versionNumber}" {if $av->versionNumber == $shownAv->versionNumber}selected="selected"{/if}>
        {$av->versionNumber}{if $av->current} (curentă){/if}
      </av>
    {/foreach}
  </select>
  <input type="submit" name="submitButton" value="Arată"/>
</form>

{if $user && $user->admin}
  Editează <a href="editare-act?id={$act->id}">actul</a> |
  <a href="editare-versiune-act?id={$shownAv->id}">această versiune</a>
{/if}

{$shownAv->htmlContents}
