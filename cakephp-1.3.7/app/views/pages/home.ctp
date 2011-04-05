<?php $user = $this->Session->read('user'); ?>

<p>Acest site este folosit în conjuncție cu <a href="http://civvic.ro">civvic.ro</a>. Momentan, el oferă o interfață rudimentară prin care voluntarii se pot coordona pentru
digitizarea și publicarea arhivei Monitorului Oficial.</p>

<?php if ($user['User']['admin']) { ?>
  <?php echo $html->link('Raport lunar', '/raw_texts/report'); ?>

<?php } ?>