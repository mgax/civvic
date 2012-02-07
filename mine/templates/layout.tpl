<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
  <head>
    <title>{$pageTitle} | Civvic.ro</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link href="{$wwwRoot}/css/main.css?v=1" rel="stylesheet" type="text/css"/>
  </head>

  <body>
    <div id="sidebar">
      <div id="logo">
        <a href="{$wwwRoot}"><img src="{$wwwRoot}/img/logo.png" title="Civvic logo"/></a>
      </div>
      Meniu
    </div>
    <div id="main">
      <div id="userActions">
        {if $user}
          {$user->getDisplayName()}
          <a href="{$wwwRoot}/auth/logout">deconectare</a>
        {else}
          <a id="openidLink" href="{$wwwRoot}/auth/login">autentificare cu OpenID</a>
        {/if}
      </div>
      {include file="bits/flashMessage.tpl"}
      <div id="template">
        {include file=$templateName}
      </div>
    </div>
  </body>

</html>
