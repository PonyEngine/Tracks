{include file='header.tpl'}
     <img src="{$user->picURL($this->config)}"/> <br/>
     Profile Name: {$user->profileName} <br />
       First Name: {$user->profile->up1} <br />
        Last Name: {$user->profile->up3} <br />
           E-Mail: {$user->usernameEmail} <br />
               IP: {$user->profile->up50} <br />
     {if $user->fbId}
      Facebook Id: {$user->fbId} <br />
    Facebook Name: {$user->profile->up20} <br />
  Facebook E_Mail: {$user->profile->up18} <br />
     {/if}
            Level: {$user->level} <br />
           Points: {$user->points} <br />
            Bucks: {$user->bucks} <br/>
        {*http://www.smarty.net/docsv2/en/language.modifier.date.format.tpl*}
       Created: {$user->tsCreated|date_format:"%m/%d/%Y at %l:%M %p"} <br/>
          &nbsp;&nbsp;&nbsp;{distance_of_time fromUnixTime=$user->tsCreated} <br/>
       Last Login: {$user->tsLastLogin|date_format:"%m/%d/%Y at %l:%M %p"} <br />
       &nbsp;&nbsp;&nbsp;{distance_of_time fromUnixTime=$user->tsLastLogin} <br/>

{include file='footer.tpl'}