<h3>Bun venit la Civvic.ro</h3>

<form id="searchForm" action="actDispatcher">
  {include file=bits/actAutocomplete.tpl name="actId" autofocus=true refFieldName="actData"}
  <input type="submit" name="submitButton" value="Caută"/>
</form>

<p>Civvic.ro își propune să ofere o arhivă completă și căutabilă a legilor, decretelor, ordonanțelor de guvern, comunicatelor așa cum au apărut ele în
Monitorul Oficial începând cu data de 22 decembrie 1989. În particular, dorim să oferim toate variantele fiecărei legi și datele la care s-au făcut
modificări asupra ei.</p>

<p>Civvic.ro este un proiect în lucru. Până acum, am digitizat majoritatea Monitoarelor Oficiale din anii 1990-1996, pe care le puteți găsi la <a
href="http://wiki.civvic.ro/">wiki.civvic.ro</a>. Acum suntem în faza de împărțire a acestor monitoare în acte individuale, cu legături între ele.</p>

<h3>Nagivhează la un act</h3>

{include file=bits/actSelect.tpl text="Legea" actType="Lege" prefix="law" years=$lawYears}
{include file=bits/actSelect.tpl text="Decretul" actType="Decret" prefix="decree" years=$decreeYears}
{include file=bits/actSelect.tpl text="Decretul-lege" actType="Decret-lege" prefix="dl" years=$dlYears}
{include file=bits/actSelect.tpl text="Hotărârea guvernului" actType="Hotărâre" prefix="ord" years=$ordYears}
