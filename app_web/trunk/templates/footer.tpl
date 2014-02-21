{literal}
<style type="text/css">
    footer.container.navbar-fixed-top {
        top: auto;
        bottom: 0;
        text-align: center;
    }
</style>
{/literal}
<footer class="container navbar" style="text-align: center">
{if $authenticated}
{include file='__footer/member.tpl'}
    {else}
{include file='__footer/guest.tpl'}
{/if}
    <script type="text/javascript" src="{$config->url->js}_bootstrap/bootstrap-button.js"></script>
    <script type="text/javascript" src="{$config->url->js}_bootstrap/bootstrap-dropdown.js"></script>
</footer>
</div>{* row *}
</div>{*container *}
</body>
</html>