{* Parameters: $name $actTypes $selected $emptyOption=true $autofocus = false *}
{assign var="emptyOption" value=$emptyOption|default:true}
{assign var="autofocus" value=$autofocus|default:false}
<input type="hidden" id="{$name}_hidden" name="{$name}" value="{$selected->id}"/>
<input type="text" id="{$name}_visible" name="{$name}_visible" value="{if $selected}{$selected->getAutocompleteId()}{/if}"
  {if $autofocus}autofocus="autofocus"{/if} size="80"/>

<script type="text/javascript">
  {literal}
  $("#{/literal}{$name}{literal}_visible").autocomplete({
    source: function(request, response) {
      $.ajax({
        url: "{/literal}{$wwwRoot}{literal}ajax/actAutocomplete.php",
        dataType: 'json',
        data: { term: request.term },
        success: function(data) { response(data); },
      })
    },
    select: function (event, ui) {
      $('#{/literal}{$name}{literal}_hidden').val(ui.item.id);
    },
    change: function (event, ui) {
      $('#{/literal}{$name}{literal}_hidden').val(ui.item ? ui.item.id : '');
    },
    minLength: 2,
  });
  {/literal}
</script>
