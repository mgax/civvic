{* Parameters: $name $authors $selected $emptyOption=true *}
{assign var="emptyOption" value=$emptyOption|default:true}
<select name="{$name}">
  {if $emptyOption}
    <option value=""></option>
  {/if}
  {foreach from=$authors item=author}
    <option value="{$author->id}" {if $author->id == $selected}selected="selected"{/if}>{$author->getDisplayName()}</option>
  {/foreach}
</select>
