{* Parameters: $monitor *}
{if $monitor && $moArchiveUrl}
  &nbsp;
  <span class="actPdfLink">
    <a href="{$moArchiveUrl}{$monitor->year}/{$monitor->getPdfNumber()}.pdf">descarcă PDF</a>
  </span>
{/if}
