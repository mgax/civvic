<h3>Decupează o imagine dintr-un monitor oficial</h3>

<form action="decupare-imagine">
  numărul: <input type="text" name="number" value="{$ci.monitorNumber}" size="4"/>
  anul: <input type="text" name="year" value="{$ci.monitorYear}" size="4"/>
  pagina: <input type="text" name="page" value="{$ci.monitorPage}" size="4"/>
  zoom: <input type="text" name="zoom" value="{$ci.zoom|default:"100"}" size="4"/>%
  <input type="submit" name="pageGrabButton" value="Previzualizează"/>

  {if $imageName}
    <div id="pdfPageWrapper">
      <img id="pdfPage" src="ajax/pdfImage?name={$imageName}" alt="imaginea unei pagini din MO"/>
    </div>
    <br/>

    <input type="hidden" name="imageName" value="{$imageName}"/>
    X start: <input type="text" id="areaX0" name="x0" value="{$ci.x0}" size="4" readonly="readonly"/>
    Y start: <input type="text" id="areaY0" name="y0" value="{$ci.y0}" size="4" readonly="readonly"/>
    lățime: <input type="text" id="areaWidth" name="width" value="{$ci.width}" size="4" readonly="readonly"/>
    înălțime: <input type="text" id="areaHeight" name="height" value="{$ci.height}" size="4" readonly="readonly"/><br/>

    nume:
    <input type="text" name="cropName" value="{$ci.name}"/>
    <input type="submit" name="cropButton" value="Salvează"/>
  {/if}
</form>

{if $imageName}
  <script>
  {literal}
    $(function() { 
      $('#pdfPage').Jcrop({
        onChange: showCoords,
        onSelect: showCoords,
        {/literal}{if $ci.width && $ci.height}
          setSelect: [ {$ci.x0}, {$ci.y0}, {$ci.width+$ci.x0}, {$ci.height+$ci.y0} ],
        {/if}{literal}
      });
    });

    function showCoords(c) {
      $('#areaX0').val(c.x);
      $('#areaY0').val(c.y);
      $('#areaWidth').val(c.w);
      $('#areaHeight').val(c.h);
    }
  {/literal}
  </script>
{/if}

<br/>
<a href="imagini">înapoi la lista de imagini</a>
