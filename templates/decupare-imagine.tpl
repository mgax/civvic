<h3>Decupează o imagine dintr-un monitor oficial</h3>

<form action="decupare-imagine">
  numărul: <input type="text" name="number" value="{$number}" size="4"/>
  anul: <input type="text" name="year" value="{$year}" size="4"/>
  pagina: <input type="text" name="page" value="{$page}" size="4"/>
  <input type="submit" name="pageGrabButton" value="Previzualizează"/>
</form>

{if $imageName}
  <h3>Selectează o regiune din imagine</h3>

  <div id="pdfPageWrapper">
    <img id="pdfPage" src="ajax/pdfImage?name={$imageName}" alt="imaginea unei pagini din MO"/>
  </div>

  <script>
  {literal}
    $(function() { 
      $('#pdfPage').Jcrop();
    });
  {/literal}
  </script>
{/if}