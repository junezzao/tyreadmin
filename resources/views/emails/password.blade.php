<p>Hi there,</p>

<p>A request to reset your account's password was recently made.</p>

<p>Click here to reset your password: {{ url('password/reset/'.$token) }} </p>

<p>If you did not make such a request, please report the incident to our our customer support team at <a href="mailto:{{ env('SYSTEM_SUPPORT_EMAIL') }}"></a>.

<p>- Pro tyre Admin Team</p>