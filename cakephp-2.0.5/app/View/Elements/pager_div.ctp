<?php echo $this->Html->script(array('jquery', 'jquery.tablesorter.min', 'jquery.tablesorter.pager')); ?>
<?php echo $this->Html->css('jquery.tablesorter/style.css', null, array('inline' => false)); ?>

<div id="pager" class="pager">
  <form>
    <?php echo $this->Html->image('silk/control_start_blue.png', array('class' => 'first')) ?>
    <?php echo $this->Html->image('silk/control_rewind_blue.png', array('class' => 'prev')) ?>
    <input type="text" class="pagedisplay"/>
    <?php echo $this->Html->image('silk/control_fastforward_blue.png', array('class' => 'next')) ?>
    <?php echo $this->Html->image('silk/control_end_blue.png', array('class' => 'last')) ?>
    <select class="pagesize">
      <option value="10">10</option>
      <option selected="selected" value="20">20</option>
      <option value="50">50</option>
      <option value="100">100</option>
      <option value="500">500</option>
    </select>
  </form>
</div>
