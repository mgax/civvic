{* Parameters: $name $id=null $actTypes $selected $emptyOption=true *}
{assign var="id" value=$id|default:null}
{assign var="emptyOption" value=$emptyOption|default:true}
<select name="{$name}" {if $id}id="{$id}"{/if}>
  {if $emptyOption}
    <option value=""></option>
  {/if}
  {foreach from=$actTypes item=at}
    <option value="{$at->id}" {if $at->id == $selected}selected="selected"{/if}>{$at->name}</option>
  {/foreach}
</select>
