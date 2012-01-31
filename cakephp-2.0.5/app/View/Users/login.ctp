<?php

if (isset($error)) {
  echo '<p class="error">'.$error.'</p>';
}
echo $this->Form->create('User', array('type' => 'post', 'action' => 'login'));
echo $this->Form->input('OpenidUrl.openid', array('label' => false));
echo $this->Form->end('Login');

?>
