{* This is temporary. Once we get enough monitors, we'll implement a smarter selection mechanism *}
{* Parameters: $name $monitors $selected $emptyOption=true *}
{assign var="emptyOption" value=$emptyOption|default:true}
<select name="{$name}">
  {if $emptyOption}
    <option value=""></option>
  {/if}
  {foreach from=$monitors item=monitor}
    <option value="{$monitor->id}" {if $monitor->id == $selected}selected="selected"{/if}>
      Monitorul Oficial {$monitor->number} / {$monitor->year}
    </option>
  {/foreach}
</select>
