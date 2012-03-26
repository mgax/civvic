<?php

class CroppedImage extends BaseObject {

  function validate() {
    if (mb_strlen($this->name) < 3) {
      FlashMessage::add('Numele imaginii trebuie să aibă minim 3 caractere.');
    }
    $invalid = false;
    for ($i = 0; $i < mb_strlen($this->name); $i++) {
      $char = mb_substr($this->name, $i, 1);
      if (!ctype_alnum($char) && $char != '-' && $char != '.' && $char != '_') {
        $invalid = true;
      }
    }
    if ($invalid) {
      FlashMessage::add('Numele imaginii poate conține doar litere, cifre și semnele .-_');
    }
    if (!$this->width || !$this->height) {
      FlashMessage::add('Trebuie să selectați o regiune din imagine.');
    }
    $other = CroppedImage::get_by_name($this->name);
    if ($other) {
      FlashMessage::add("Există deja o imagine cu numele '{$this->name}'.");
    }
    return !FlashMessage::getMessage();
  }

  function getFileName() {
    return sprintf("%s/www/img/%s/%s.png", Util::$rootPath, Config::get('pdfImage.croppedImageDir'), $this->name);
  }

  /* Make sure the image file exists in croppedImageDir; if not, save it from the contents field. */
  function ensureFile() {
    $filename = $this->getFileName();
    if (!file_exists($filename)) {
      file_put_contents($filename, $this->contents);
    }
  }

  /* Also saves the image to its respective file. */
  function cropFrom($pdfImageName) {
    $tmpDir = Config::get('pdfImage.tmpImageDir');
    $pdfFileName = "{$tmpDir}/{$pdfImageName}.png";
    $src = imagecreatefrompng($pdfFileName);
    $dst = imagecreatetruecolor($this->width, $this->height);
    imagecopy($dst, $src, 0, 0, $this->x0, $this->y0, $this->width, $this->height);
    $filename = $this->getFileName();
    imagepng($dst, $filename, 9); // Maximum compression
    $this->contents = file_get_contents($filename);
  }

}

?>
