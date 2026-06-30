<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Bayani Chronicles Admin OTP Verification</title>
</head>

<body>
    <h2>
        @if ($type == 'new')
            Welcome new User!
        @else
            Your Account has been Updated!
        @endif
    </h2>

    <h1>Username: {{ $username }}</h1>

    @if ($type == 'new' && $pass)
        <h1>Password: {{ $pass }}</h1>
    @endif

    <p>Please do not share this to anyone.</p>
    <p><a href="{{ route('file.download', ['storage' => 'Application', 'filename' => 'Bayani Chronicles.apk']) }}">Click
            here</a> to download the app</p>
    {{-- <p>if you want to change your password, <a href="{{ route('users.forgot') }}">Click here</a></p> --}}
    <p>These credentials serves as authentication in your Bayani Chronicles game</p>
</body>

</html>
