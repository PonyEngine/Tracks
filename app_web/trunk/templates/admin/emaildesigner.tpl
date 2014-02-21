{include file='header.tpl'}
<head>
    <script type="text/javascript" src="{$config->url->js}_redactor/redactor.js"></script>
    <link rel="stylesheet" href="{$config->url->js}_redactor/css/redactor.css" />
    <link href="{$config->url->css}admin/emaildesigner.css" rel="stylesheet">
</head>
<br />
<div class="container-fluid">
    <div class="page-header">
        <h3>Email Designer <small>Design custom emails for internal use</small></h3>
    </div>
    <div class="row-fluid">
        <div class="span2">
            <div class="well sidebar-nav">
                <ul class="nav nav-list">
                    <div class="control-group">
                        <label class="control-label" for="saveEmail"></label>
                        <div class="controls">
                            <button id="saveEmail">Save</button>
                        </div>
                    </div>
                 </ul>
             </div>
        </div>
        <div class="span10">
<textarea id="redactor_content" name="content" style="height: 560px;"></textarea>
<script type="text/javascript" src="{$config->url->js}admin/emaildesigner.js"></script>
            </div>
      </div>
    </div>
{include file='footer.tpl'}