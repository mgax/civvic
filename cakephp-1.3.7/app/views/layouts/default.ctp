<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" /> 
    <title>Civvic.ro</title>
    <?php echo $this->Html->css('main'); ?>
  </head>
  <body>
    <div id="header">
      <div id="logo">
        civvic.ro
      </div>
      <div id="identity">
        <?php echo $this->Html->image('openid-16x16.png', array('alt' => 'OpenId logo')) ?>
        <?php if ($this->Session->read("openId")) {?>
          Utilizator: <?php print $this->Session->read("openId"); ?>
          <a href="/users/logout">deconectare</a>
        <?php } else { ?>
          <a href="/users/login">conectare</a>
        <?php } ?>
      </div>
      <div class="clearer"></div>
    </div>
    <div id="mainContainer">
      <?php echo $content_for_layout ?>
    </div>
  </body>
</html>
