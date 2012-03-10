{* Parameters: $text $actType $prefix $years *}
<form action="actDispatcher" method="get">
  {$text}
  <select id="{$prefix}Year" name="{$prefix}Year"/>
    <option value="">anul...</option>
    {foreach from=$years item=year}
      <option value="{$year}">{$year}</option>
    {/foreach}
  </select>
  <select id="{$prefix}Number" name="actId" disabled="disabled"/>
    <option value="">numărul...</option>
  </select>
  <input type="submit" id="{$prefix}Submit" name="{$prefix}Submit" value="Arată" disabled="disabled"/>
</form>

<script>
  $(function() {ldelim}
    $('#{$prefix}Year').change(function() {ldelim}
      actSelectYearChange('{$actType}', $(this), $('#{$prefix}Number'), $('#{$prefix}Submit'));
    {rdelim});
    $('#{$prefix}Number').change(function() {ldelim}
      actSelectNumberChange($(this), $('#{$prefix}Submit'));
    {rdelim});
  {rdelim});
</script>
