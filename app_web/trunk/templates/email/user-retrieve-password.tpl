{$user->usernameEmail}, Your Account Password
Dear {$user->usernameEmail},

You recently requested a password reset as you had forgotten your password.

Your new password is listed below. To activate this password, click this link:

    Activate Password: http://tracks.ponyengine.com/account/retrievepassword?action=confirm&id={$user->getId()}&key={$user->profile->up58}
    Username: {$user->usernameEmail}
    New Password: {$tempArr._newPassword}

If you didn't request a password reset, please ignore this message and your password
will remain unchanged.

Sincerely,
The Event2Pix Team