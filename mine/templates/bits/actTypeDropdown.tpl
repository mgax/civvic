{* Parameters: $name $actTypes $selected *}
<select name="{$name}">
  {foreach from=$actTypes item=at}
    <option value="{$at->id}" {if $at->id == $selected}selected="selected"{/if}>{$at->name}</option>
  {/foreach}
</select>
