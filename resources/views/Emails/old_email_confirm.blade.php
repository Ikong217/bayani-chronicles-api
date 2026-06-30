<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Email Change</title>
</head>
<body>
    <h1>Click the code you see in the game</h1>

    <p>Please select the code that matches the one shown in your game to confirm your email change.</p>

    <p>
        <a href="{{ url('/Email/Code/Verify/' . $encryptedId . '/' . $codeSet[0]) }}">
            {{ $codeSet[0] }}
        </a>
        <a href="{{ url('/Email/Code/Verify/' . $encryptedId . '/' . $codeSet[1]) }}">
            {{ $codeSet[1] }}
        </a>
        <a href="{{ url('/Email/Code/Verify/' . $encryptedId . '/' . $codeSet[2]) }}">
            {{ $codeSet[2] }}
        </a>
    </p>

    <p>If you don’t wish to change your email, please ignore this message.</p>
</body>
</html>
