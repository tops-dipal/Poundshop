<p>Hello {{ $data['toName'] }}</p>

<p>You profile has been created successfully at Pounshop.</p>

<p>Your Credentials:</p>
<p> Link: <a href="{{ route('login') }}">Click Here To Login</a></p>
<p>UserName: {{ $data['username'] }}</p>
<p>Password:{{ $data['password'] }}</p>