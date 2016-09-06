<h3>Reset password</h3>

<p>Dear {{$user->first_name}} {{$user->last_name}},</p>

<p>There was a request to change your password.</p>

<p>If you did not make this request, please ignore this email. Otherwise, click the button below to change your
    password:</p>

<div><!--[if mso]>
    <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word"
                 href="{{ route('password.reset.get', $token).'?email='.urlencode($user->getEmailForPasswordReset()) }}"
                 style="height:40px;v-text-anchor:middle;width:200px;" arcsize="10%" stroke="f" fillcolor="#2e8bef">
        <w:anchorlock/>
        <center>
    <![endif]-->
    <a href="{{ route('password.reset.get', $token).'?email='.urlencode($user->getEmailForPasswordReset()) }}"
       style="background-color:#2e8bef;border-radius:4px;color:#ffffff;display:inline-block;font-family:sans-serif;font-size:13px;font-weight:bold;line-height:40px;text-align:center;text-decoration:none;width:200px;-webkit-text-size-adjust:none;">
        Reset Password
    </a>
    <!--[if mso]>
    </center>
    </v:roundrect>
    <![endif]--></div>