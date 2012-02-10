{* This is temporary. Once we get enough acts, we'll implement a smarter selection mechanism *}
{* Parameters: $name $acts $actTypes $selected $emptyOption=true *}
{assign var="emptyOption" value=$emptyOption|default:true}
<select name="{$name}">
  {if $emptyOption}
    <option value=""></option>
  {/if}
  {foreach from=$acts item=act}
    {assign var=actTypeId value=$act->actTypeId}
    <option value="{$act->id}" {if $act->id == $selected}selected="selected"{/if}>{$actTypes[$actTypeId]->name} {$act->number} / {$act->year}</option>
  {/foreach}
</select>
