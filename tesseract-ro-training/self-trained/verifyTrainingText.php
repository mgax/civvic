#!/usr/bin/php
<?
mb_internal_encoding('UTF-8');

$FILE = 'combined.box';
$TEXT = str_replace("\n", "", '
Aaăâbc!Ădefgh#Âiîjkl$Bmnopq%Crsștț&Duvwxy\'Ezaăâb(Fcdefg)Ghiîjk*Hlmnop+Iqrsșt,Îțuvwx-Jyzaăâ.Kbcdef/Lghiîj:Mklmno;Npqrsș
<Otțuvw=Pxyzaă>Qâbcde?Rfghiî[Sjklmn]Șopqrs^Tștțuv{Țwxyza}Uăâbcd„Vefghi”Wîjklm—Xnopqr!Ysștțu#Zvwxyz$Aaăâbc%Ădefgh&Âiîjkl\'Bmnopq(
Crsștț)Duvwxy*Ezaăâb+Fcdefg,Ghiîjk-Hlmnop.Iqrsșt/Îțuvwx:Jyzaăâ;Kbcdef<LghiîjMklmno>Npqrsș?Otțuvw[Pxyzaă]Qâbcde^Rfghiî{Sjklmn}Șo
pqrs„Tștțuv”Țwxyza—Uăâbcd!Vefghi#Wîjklm$Xnopqr%Ysștțu&Zvwxyz\'Aaăâbc(Ădefgh)Âiîjkl*Bmnopq+Crsștț,Duvwxy-Ezaăâb.Fcdefg/Ghiîjk:Hlm
nop;Iqrsșt<ÎțuvwxJyzaăâ>Kbcdef?Lghiîj[Mklmno]Npqrsș^Otțuvw{Pxyzaă}Qâbcde„Rfghiî”Sjklmn—Șopqrs!Tștțuv#Țwxyza$Uăâbcd%Vefghi&Wîjkl
m\'Xnopqr(Ysștțu)Zvwxyz*Aaăâbc+Ădefgh,Âiîjkl-Bmnopq.Crsștț/Duvwxy:Ezaăâb;Fcdefg<Ghiîjk=Hlmnop>Iqrsșt?Îțuvwx[Jyzaăâ]Kbcdef^Lghiîj
{Mklmno}Npqrsș„Otțuvw”Pxyzaă—Qâbcde!Rfghiî#Sjklmn$Șopqrs%Tștțuv&Țwxyza\'Uăâbcd(Vefghi)Wîjklm*Xnopqr+Ysștțu,Zvwxyz-Aaăâbc.Ădefgh/
Âiîjkl:Bmnopq;Crsștț<Duvwxy=Ezaăâb>Fcdefg?Ghiîjk[Hlmnop]Iqrsșt^Îțuvwx{Jyzaăâ}Kbcdef„Lghiîj”Mklmno—Npqrsș!Otțuvw#Pxyzaă$Qâbcde%Rf
ghiî&Sjklmn\'Șopqrs(Tștțuv)Țwxyza*Uăâbcd+Vefghi,Wîjklm-Xnopqr.Ysștțu/Zvwxyz:96368,5864154,42143337,67837681,38534121,15275303,31
879889,22886434,43295300,1930675,55315391,5057454,75698088,38265676,59274035,51658294,55730161,0135849,99120219,31719137,41
Aaăâbc!Ădefgh#Âiîjkl$Bmnopq%Crsștț&Duvwxy\'Ezaăâb(Fcdefg)Ghiîjk*Hlmnop+Iqrsșt,Îțuvwx-Jyzaăâ.Kbcdef/Lghiîj:Mklmno;Npqrsș
<Otțuvw=Pxyzaă>Qâbcde?Rfghiî[Sjklmn]Șopqrs^Tștțuv{Țwxyza}Uăâbcd„Vefghi”Wîjklm—Xnopqr!Ysștțu#Zvwxyz$Aaăâbc%Ădefgh&Âiîjkl\'Bmnopq(
Crsștț)Duvwxy*Ezaăâb+Fcdefg,Ghiîjk-Hlmnop.Iqrsșt/Îțuvwx:Jyzaăâ;Kbcdef<Lghiîj=Mklmno>Npqrsș?Otțuvw[Pxyzaă]Qâbcde^Rfghiî{Sjklmn}Șo
pqrs„Tștțuv”Țwxyza—Uăâbcd!Vefghi#Wîjklm$Xnopqr%Ysștțu&ZvwxyzAaăâbc(Ădefgh)Âiîjkl*Bmnopq+Crsștț,Duvwxy-Ezaăâb.Fcdefg/Ghiîjk:Hlm
nop;Iqrsșt<Îțuvwx=Jyzaăâ>Kbcdef?Lghiîj[Mklmno]Npqrsș^Otțuvw{Pxyzaă}Qâbcde„Rfghiî”Sjklmn—Șopqrs!Tștțuv#Țwxyza$Uăâbcd%Vefghi&Wîjkl
m\'Xnopqr(Ysștțu)Zvwxyz*Aaăâbc+Ădefgh,Âiîjkl-Bmnopq.Crsștț/Duvwxy:Ezaăâb;Fcdefg<Ghiîjk=Hlmnop>Iqrsșt?Îțuvwx[Jyzaăâ]Kbcdef^Lghiîj
{Mklmno}Npqrsș„Otțuvw”Pxyzaă—Qâbcde!Rfghiî#Sjklmn$Șopqrs%Tștțuv&Țwxyza\'Uăâbcd(Vefghi)Wîjklm*Xnopqr+Ysștțu,Zvwxyz-Aaăâbc.Ădefgh/
Âiîjkl:Bmnopq;Crsștț<Duvwxy=Ezaăâb>Fcdefg?Ghiîjk[Hlmnop]Iqrsșt^Îțuvwx{Jyzaăâ}Kbcdef„Lghiîj”Mklmno—Npqrsș!Otțuvw#Pxyzaă$Qâbcde%Rf
ghiî&Sjklmn\'Șopqrs(Tștțuv)Țwxyza*Uăâbcd+Vefghi,Wîjklm-Xnopqr.Ysștțu/Zvwxyz:96368,5864154,42143337,67837681,38534121,15275303,31
879889,22886434,43295300,1930675,55315391,5057454,75698088,38265676,59274035,51658294,55730161,0135849,99120219,31719137,41
Aaăâbc!Ădefgh#Âiîjkl$Bmnopq%Crsștț&Duvwxy\'Ezaăâb(Fcdefg)Ghiîjk*Hlmnop+Iqrsșt,Îțuvwx-Jyzaăâ.Kbcdef/Lghiîj:Mklmno;Npqrsș
<Otțuvw=Pxyzaă>Qâbcde?Rfghiî[Sjklmn]Șopqrs^Tștțuv{Țwxyza}Uăâbcd„Vefghi”Wîjklm—Xnopqr!Ysștțu#Zvwxyz$Aaăâbc%Ădefgh&Âiîjkl\'Bmnopq(
Crsștț)Duvwxy*Ezaăâb+Fcdefg,Ghiîjk-Hlmnop.Iqrsșt/Îțuvwx:Jyzaăâ;Kbcdef<Lghiîj=Mklmno>Npqrsș?Otțuvw[Pxyzaă]Qâbcde^Rfghiî{Sjklmn}Șo
pqrs„Tștțuv”Țwxyza—Uăâbcd!Vefghi#Wîjklm$Xnopqr%Ysștțu&Zvwxyz\'Aaăâbc(Ădefgh)Âiîjkl*Bmnopq+Crsștț,Duvwxy-Ezaăâb.Fcdefg/Ghiîjk:Hlm
nop;Iqrsșt<Îțuvwx=Jyzaăâ>Kbcdef?Lghiîj[Mklmno]Npqrsș^Otțuvw{Pxyzaă}Qâbcde„Rfghiî”Sjklmn—Șopqrs!Tștțuv#Țwxyza$Uăâbcd%Vefghi&Wîjkl
m\'Xnopqr(Ysștțu)Zvwxyz*Aaăâbc+Ădefgh,Âiîjkl-Bmnopq.Crsștț/Duvwxy:Ezaăâb;Fcdefg<Ghiîjk=Hlmnop>Iqrsșt?Îțuvwx[Jyzaăâ]Kbcdef^Lghiîj
{Mklmno}Npqrsș„Otțuvw”Pxyzaă—Qâbcde!Rfghiî#Sjklmn$Șopqrs%Tștțuv&Țwxyza\'Uăâbcd(Vefghi)Wîjklm*Xnopqr+Ysștțu,Zvwxyz-Aaăâbc.Ădefgh/
Âiîjkl:Bmnopq;Crsștț<Duvwxy=Ezaăâb>Fcdefg?Ghiîjk[Hlmnop]Iqrsșt^Îțuvwx{Jyzaăâ}Kbcdef„Lghiîj”Mklmno—Npqrsș!Otțuvw#Pxyzaă$Qâbcde%Rf
ghiî&Sjklmn\'Șopqrs(Tștțuv)Țwxyza*Uăâbcd+Vefghi,Wîjklm-Xnopqr.Ysștțu/Zvwxyz:96368,5864154,42143337,67837681,38534121,15275303,31
879889,22886434,43295300,1930675,55315391,5057454,75698088,38265676,59274035,51658294,55730161,0135849,99120219,31719137,41
Aaăâbc!Ădefgh#Âiîjkl$Bmnopq%Crsștț&Duvwxy\'Ezaăâb(Fcdefg)Ghiîjk*Hlmnop+Iqrsșt,Îțuvwx-Jyzaăâ.Kbcdef/Lghiîj:Mklmno;Npqrsș
<Otțuvw=Pxyzaă>Qâbcde?Rfghiî[Sjklmn]Șopqrs^Tștțuv{Țwxyza}Uăâbcd„Vefghi”Wîjklm—Xnopqr!Ysștțu#Zvwxyz$Aaăâbc%Ădefgh&Âiîjkl\'Bmnopq(
Crsștț)Duvwxy*Ezaăâb+Fcdefg,Ghiîjk-Hlmnop.Iqrsșt/Îțuvwx:Jyzaăâ;Kbcdef<Lghiîj=Mklmno>Npqrsș?Otțuvw[Pxyzaă]Qâbcde^Rfghiî{Sjklmn}Șo
pqrs„Tștțuv”Țwxyza—Uăâbcd!Vefghi#Wîjklm$Xnopqr%Ysștțu&Zvwxyz\'Aaăâbc(Ădefgh)Âiîjkl*Bmnopq+Crsștț,Duvwxy-Ezaăâb.Fcdefg/Ghiîjk:Hlm
nop;Iqrsșt<Îțuvwx=Jyzaăâ>Kbcdef?Lghiîj[Mklmno]Npqrsș^Otțuvw{Pxyzaă}Qâbcde„Rfghiî”Sjklmn—Șopqrs!Tștțuv#Țwxyza$Uăâbcd%Vefghi&Wîjkl
m\'Xnopqr(Ysștțu)Zvwxyz*Aaăâbc+Ădefgh,Âiîjkl-Bmnopq.Crsștț/Duvwxy:Ezaăâb;Fcdefg<Ghiîjk=Hlmnop>Iqrsșt?Îțuvwx[Jyzaăâ]Kbcdef^Lghiîj
{Mklmno}Npqrsș„Otțuvw”Pxyzaă—Qâbcde!Rfghiî#Sjklmn$Șopqrs%Tștțuv&Țwxyza\'Uăâbcd(Vefghi)Wîjklm*Xnopqr+Ysștțu,Zvwxyz-Aaăâbc.Ădefgh/
Âiîjkl:Bmnopq;Crsștț<Duvwxy=Ezaăâb>Fcdefg?Ghiîjk[Hlmnop]Iqrsșt^Îțuvwx{Jyzaăâ}Kbcdef„Lghiîj”Mklmno—Npqrsș!Otțuvw#Pxyzaă$Qâbcde%Rf
ghiî&Sjklmn\'Șopqrs(Tștțuv)Țwxyza*Uăâbcd+Vefghi,Wîjklm-Xnopqr.Ysștțu/Zvwxyz:96368,5864154,42143337,67837681,38534121,15275303,31
879889,22886434,43295300,1930675,55315391,5057454,75698088,38265676,59274035,51658294,55730161,0135849,99120219,31719137,41'
);
$MISSING_CHARS = array(array(0, 321));

