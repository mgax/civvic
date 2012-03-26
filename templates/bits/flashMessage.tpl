{php}
  $this->assign('flashMessage', FlashMessage::$message);
  $this->assign('flashMessageType', FlashMessage::$type);
{/php}
{if $flashMessage}
  <div class="flashMessage {$flashMessageType}Type">{$flashMessage}</div>
{/if}
