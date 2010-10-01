<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link href="boxingCoach.css" rel="stylesheet" type="text/css"/>
    <title>Boxing Coach for Tesseract</title>
  </head>

  <body onload="document.getElementById('textBox').select(); document.getElementById('textBox').focus();">
    {if $smarty.session.flashMessage}
      <div id="flashMessage">{$smarty.session.flashMessage}</div>
      {php}unset($_SESSION['flashMessage']);{/php}
    {/if}

    <h3>Page {$pageId}, symbol {$boxId} of {$numBoxes}</h3>
    <img id="crop" src="{$image}" alt="{$box->text|escape}"/><br/>

    <div id="leftCol">
      <form action="boxingCoach.php" method="get">
        <input type="hidden" name="pageId" value="{$pageId}"/>
        <input type="hidden" name="boxId" value="{$boxId}"/>
        Text: <input type="text" id="textBox" name="text" value="{$box->text|escape}" size="5" autocomplete="off"/><br/>
        <input type="submit" name="submitButton" value="Okay" title="Accept the box size and text"/>
        <input type="submit" name="prevButton" value="<<" title="Go to the previous symbol"/>
        <input type="submit" name="mergeButton" value="Merge w/ next" title="Merge this symbol and the next in a box encompassing both"/><br/>
        <input type="submit" name="splitButton" value="Split" title="Split this symbol in two"/>
        <input type="submit" name="deleteButton" value="Delete" title="Delete this symbol"/>
      </form>
    </div>

    <div id="rightCol">
      <form action="boxingCoach.php" method="get">
        <input type="hidden" name="pageId" value="{$pageId}"/>
        <input type="hidden" name="boxId" value="{$boxId}"/>
        <table id="crosshair">
          <tr>
            <td></td>
            <td>
              <input type="submit" name="growTop" value="↑" title="Grow top"/><br/>
              <input type="submit" name="shrinkTop" value="↓" title="Shrink top"/>
            </td>
            <td></td>
          </tr>
          <tr>
            <td>
              <input type="submit" name="growLeft" value="←" title="Grow left"/>
              <input type="submit" name="shrinkLeft" value="→" title="Shrink left"/>
            </td>
            <td>
              <input id="crosshairValue" type="text" name="pixels" value="1" size="2"/>
            </td>
            <td>
              <input type="submit" name="shrinkRight" value="←" title="Shrink right"/>
              <input type="submit" name="growRight" value="→" title="Grow right"/>
            </td>
          </tr>
          <tr>
            <td></td>
            <td>
              <input type="submit" name="shrinkBottom" value="↑" title="Shrink bottom"/><br/>
              <input type="submit" name="growBottom" value="↓" title="Grow bottom"/>
            </td>
            <td></td>
          </tr>
        </table>
      </form>
    </div>

    <div class="clearer"></div>

    <form id="actions" action="boxingCoach.php" method="get">
      Skip to page <input type="text" name="pageId" value="{$pageId}" size="3"/>, symbol <input type="text" name="boxId" value="{$boxId}" size="5"/>
      <input type="submit" name="skipButton" value="Skip"/>
    </form>
  </body>
</html>
