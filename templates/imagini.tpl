<h3>Imagini</h3>

<a href="decupare-imagine">decupează o imagine din monitorul oficial</a><br/><br/>

Această pagină este în construcție. Când vom avea destule imagini ca să ne dăm seama cum vrem să le ordonăm și filtrăm, atunci vom căuta un modul mai
arătos cu aceste funcții.<br/>

{foreach from=$images item=i}
  <img class="croppedImageList" src="{$wwwRoot}img/{$croppedDir}/{$i->name}" alt="imagine decupată: {$i->name}"/><br/>
  {$i->name}
  <span class="croppedImageDetails">
    Decupată din M.O. {$i->monitorNumber} / {$i->monitorYear}, pagina {$i->monitorPage}
  </span>
  <br/>
{/foreach}