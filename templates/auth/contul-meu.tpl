<h3>Contul meu</h3>

{if !$user->nickname}
  <div class="flashMessage warningType"/>
  OpenID-ul dumneavoastră nu include o poreclă. În această situație, Civvic folosește, în ordine, numele întreg, adresa de email sau OpenID-ul
  dumneavoastră. În prezent sunteți cunoscut ca <b>{$user->getDisplayName()}</b>. Pentru a schimba aceasta, vizitați pagina furnizorului de OpenID și
  schimbați-vă detaliile, apoi reautentificați-vă.
  </div>
{/if}

<table>
  <tr>
    <td>Identitate OpenID:</td>
    <td>{$user->identity}</td>
  </tr>
  <tr>
    <td>Nume întreg:</td>
    <td>{$user->name}</td>
  </tr>
  <tr>
    <td>Adresă de e-mail:</td>
    <td>{$user->email}</td>
  </tr>
  <tr>
    <td>Poreclă:</td>
    <td>{$user->nickname}</td>
  </tr>
</table>
