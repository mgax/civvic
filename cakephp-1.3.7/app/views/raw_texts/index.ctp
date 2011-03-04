<?php echo $this->Form->create('RawText', array('action' => 'index', 'inputDefaults' => array('div' => false))) ?>
  <ul id="rawTextFilter">
    <li>
      <?php echo $this->Form->label('year', 'Anul:') ?>
      <?php echo $this->Form->year('year', 1989, 2011, null, array('orderYear' => 'asc', 'empty' => 'oricare')) ?>
    </li>
    <li><?php echo $this->Form->input('Progress', array('label' => 'Stadiul:', 'empty' => 'oricare')) ?></li>
    <?php if ($this->Session->read('user')): ?>
      <li>
        <?php echo $this->Form->checkbox('mine') ?>
        <?php echo $this->Form->label('mine', 'Deținute de mine', array('class' => 'spaced')) ?>
      </li>
    <?php endif ?>
    <li>
      <?php echo $this->Form->label('limit', 'Afișează:') ?>
      <?php echo $this->Form->select('limit', array('10' => '10', '20' => '20', '50' => '50', '100' => '100'), $limit) ?>
    </li>
    <li><?php echo $this->Form->submit('Filtrează', array('div' => false)) ?></li>
  </ul>
<?php echo $this->Form->end() ?>

<table id="moList">
  <tr>
    <th>Număr</th>
    <th>Pagini</th>
    <th>Stadiu</th>
    <th>Deținător</th>
  </tr>

  <?php foreach ($rawTexts as $rawText): ?>
  <tr>
    <td>
      <?php echo $html->link($rawText['RawText']['issue'] . '/' . $rawText['RawText']['year'], array('controller' => 'raw_texts', 'action' => 'view', $rawText['RawText']['id'])); ?>
    </td>
    <td><?php echo $rawText['PdfDocument']['page_count']; ?></td>
    <td><?php echo RawText::progresses($rawText['RawText']['progress']) ?></td>
    <td><?php echo User::displayValue($rawText['User']['openid']); ?></td>
  </tr>
  <?php endforeach; ?>
</table>
