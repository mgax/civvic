<h3>Acte fără număr</h3>

Politica Civvic.ro este ca fiecare act să aibă un număr unic. Pentru actele care nu au număr în Monitoarele Oficiale (unele comunicate, rectificări
etc.), vom folosi numele secvențiale FN1, FN2, ...

<ul>
  {foreach from=$acts item=a}
    <li>{include file=bits/actLink.tpl act=$a} {$a->name}</li>
  {/foreach}
</ul>
