{* Parameters: $name $id=null $actTypes $selected $emptyOption=true $autofocus=false *}
{assign var="id" value=$id|default:null}
{assign var="emptyOption" value=$emptyOption|default:true}
{assign var="autofocus" value=$autofocus|default:false}
<select name="{$name}" {if $id}id="{$id}"{/if} {if $autofocus}autofocus="autofocus"{/if}>
  {if $emptyOption}
    <option value=""></option>
  {/if}
  {foreach from=$actTypes item=at}
    <option value="{$at->id}" {if $at->id == $selected}selected="selected"{/if}>{$at->name}</option>
  {/foreach}
</select>
