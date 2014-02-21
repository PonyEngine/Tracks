{include file='header.tpl'}
<div class="span-22 stdMainBody append-1 prepend-1 last" >
{if $action == 'confirm'}
    {if $errors|@count == 0}
        <p>
            Your new password has now been activated.
        </p>

        <ul>
            <li><a href="/account/login">Log in to your account</a></li>
        </ul>
    {else}
        <p>
            Your new password was not confirmed. Please double-check the link
            sent to you by e-mail, or try using the
            <a href="/account/retreivepassword">Retreive Password</a> tool again.
        </p>
    {/if}
{elseif $action == 'complete'}
    <p>
        A password has been sent to your account e-mail address containing
        your new password. You must click the link in this e-mail to activate
        the new password.
    </p>
{else}
	<div class="registrationForm span-8" >
		<h2>Retrieve Password</h2>
    	<form method="post" action="/account/retrievepassword">
          
            <div class="row" id="form_usernameEmail_container">
                <label for="form_usernameEmail">Email Login:</label>
                <input type="text" id="form_usernameEmail" class="lText tText waterMark" sav="myaddress@tracks.ponyengine.com" name="usernameEmail" />
                {include file='lib/error.tpl' error=$errors.usernameEmail}
            </div>
            <div class="submit">
                <input type="submit" value="Retrieve Password" />
            </div>
    </form>
	</div>
{/if}
</div>
{include file='footer.tpl'}