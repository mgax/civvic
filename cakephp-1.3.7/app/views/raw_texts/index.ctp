<?php echo $this->Html->script(array('jquery', 'jquery.tablesorter.min', 'jquery.tablesorter.pager')); ?>
<?php echo $this->Html->css('jquery.tablesorter/style.css', null, array('inline' => false)); ?>
<?php echo $this->Form->create('RawText', array('action' => 'index', 'inputDefaults' => array('div' => false))) ?>
  <ul id="rawTextFilter">
    <li>
      <?php echo $this->Form->label('year', 'Anul:'); ?>
      <?php echo $this->Form->year('year', 1989, 2011, null, array('orderYear' => 'asc', 'empty' => 'oricare')); ?>
    </li>
    <li><?php echo $this->Form->input('Progress', array('label' => 'Stadiul:', 'empty' => 'oricare')); ?></li>
    <li><?php echo $this->Form->input('Difficulty', array('label' => 'Dificultatea:', 'empty' => 'oricare')); ?></li>
    <?php if ($this->Session->read('user')): ?>
      <li>
        <?php echo $this->Form->checkbox('mine') ?>
        <?php echo $this->Form->label('mine', 'Deținute de mine', array('class' => 'spaced')) ?>
      </li>
    <?php endif ?>
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
        <td><?php echo User::displayValue($rawText['User']['openid']); ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<div id="pager" class="pager">
  <form>
    <?php echo $html->image('silk/control_start_blue.png', array('class' => 'first')) ?>
    <?php echo $html->image('silk/control_rewind_blue.png', array('class' => 'prev')) ?>
    <input type="text" class="pagedisplay"/>
    <?php echo $html->image('silk/control_fastforward_blue.png', array('class' => 'next')) ?>
    <?php echo $html->image('silk/control_end_blue.png', array('class' => 'last')) ?>
    <select class="pagesize">
      <option value="10">10</option>
      <option selected="selected" value="20">20</option>
      <option value="50">50</option>
      <option value="100">100</option>
    </select>
  </form>
</div>

<script type="text/javascript">
  $(document).ready(function() { 
    $("#moList").tablesorter().tablesorterPager({container: $("#pager"), size: 20});
  });
</script>
