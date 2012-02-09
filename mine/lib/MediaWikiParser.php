<?php

MediaWikiParser::init();

class MediaWikiParser {
  private static $url;

  static function init() {
    self::$url = Config::get('general.mediaWikiParser');
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
