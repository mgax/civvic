<?php echo $this->Form->create('RawText', array('action' => 'index', 'inputDefaults' => array('div' => false))) ?>
  <ul class="rawTextFilter">
    <li>
      <?php echo $this->Form->label('year', 'Anul:'); ?>
      <?php echo $this->Form->year('year', 1989, 2011, null, array('orderYear' => 'asc', 'empty' => 'oricare')); ?>
    </li>
    <li><?php echo $this->Form->input('Progress', array('label' => 'Stadiul:', 'empty' => 'oricare')); ?></li>
    <li><?php echo $this->Form->input('Difficulty', array('label' => 'Dificultatea:', 'empty' => 'oricare')); ?></li>
  </ul>
  <ul class="rawTextFilter">
    <li>Deținute de <?php echo $this->Form->radio('ownerChoices', $ownerChoices, array('value' => $selectedOwner, 'legend' => false, 'separator' => '&nbsp;&nbsp;')); ?></li>
    <li><?php echo $this->Form->input('owner', array('label' => '')); ?></li>
    <li><?php echo $this->Form->submit('Filtrează', array('div' => false)) ?></li>
  </ul>
<?php echo $this->Form->end() ?>

<table id="moList" class="tablesorter">
  <thead>
    <tr>
      <th>Număr</th>
      <th>Pagini</th>
      <th>Stadiu</th>
      <th>Dificultate</th>
      <th>Deținător</th>
    </tr>
  </thead>

  <tbody>
    <?php foreach ($rawTexts as $rawText): ?>
      <tr>
        <td>
          <?php echo $html->link($rawText['RawText']['issue'] . '/' . $rawText['RawText']['year'], array('controller' => 'raw_texts', 'action' => 'view', $rawText['RawText']['id'])); ?>
        </td>
        <td><?php echo $rawText['PdfDocument']['page_count']; ?></td>
        <td><?php echo RawText::progresses($rawText['RawText']['progress']) ?></td>
        <td><?php echo RawText::difficulties($rawText['RawText']['difficulty']) ?></td>
        <td><?php echo User::displayValue($rawText['User']); ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php echo $this->element('pager_div'); ?>

<script type="text/javascript">
  $(document).ready(function() { 
    $("#moList").tablesorter().tablesorterPager({container: $("#pager"), size: 20});
  });
</script>
