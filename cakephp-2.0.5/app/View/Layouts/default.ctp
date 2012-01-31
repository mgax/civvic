<?php $user = $this->Session->read('user'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" /> 
    <title>Administrare civvic.ro</title>
    <?php echo $html->css('main'); ?>
    <?php echo $scripts_for_layout; ?>
  </head>
  <body>
    <div id="header">
      <div id="logo">
        <?php echo $html->link('admin.civvic.ro', '/') ?>
      </div>
      <div id="identity">
        <?php echo $html->image('openid-16x16.png', array('alt' => 'OpenId logo')) ?>
        <?php if ($user) {?>
          <?php print User::displayValue($user['User']); ?>
          <?php echo $html->link('deconectare', '/users/logout') ?>
        <?php } else { ?>
          <?php echo $html->link('conectare', '/users/login') ?>
        <?php } ?>
      </div>
      <div class="clearer"></div>
    </div>

    <div id="menubar">
      <ul>
        <li><?php echo $html->link('Lista Monitoarelor Oficiale', '/raw_texts/index') ?></li>
        <?php if ($user['User']['admin']) { ?>
          <li><?php echo $html->link('utilizatori', '/users'); ?></li>
        <?php } ?>
        <li><?php echo $html->link('ajutor', '/pages/help') ?></li>
      </ul>
    </div>

    <?php echo $this->Session->flash() ?>
    <div id="mainContainer">
      <?php echo $content_for_layout ?>
    </div>
  </body>
</html>
