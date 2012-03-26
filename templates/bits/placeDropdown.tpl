{* Parameters: $name $places $selected $emptyOption=true *}
{assign var="emptyOption" value=$emptyOption|default:true}
<select name="{$name}">
  {if $emptyOption}
    <option value=""></option>
  {/if}
  {foreach from=$places item=place}
    <option value="{$place->id}" {if $place->id == $selected}selected="selected"{/if}>
      {$place->name}
    </option>
  {/foreach}
</select>
