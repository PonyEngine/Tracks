<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"/>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en" xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
    <meta content="text/html; charset=UTF-8" http-equiv="Content-Type" />
    <title>{$config->title} {if $config->version->num}({$config->version->num}){/if}{if $header.title!=''}-{/if}{$header.title|escape}</title>
    <meta name="description" content="Tracks App">
    <meta name="author" content="Tracks team">
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <link rel="icon" href="/favicon.ico" type="image/x-icon"/>
    <link rel="shortcut icon" href="/favicon.ico" />
    <script type="text/javascript" src="{$config->url->js}_jquery/jquery1.7.2.js"></script>
    <link href="{$config->url->css}_bootstrap/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="{$config->url->css}_bootstrap/font-awesome.css">
    {*<link href="{$config->url->css}_bootstrap/bootstrap-responsive.css" rel="stylesheet">*}
    <link href="{$config->url->css}eventphotoapp.css" rel="stylesheet">
    <meta charset="utf-8">
    <!-- Le HTML5 shim, for IE6-8 support of HTML elements -->
    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
   <script type="text/javascript" src="{$config->url->js}_social/fbLoginBtn.js"></script>
    <script type="text/javascript" src="{$config->url->js}_knockout/knockout-2.0.0.js"></script>
    <script type="text/javascript" src="{$config->url->js}_knockout/knockout.mapping.js"></script>
    <script type="text/javascript" src="{$config->url->js}json2.js"></script>
    <link rel="shortcut icon" href="images/favicon.ico">
    <link rel="apple-touch-icon" href="images/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="72x72" href="images/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="114x114" href="images/apple-touch-icon-114x114.png">

    {foreach from=$cssCode key=i item=cssCode}
        <link rel="stylesheet" type="text/css" href="{$cssCode}" />
    {/foreach}
    {foreach from=$lessCode key=i item=lessCode}
        <script type="text/javascript" src="{$lessCode}"></script>
    {/foreach}
    {foreach from=$jsCode key=i item=jsCode}
        <script type="text/javascript" src="{$jsCode}"></script>
    {/foreach}
</head>
<body>
{literal}
<style type="text/css">
    .nav-collapse.collapse { height: 0; overflow: visible; }
</style>
{/literal}

{if $auth->hasIdentity()}
        {include file='__header/member.tpl'}
 {else}
        {include file='__header/guest.tpl'}
  {/if}

<div class="container-fluid">
<br/><br /><br />