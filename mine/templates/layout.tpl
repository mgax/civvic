<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
  <head>
    <title>{$pageTitle} | Civvic.ro</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link type="text/css" href="{$wwwRoot}css/main.css?v=1" rel="stylesheet"/>
    <link type="text/css" href="{$wwwRoot}css/smoothness/jquery-ui-1.8.17.custom.css" rel="stylesheet" />	
    <script type="text/javascript" src="{$wwwRoot}js/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="{$wwwRoot}js/jquery-ui-1.8.17.custom.min.js"></script>
    <script type="text/javascript" src="{$wwwRoot}js/jquery.ui.datepicker-ro.js"></script>
    <script type="text/javascript" src="{$wwwRoot}js/main.js"></script>
  </head>

  <body>
    <div id="sidebar">
      <div id="logo">
        <a href="{$wwwRoot}"><img src="{$wwwRoot}img/logo.png" title="Civvic logo"/></a>
      </div>
      <ul id="sideMenu">
        {if $user && $user->admin}
          <li><a href="tipuri-acte">tipuri de acte</a></li>
          <li><a href="acte">acte</a></li>
          <li><a href="monitoare">monitoare</a></li>
        {/if}
      </ul>
    </div>
    <div id="main">
      <div id="userActions">
        {if $user}
          {$user->getDisplayName()}
          <a href="{$wwwRoot}auth/logout">deconectare</a>
        {else}
          <a id="openidLink" href="{$wwwRoot}auth/login">autentificare cu OpenID</a>
        {/if}
      </div>
      {include file="bits/flashMessage.tpl"}
      <div id="template">
        {include file=$templateName}
      </div>
    </div>
  </body>

</html>
