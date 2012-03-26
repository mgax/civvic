{* Parameters: $name $authors *}
<textarea id="{$name}" name="{$name}" rows="{math equation="x + 2" x=$authors|@count}">
{foreach from=$authors item=a}{$a->getDisplayName()}
{/foreach}
</textarea>

<script>
  {literal}
  $(function() {
    function splitLines(s) {
      return s.trim().split(/[\n\r]+/);
    }
    function getLastLine(s) {
      return splitLines(s).pop();
    }
    $("#{/literal}{$name}{literal}")
      // don't navigate away from the field on tab when selecting an item
      .bind("keydown", function(event) {
        if (event.keyCode === $.ui.keyCode.TAB && $(this).data("autocomplete").menu.active) {
          event.preventDefault();
        }
      })
      .autocomplete({
        source: function(request, response) {
          $.getJSON("{/literal}{$wwwRoot}{literal}ajax/authorAutocomplete.php", { term: getLastLine(request.term) }, response);
        },
        search: function() {
          // custom minLength
          var term = getLastLine(this.value);
          if (term.length < 2) {
            return false;
          }
        },
        focus: function() {
          // prevent value inserted on focus
          return false;
        },
        select: function(event, ui) {
          var terms = splitLines(this.value);
          terms.pop();
          terms.push(ui.item.value);
          // add placeholder to get the newline at the end
          terms.push("");
          this.value = terms.join("\n");
          return false;
        },
      });
  });
  {/literal}
</script>
