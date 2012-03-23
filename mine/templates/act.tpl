{include file=bits/actHeader.tpl act=$act monitor=$monitor authors=$authors editLinks=true}

{if $shownAv->status == $smarty.const.ACT_STATUS_REPEALED}
  <div class="repealedMention">
    Acest act a fost abrogat de {include file=bits/actLink.tpl act=$modifyingAct}.
  </div>
{/if}
{$shownAv->htmlContents}
