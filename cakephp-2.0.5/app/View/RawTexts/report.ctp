<h1>Raport al monitoarelor complete</h1>

<table id="sorted" class="tablesorter">
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
    <?php foreach ($rawTexts as $r): ?>
      <tr>
        <td>
          <?php echo $this->Html->link($r['RawText']['issue'] . '/' . $r['RawText']['year'], array('controller' => 'raw_texts', 'action' => 'view', $r['RawText']['id'])); ?>
        </td>
        <td><?php echo $r['PdfDocument']['page_count']; ?></td>
        <td><?php echo RawText::progresses($r['RawText']['progress']) ?></td>
        <td><?php echo RawText::difficulties($r['RawText']['difficulty']) ?></td>
        <td class="left"><?php echo User::displayValue($r['User']); ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php echo $this->element('pager_div'); ?>

<script type="text/javascript">
  $(document).ready(function() { 
    $("#sorted").tablesorter().tablesorterPager({container: $("#pager"), size: 20});
  });
</script>

<h1>Totaluri</h1>

<table>
  <tr>
    <th rowspan="2">Utilizator</th>
    <th colspan="3">Pagini cu dificultate...</th>
    <th rowspan="2">Acțiuni</th>
  </tr>
  <tr>
    <th><?php echo $difficulties[RawText::DIFFICULTY_LOW]; ?></th>
    <th><?php echo $difficulties[RawText::DIFFICULTY_MEDIUM]; ?></th>
    <th><?php echo $difficulties[RawText::DIFFICULTY_HIGH]; ?></th>
  </tr>
  <?php foreach ($pageCounts as $userId => $diffBreakdown): ?>
    <tr>
     <?php $username = User::displayValue($users[$userId]['User']); ?>
      <td class="left"><?=$username?></td>
      <td><?php echo $diffBreakdown[RawText::DIFFICULTY_LOW]; ?></td>
      <td><?php echo $diffBreakdown[RawText::DIFFICULTY_MEDIUM]; ?></td>
      <td><?php echo $diffBreakdown[RawText::DIFFICULTY_HIGH]; ?></td>
      <td><?php echo $this->Html->link('marchează ca verificat', "/raw_texts/mark_verified/{$userId}", null,
                                 "Verificați toate documentele pentru {$username}? Asigurați-vă că ați tipărit această pagină."); ?></td>
    </tr>
  <?php endforeach; ?>  
</table>
