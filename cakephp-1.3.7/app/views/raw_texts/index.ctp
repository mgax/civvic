<table>
  <tr>
    <th>Id</th>
    <th>Număr</th>
    <th>Numărul de pagini</th>
    <th>Stadiu</th>
    <th>Creat</th>
  </tr>

  <?php foreach ($rawTexts as $rawText): ?>
  <tr>
    <td><?php echo $rawText['RawText']['id']; ?></td>
    <td>
      <?php echo $html->link($rawText['RawText']['issue'] . '/' . $rawText['RawText']['year'], array('controller' => 'raw_texts', 'action' => 'view', $rawText['RawText']['id'])); ?>
    </td>
    <td><?php echo $rawText['PdfDocument']['page_count']; ?></td>
    <td><?php echo RawText::progresses($rawText['RawText']['progress']) ?></td>
    <td><?php echo $rawText['RawText']['created']; ?></td>
  </tr>
  <?php endforeach; ?>
</table>
