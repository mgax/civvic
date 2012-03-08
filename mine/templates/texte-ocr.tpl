<h3>Texte OCR</h3>

<table id="rawText"></table>
<div id="rawTextPager"></div>

<script>
  {literal}
  jQuery("#rawText").jqGrid({
    url: 'ajax/rawTextGrid.php',
    datatype: 'json',
    jsonReader : { repeatitems: false },
    colNames: ['id', 'număr', 'an', 'pagini', 'dificultate', 'stadiu', 'deținător'],
    colModel: [
      {name: 'id', index: 'id', hidden: true},
      {name: 'number', index: 'number', sorttype: 'int', width: 30},
      {name: 'year', index: 'year', sorttype: 'int', width: 40},
      {name: 'pageCount', index: 'pageCount', sorttype: 'int', width: 40},
      {name: 'difficulty', index: 'difficulty', sorttype: 'int', width: 40},
      {name: 'progress', index: 'progress', sorttype: 'int', width: 40},
      {name: 'openId', index:'openId'},
    ],
    height: 'auto',
    rowNum: 20,
    rowList: [10, 20, 50, 100],
    pager: '#rawTextPager',
    sortname: 'year',
    viewrecords: true,
    sortorder: 'asc',
    caption: 'Texte OCR',
    width: 884,
    hidegrid: false,
  });
  {/literal}
</script>