// Sort the file; for some reason page 2 is jumbled up.
// $lines = file($FILE);
// $m = array();
// foreach ($lines as $line) {
//   $m[] = preg_split('/\s+/', $line);
// }

// for ($i = 0; $i < count($m) - 1; $i++) {
//   $k = $i;
//   for ($j = $i + 1; $j < count($m); $j++) {
//     if (boxCmp($m[$j], $m[$k])) {
//       $k = $j;
//     }
//   }
//   $tmp = $m[$i]; $m[$i] = $m[$k]; $m[$k] = $tmp;
// }

// $f = fopen($FILE, 'w');
// foreach ($m as $v) {
//   fwrite($f, "{$v[0]} {$v[1]} {$v[2]} {$v[3]} {$v[4]} {$v[5]}\n");
// }
// fclose($f);

// Now the actual verification
$lines = file($FILE);
$numErrors = 0;

foreach ($lines as $i => $line) {
  $words = preg_split('/\s+/', $line);
  $char = $words[0];
  $expectedChar = mb_substr($TEXT, $i, 1);
  if ($char !== $expectedChar) {
    print "Line $i: expected «{$expectedChar}», got «{$char}»\n";
    if (++$numErrors == 10) {
      print "Bailing due to too many errors.\n";
      exit(1);
    }
  }
}

/*************************************************************************/

// Returns true if $a needs to be listed before $b
function boxCmp(&$a, &$b) {
  // Compare pages
  if ($a[5] > $b[5]) return false;
  if ($b[5] > $a[5]) return true;

  // Compare y coordinate
  if ($a[2] > $b[4] + 10) return true;
  if ($b[2] > $a[4] + 10) return false;

  // Same row -- compare x coordinate
  return ($a[1] < $b[1]);
}

?>
