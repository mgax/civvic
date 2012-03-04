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

  static function wikiToHtml($text, &$references = null) {
    $text = self::ensureReferences($text);
    $text = self::parse($text);

    // Automatic links to acts
    $actTypes = Model::factory('ActType')->find_many();
    foreach ($actTypes as $at) {
      $type = sprintf("(%s|%s|%s)", $at->name, $at->artName, $at->genArtName);
      // Parses "din <day> <month> <year>" or "/ <year>"
      $date = sprintf("((\\s+din\\s+(\\d{1,2})\\s+(%s)\\s+)|(\\s*\\/\\s*))(?P<year>\\d{4})", implode('|', StringUtil::$months));
      $regexp = "/{$type}\\s+(nr\\.?)?\\s*(?P<number>\\d+){$date}/i";
      $matches = array();
      preg_match_all($regexp, $text, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
      foreach (array_reverse($matches) as $match) {
        $linkText = $match[0][0];
        $position = $match[0][1];
        $number = $match['number'][0];
        $year = $match['year'][0];

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
    if (preg_match("/<\\s*ref\\s*>/", $text) && !preg_match("/<\\s*references\\s*\\/>/", $text)) {
      $text .= "\n<references/>";
      FlashMessage::add('Dacă folosiți &lt;ref&gt; pentru a indica referințe, nu uitați să adăugați eticheta &lt;references/&gt; la sfârșit.', 'warning');
    }
    return $text;
  }

  static function parse($text) {
    $xmlString = Util::makePostRequest(self::$url, array('action' => 'parse', 'text' => $text, 'format' => 'xml'));
    $xml = simplexml_load_string($xmlString);
    return (string)$xml->parse->text;
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
    $regexp = sprintf("/Anul\\s+[IVXLCDM]+,?\\s+Nr\\.\\s+\\[\\[issue::\s*(?P<number>\\d+)\\]\\]\\s+-\\s+(Partea\\s+I\\s+-\\s+)?" .
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
      FlashMessage::add(sprintf("Anul din monitor (%s) nu coincide cu numărul din URL (%s).", $matches['year'], $year));
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

    // Split the contents into lines and locate the == and === headers
    $lines = explode("\n", $contents);
    $headers23 = array();
    foreach ($lines as $i => $line) {
      if (StringUtil::startsWith($line, '==') && !StringUtil::startsWith($line, '====')) {
        $headers23[] = $i;
      }
    }
    $headers23[] = count($lines);

    $actTypes = Model::factory('ActType')->find_many();

    foreach ($headers23 as $i => $lineNo) {
      if ($i < count($headers23) - 1 && StringUtil::startsWith($lines[$lineNo], '===')) {
        $chunk = array_slice($lines, $lineNo, $headers23[$i + 1] - $lineNo);
        $act = Model::factory('Act')->create();
        $act->year = $monitor->year;

        // Extract the title from the first line
        $matches = array();
        preg_match("/^\\s*===(?P<title>.+)===\\s*$/", $chunk[0], $matches);
        if (!array_key_exists('title', $matches)) {
          FlashMessage::add("Nu pot extrage titlul actului din linia '{$chunk[0]}'.");
          return false;
        }
        $act->name = trim($matches['title']);

        // Extract the act type from the title
        foreach ($actTypes as $actType) {
          if ($actType->shortName &&
              (StringUtil::startsWith($act->name, $actType->name . ' ') ||
               StringUtil::startsWith($act->name, $actType->shortName . ' ') ||
               StringUtil::startsWith($act->name, $actType->artName . ' '))) {
            $act->actTypeId = $actType->id;
          }
        }
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
          $act->authorId = $signData['authorId'];
          $act->placeId = $signData['placeId'];
          $act->issueDate = $signData['issueDate'];
          $act->number = $signData['number'];
        } else {
          FlashMessage::add("Nu pot găsi linia cu semnătura în actul '{$act->name}'.", 'warning');
        }

        // Create the act version
        $av = ActVersion::createVersionOne($act);
        $av->contents = trim(implode("\n", array_slice($chunk, 1, $found ? $signIndex - 1 : count($chunk))));
        $data['acts'][] = $act;
        $data['actVersions'][] = $av;
      }
    }

    return $data;
  }

  static function parseSignatureLine($line) {
    $parts = explode('|', substr($line, 2, -2));
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

      if (!ctype_digit($parts[4])) {
        FlashMessage::add("Numărul actului '{$parts[4]}' din semnătura $line este incorect.");
        return false;
      }
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

      if (!ctype_digit($parts[4])) {
        FlashMessage::add("Numărul actului '{$parts[4]}' din semnătura $line este incorect.");
        return false;
      }
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
