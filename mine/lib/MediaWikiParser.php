<?php

MediaWikiParser::init();

class MediaWikiParser {
  private static $url;
  private static $botName;
  private static $botPassword;

  static function init() {
    self::$url = Config::get('general.mediaWikiParser');
    self::$botName = Config::get('general.mediaWikiBotName');
    self::$botPassword = Config::get('general.mediaWikiBotPassword');
  }

  static function wikiToHtml($actVersion, &$references = null) {
    $text = self::insertChangeDetails($actVersion);
    $text = self::ensureReferences($text);
    $text = self::parse($text);
    $text = self::deleteEmptyTables($text);

    // Automatic links to acts
    $actTypes = Model::factory('ActType')->raw_query('select * from act_type order by length(name) desc', null)->find_many();
    foreach ($actTypes as $at) {
      $type = sprintf("(%s|%s|%s)", $at->name, $at->artName, $at->genArtName);
      // Parses "din <day> <month> <year>" or "/ <year>"
      $date = sprintf("((\\s+din\\s+(\\d{1,2})\\s+(%s)\\s+)|(\\s*\\/\\s*))(?P<year>\\d{4})", implode('|', StringUtil::$months));
      $regexp = "/(?<![->]){$type}\\s+(nr\\.?)?\\s*(?P<number>[-0-9A-Za-z.]+){$date}(?!<\\/a)/i";
      $matches = array();
      preg_match_all($regexp, $text, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
      foreach (array_reverse($matches) as $match) {
        $linkText = $match[0][0];
        $position = $match[0][1];
        $number = $match['number'][0];
        $year = $match['year'][0];

        if ($position && $text[$position - 1] == '@') {
          $text = substr($text, 0, $position - 1) . substr($text, $position);
        } else {
          $link = Act::getLink($at->id, $number, $year, $linkText);
          $text = substr($text, 0, $position) . $link . substr($text, $position + strlen($linkText));
          if ($references !== null) {
            $ref = Model::factory('Reference')->create();
            $ref->actTypeId = $at->id;
            $ref->number = $number;
            $ref->year = $year;
            $references[] = $ref;
          }
        }
      }
    }
    return $text;
  }

  static function getMediaWikiVersion() {
    $xmlString = Util::makePostRequest(self::$url, array('action' => 'expandtemplates', 'text' => "{{CURRENTVERSION}}", 'format' => 'xml'));
    $xml = simplexml_load_string($xmlString);
    return (string)$xml->expandtemplates;
  }

  static function fetchPage($pageTitle) {
    $params = array('action' => 'query', 'titles' => $pageTitle, 'prop' => 'revisions', 'rvprop' => 'content', 'format' => 'xml');
    $xmlString = Util::makePostRequest(self::$url, $params);
    $xml = simplexml_load_string($xmlString);
    $page = $xml->query->pages->page[0];
    $pageId = (string)$page['pageid'];
    if (!$page['pageid']) {
      return false;
    }
    return (string)$page->revisions->rev[0];
  }

  static function maybeProtectMonitor($number, $year) {
    if (self::$botName && self::$botPassword) {
      $pageTitle = "Monitorul_Oficial_{$number}/{$year}";
      MediaWikiParser::botLogin();
      MediaWikiParser::botProtectMonitor($pageTitle);
      MediaWikiParser::botInsertMigrationWarning($pageTitle);
    }
  }

  static function botLogin() {
    $xmlString = Util::makePostRequest(self::$url . "?action=login&format=xml",
				       array('lgname' => self::$botName,
                                             'lgpassword' => self::$botPassword),
				       true);
    $xml = simplexml_load_string($xmlString);
    // $result = (string)$xml->login['result'];
    $token = (string)$xml->login['token'];

    Util::makePostRequest(self::$url . "?action=login&format=xml",
                          array('lgname' => self::$botName,
                                'lgpassword' => self::$botPassword,
                                'lgtoken' => $token),
                          true);
  }

  static function botProtectMonitor($pageTitle) {
    $xmlString = Util::makePostRequest(self::$url,
				       array('action' => 'query',
                                             'format' => 'xml',
                                             'prop' => 'info',
                                             'intoken' => 'protect',
                                             'titles' => $pageTitle),
				       true);
    $xml = simplexml_load_string($xmlString);
    $protectToken = (string)$xml->query->pages->page[0]['protecttoken'];

    Util::makePostRequest(self::$url,
                          array('action' => 'protect',
                                'format' => 'xml',
                                'title' => $pageTitle,
                                'protections' => 'edit=sysop',
                                'expiry' => 'never',
                                'reason' => 'Migrat la civvic.ro',
                                'token' => $protectToken),
                          true);
  }

  static function botInsertMigrationWarning($pageTitle) {
    $contents = self::fetchPage($pageTitle);
    if (strpos($contents, '{{MigrationWarning}}') !== false) {
      return; // Page already contains warning
    }
    $xmlString = Util::makePostRequest(self::$url,
				       array('action' => 'query',
                                             'format' => 'xml',
                                             'prop' => 'info',
                                             'intoken' => 'edit',
                                             'titles' => $pageTitle),
				       true);
    $xml = simplexml_load_string($xmlString);
    $editToken = (string)$xml->query->pages->page[0]['edittoken'];

    Util::makePostRequest(self::$url,
                          array('action' => 'edit',
                                'format' => 'xml',
                                'title' => $pageTitle,
                                'section' => '0',
                                'prependtext' => "{{MigrationWarning}}\n",
                                'summary' => 'Migrat la civvic.ro',
                                'token' => $editToken),
                          true);
  }

  private static function ensureReferences($text) {
    if (preg_match("/<\\s*ref\\s+name*\\s*=/", $text)) {
      FlashMessage::add('Sistemul nu admite referințe cu &ltref name="..."&gt. Vă rugăm să copiați explicit textul referinței.', 'warning');
    }
    if (preg_match("/<\\s*ref[^>]*>/", $text) && !preg_match("/<\\s*references\\s*\\/>/", $text)) {
      $text .= "\n<references/>";
      FlashMessage::add('Dacă folosiți &lt;ref&gt; pentru a indica referințe, nu uitați să adăugați eticheta &lt;references/&gt; la sfârșit.', 'warning');
    }
    return $text;
  }

  private static function insertChangeDetails($actVersion) {
    $ann = json_decode($actVersion->annotated, true);
    $output = array();
    $version = 'a1';
    $n = count($ann['lines']);

    $modifyingActs = Act::getModifyingActs($actVersion->actId);
    $modifyingActs[$actVersion->versionNumber] = Act::get_by_id($actVersion->modifyingActId); // In case it hasn't yet been saved.

    $tableLevel = 0; // We cannot insert a div in the middle of a table

    for ($i = 0; $i < $n; $i++) {
      if ($ann['history'][$i] != $version) {
        // Close the previous section, if needed
        if ($tableLevel) {
          $output[] = '|}';
        }
        if ($version != 'a1') {
          $output[] = '</div>';
          switch (substr($version, 0, 1)) {
          case 'a': $keyword = 'Adăugat'; break;
          case 'm': $keyword = 'Modificat'; break;
          default: $keyword = 'Abrogat';
          }
          $act = $modifyingActs[substr($version, 1)];
          $actText = $act ? $act->getDisplayId() : 'un act necunoscut';
          $output[] = sprintf("<div class=\"actChangeDetails\">%s de %s</div>", $keyword, $actText);
        }
        $version = $ann['history'][$i];
        if ($version != 'a1') {
          switch (substr($version, 0, 1)) {
          case 'a': $divClass = 'addedSection'; break;
          case 'm': $divClass = 'modifiedSection'; break;
          default: $divClass = 'deletedSection';
          }
          $output[] = "<div class=\"actChange {$divClass}\">";
        }
        if ($tableLevel) {
          $output[] = '{|';
        }
      }
      $line = $ann['lines'][$i];
      $output[] = $line;
      if (StringUtil::startsWith($line, '{|')) {
        $tableLevel++;
      } else if (StringUtil::startsWith($line, '|}')) {
        $tableLevel--;
      }
    }

    if ($version != 'a1') {
      if ($tableLevel) {
        $output[] = '|}';
      }
      $output[] = '</div>';
      switch (substr($version, 0, 1)) {
      case 'a': $keyword = 'Adăugat'; break;
      case 'm': $keyword = 'Modificat'; break;
      default: $keyword = 'Abrogat';
      }
      $act = $modifyingActs[substr($version, 1)];
      $actText = $act ? $act->getDisplayId() : 'un act necunoscut';
      $output[] = sprintf("<div class=\"actChangeDetails\">%s de %s</div>", $keyword, $actText);
    }
    return implode("\n", $output);
  }

  static function parse($text) {
    $text = "__NOTOC__\n" . $text;
    $xmlString = Util::makePostRequest(self::$url, array('action' => 'parse', 'text' => $text, 'format' => 'xml'));
    $xml = simplexml_load_string($xmlString);
    return (string)$xml->parse->text;
  }

  static function deleteEmptyTables($text) {
    return preg_replace("/<table>\\s*<tr>\\s*<td>\\s*<\\/td>\\s*<\\/tr>\\s*<\\/table>/i", '', $text);
  }

  // Returns an array consisting of a monitor and a collection of acts and their versions.
  // Returns false and sets flash messages on all errors.
  static function importMonitor($number, $year) {
    // Check that we don't already have this monitor
    $monitor = Model::factory('Monitor')->where('number', $number)->where('year', $year)->find_one();
    if ($monitor) {
      FlashMessage::add("Monitorul {$number}/{$year} a fost deja importat (sau există în sistem din alt motiv).");
      return false;
    }

    // Fetch the contents
    $contents = self::fetchPage("Monitorul_Oficial_{$number}/{$year}");
    if ($contents === false) {
      FlashMessage::add("Monitorul {$number}/{$year} nu există.");
      return false;
    }

    // Extract the publication date
    $regexp = sprintf("/Anul\\s+[IVXLCDM]+,?\\s+Nr\\.\\s+\\[\\[issue::\s*(?P<number>[-0-9A-Za-z.]+)\\]\\]\\s+-\\s+(Partea\\s+I\\s+-\\s+)?" .
                      "(Luni|Marți|Miercuri|Joi|Vineri|Sâmbătă|Duminică),?\\s*(?P<day>\\d{1,2})\\s+(?P<month>%s)\\s+" .
                      "\\[\\[year::\s*(?P<year>\\d{4})\\]\\]/i", implode('|', StringUtil::$months));
    preg_match($regexp, $contents, $matches);
    if (!count($matches)) {
      FlashMessage::add('Nu pot extrage data din primele linii ale monitorului.');
    }
    if ($matches['number'] != $number) {
      FlashMessage::add(sprintf("Numărul din monitor (%s) nu coincide cu numărul din URL (%s).", $matches['number'], $number));
      return false;
    }
    if ($matches['year'] != $year) {
      FlashMessage::add(sprintf("Anul din monitor (%s) nu coincide cu anul din URL (%s).", $matches['year'], $year));
      return false;
    }
    $month = 1 + array_search($matches['month'], StringUtil::$months);

    // Build the monitor
    $monitor = Model::factory('Monitor')->create();
    $monitor->number = $number;
    $monitor->year = $year;
    $monitor->issueDate = sprintf("%4d-%02d-%02d", $year, $month, $matches['day']);
    $data['monitor'] = $monitor;
    $data['acts'] = array();
    $data['actVersions'] = array();
    $data['authorIds'] = array();

    // Split the contents into lines and locate the == and === headers
    $lines = explode("\n", $contents);
    $headers23 = array();
    foreach ($lines as $i => $line) {
      if (StringUtil::startsWith($line, '==') && !StringUtil::startsWith($line, '====')) {
        $headers23[] = $i;
      }
    }
    $headers23[] = count($lines);

    $actTypes = Model::factory('ActType')->raw_query('select * from act_type order by length(name) desc', null)->find_many();

    foreach ($headers23 as $i => $lineNo) {
      if ($i < count($headers23) - 1 && StringUtil::startsWith($lines[$lineNo], '===')) {
        $chunk = array_slice($lines, $lineNo, $headers23[$i + 1] - $lineNo);
        $act = Model::factory('Act')->create();
        $act->year = $monitor->year;
        $authorIds = array();

        // Extract the title from the first line
        $matches = array();
        preg_match("/^\\s*===(?P<title>.+)===\\s*$/", $chunk[0], $matches);
        if (!array_key_exists('title', $matches)) {
          FlashMessage::add("Nu pot extrage titlul actului din linia '{$chunk[0]}'.");
          return false;
        }
        $act->name = trim($matches['title']);

        // Extract the act type from the title
        $i = 0;
        do {
          if ($actTypes[$i]->shortName &&
              (StringUtil::startsWith($act->name, $actTypes[$i]->name) ||
               StringUtil::startsWith($act->name, $actTypes[$i]->shortName) ||
               StringUtil::startsWith($act->name, $actTypes[$i]->artName))) {
            $act->actTypeId = $actTypes[$i]->id;
          }
          $i++;
        } while ($i < count($actTypes) && !$act->actTypeId);
        if (!$act->actTypeId) {
          FlashMessage::add("Nu pot extrage tipul de act din titlul '{$act->name}'. Voi folosi implicit tipul 'Diverse'.", 'warning');
          $diverse = ActType::get_by_name('Diverse');
          $act->actTypeId = $diverse->id;
        }

        // Locate the signature line
        $signIndex = count($chunk);
        do {
          $signIndex--;
          $signLine = trim($chunk[$signIndex]);
          $found = StringUtil::startsWith($signLine, '{{') && StringUtil::endsWith($signLine, '}}');
        } while ($signIndex > 0 && !$found);
        if ($found) {
          $signData = self::parseSignatureLine($signLine);
          if (!$signData) {
            return false;
          }
          $authorIds[] = $signData['authorId'];
          $act->placeId = $signData['placeId'];
          $act->issueDate = $signData['issueDate'];
          $act->number = $signData['number'];

          if ($act->issueDate) {
            $issueDateYear = substr($act->issueDate, 0, 4);
            if ($issueDateYear != $act->year) {
              $act->year = $issueDateYear;
              FlashMessage::add(sprintf("%s a fost emis în %s, dar publicat în %s. Asigurați-vă că am ales bine anul actului.",
                                        $act->getDisplayId(), $issueDateYear, $monitor->year), 'warning');
            }
          }

          if ($act->year && $act->number) {
            $other = Model::factory('Act')->where('actTypeId', $act->actTypeId)->where('year', $act->year)
              ->where('number', $act->number)->find_one();
            if ($other) {
              FlashMessage::add(sprintf("Actul '%s' există deja.", $act->getDisplayId()), 'warning');
            }
          }
          

          array_splice($chunk, $signIndex, 1);
        } else {
          FlashMessage::add("Nu pot găsi linia cu semnătura în actul '{$act->name}'.", 'warning');
        }

        // Create the act version
        $av = ActVersion::createVersionOne($act);
        $av->contents = trim(implode("\n", array_slice($chunk, 1)));
        $data['acts'][] = $act;
        $data['actVersions'][] = $av;
        $data['authorIds'][] = $authorIds;
      }
    }

    return $data;
  }

  static function parseSignatureLine($line) {
    // Some signatures use named parameters, others use unnamed parameters
    $parts = explode('|', substr($line, 2, -2));
    foreach ($parts as $part) {
      $nv = preg_split('/=/', $part, 2);
      if (count($nv) == 2) {
        $parts[trim($nv[0])] = trim($nv[1]);
      }
    }
    $data = array();

    // Parse the signature line
    switch($parts[0]) {
    case 'SemnPcfsn':
      $author = Model::factory('Author')->where('position', 'Președintele Consiliului Frontului Salvării Naționale')
        ->where('name', $parts[1])->find_one();
      if (!$author) {
        FlashMessage::add("Trebuie definit autorul 'Președintele Consiliului Frontului Salvării Naționale, {$parts[1]}'.");
        return false;
      }
      $data['authorId'] = $author->id;

      $place = Place::get_by_name($parts[2]);
      if (!$place) {
        FlashMessage::add("Trebuie definit locul '{$parts[2]}'.");
        return false;
      }
      $data['placeId'] = $place->id;

      $issueDate = StringUtil::parseRomanianDate($parts[3]);
      if (!$issueDate) {
        FlashMessage::add(sprintf("Data '%s' este incorectă.", $parts[3]));
        return false;
      }
      $data['issueDate'] = $issueDate;

      $data['number'] = $parts[4];
      break;

    case 'SemnPm':
      $author = Model::factory('Author')->where('position', 'Prim-ministru')->where('name', $parts[1])->find_one();
      if (!$author) {
        FlashMessage::add("Trebuie definit autorul 'Prim-ministru, {$parts[1]}'.");
        return false;
      }
      $data['authorId'] = $author->id;

      $place = Place::get_by_name($parts[2]);
      if (!$place) {
        FlashMessage::add("Trebuie definit locul '{$parts[2]}'.");
        return false;
      }
      $data['placeId'] = $place->id;

      $issueDate = StringUtil::parseRomanianDate($parts[3]);
      if (!$issueDate) {
        FlashMessage::add(sprintf("Data '%s' este incorectă.", $parts[3]));
        return false;
      }
      $data['issueDate'] = $issueDate;

      $data['number'] = $parts[4];
      break;

    case 'Sem-p-Pm':
      foreach (array('pt', 'nume', 'func', 'dataAct', 'nrAct') as $arg) {
        if (!array_key_exists($arg, $parts)) {
          FlashMessage::add("Semnătura '{$line}' nu include parametrul '{$arg}'.");
          return false;
        }
      }
      if (!array_key_exists('oras', $parts)) {
        $parts['oras'] = 'București';
      }
      if ($parts['func']) {
        FlashMessage::add("Nu știu să gestionez argumentul 'func' în semnătura '{$line}'.");
        return false;
      }

      $author = Model::factory('Author')->where('position', $parts['pt'] . ' Prim-ministru')->where('name', $parts['nume'])->find_one();
      if (!$author) {
        FlashMessage::add(sprintf("Trebuie definit autorul '%s Prim-ministru, %s', ", $parts['pt'], $parts['nume']));
        return false;
      }
      $data['authorId'] = $author->id;

      $place = Place::get_by_name($parts['oras']);
      if (!$place) {
        FlashMessage::add(sprintf("Trebuie definit locul '%s'.", $parts['oras']));
        return false;
      }
      $data['placeId'] = $place->id;

      $issueDate = StringUtil::parseRomanianDate($parts['dataAct']);
      if (!$issueDate) {
        FlashMessage::add(sprintf("Data '%s' este incorectă.", $parts['dataAct']));
        return false;
      }
      $data['issueDate'] = $issueDate;

      $data['number'] = $parts['nrAct'];
      break;

    case 'SemnCfsn':
      $author = Model::factory('Author')->where('institution', 'Consiliul Frontului Salvării Naționale')->where('name', '')->find_one();
      if (!$author) {
        FlashMessage::add("Trebuie definit autorul 'Consiliul Frontului Salvării Naționale'.");
        return false;
      }
      $data['authorId'] = $author->id;

      $place = Place::get_by_name($parts[1]);
      if (!$place) {
        FlashMessage::add("Trebuie definit locul '{$parts[1]}'.");
        return false;
      }
      $data['placeId'] = $place->id;

      $issueDate = StringUtil::parseRomanianDate($parts[2]);
      if (!$issueDate) {
        FlashMessage::add(sprintf("Data '%s' este incorectă.", $parts[2]));
        return false;
      }
      $data['issueDate'] = $issueDate;

      $data['number'] = $parts[3];
      break;

    case 'SemnPcpun':
      $author = Model::factory('Author')->where('position', 'Președintele Consiliului Provizoriu de Uniune Națională')
        ->where('name', $parts[1])->find_one();
      if (!$author) {
        FlashMessage::add("Trebuie definit autorul 'Președintele Consiliului Frontului Salvării Naționale, {$parts[1]}'.");
        return false;
      }
      $data['authorId'] = $author->id;

      $place = Place::get_by_name($parts[2]);
      if (!$place) {
        FlashMessage::add("Trebuie definit locul '{$parts[2]}'.");
        return false;
      }
      $data['placeId'] = $place->id;

      $issueDate = StringUtil::parseRomanianDate($parts[3]);
      if (!$issueDate) {
        FlashMessage::add(sprintf("Data '%s' este incorectă.", $parts[3]));
        return false;
      }
      $data['issueDate'] = $issueDate;

      $data['number'] = $parts[4];
      break;

    default:
      FlashMessage::add(sprintf("Nu știu să interpretez semnături de tipul {{%s}}.", $parts[0]));
      return false;
    }
    return $data;
  }

}

?>
