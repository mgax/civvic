<?php

MediaWikiParser::init();

class MediaWikiParser {
  private static $url;

  static function init() {
    self::$url = Config::get('general.mediaWikiParser');
  }

  static function wikiToHtml($text, &$references = null) {
    // Automatic links to acts
    $actTypes = Model::factory('ActType')->find_many();
    foreach ($actTypes as $at) {
      $regexp = sprintf("/((%s|%s)\\s+(nr\\.?)?\\s*(?P<number>\\d+)\\s*\\/\\s*(?P<year>\\d{4}))/i", $at->name, $at->artName);
      $matches = array();
      preg_match_all($regexp, $text, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
      foreach (array_reverse($matches) as $match) {
        $linkText = $match[0][0];
        $position = $match[0][1];
        $number = $match['number'][0];
        $year = $match['year'][0];
        $text = substr($text, 0, $position) .
          sprintf("[http://civvic.ro/cauta?actTypeId=%s&amp;number=%s&amp;year=%s %s]", $at->id, $number, $year, $linkText) .
          substr($text, $position + strlen($linkText));
        if ($references !== null) {
          $ref = Model::factory('Reference')->create();
          $ref->actTypeId = $at->id;
          $ref->number = $number;
          $ref->year = $year;
          $references[] = $ref;
        }
      }
    }
    return self::parse($text);
  }

  static function getMediaWikiVersion() {
    $xmlString = Util::makePostRequest(self::$url, array('action' => 'expandtemplates', 'text' => "{{CURRENTVERSION}}", 'format' => 'xml'));
    $xml = simplexml_load_string($xmlString);
    return (string)$xml->expandtemplates;
  }

  static function parse($text) {
    $xmlString = Util::makePostRequest(self::$url, array('action' => 'parse', 'text' => $text, 'format' => 'xml'));
    $xml = simplexml_load_string($xmlString);
    return (string)$xml->parse->text;
  }

}

?>
