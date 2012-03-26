{* Parameters: $name $actStatuses $selected $emptyOption=true *}
{assign var="emptyOption" value=$emptyOption|default:true}
<select name="{$name}">
  {if $emptyOption}
    <option value=""></option>
  {/if}
  {foreach from=$actStatuses item=as key=k}
    <option value="{$k}" {if $k == $selected}selected="selected"{/if}>{$as}</option>
  {/foreach}
</select>
