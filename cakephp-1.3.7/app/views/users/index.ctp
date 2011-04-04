<?php echo $this->Form->create('User', array('action' => 'index')) ?>
  <table id="userList">
    <tr>
      <th>OpenId</th>
      <th>Admin</th>
    </tr>
  
    <?php foreach ($users as $user): ?>
    <tr>
      <td><?=$user['User']['openid']?></td>
      <td><?php echo $this->Form->input("admin_{$user['User']['id']}", array('type' => 'checkbox', 'label' => '',
        'checked' => $user['User']['admin'], 'disabled' => ($myId == $user['User']['id']) ? 'disabled' : '')); ?></td>
    </tr>
    <?php endforeach; ?>
  </table>
  <?php echo $this->Form->submit('Salvează') ?></li>
<?php echo $this->Form->end() ?>
