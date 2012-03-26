<h3>Acte cu comentarii</h3>

<ul>
  {foreach from=$acts item=a}
    <li>
     {include file=bits/actLink.tpl act=$a} {$a->name}<br/>
     <span class="deemphActName">{$a->comment}</span>
    </li>
  {/foreach}
</ul>
