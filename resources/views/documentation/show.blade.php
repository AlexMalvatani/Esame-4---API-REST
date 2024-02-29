<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Documentation</title>

    <style>
        body {
            box-sizing: border-box;
            padding: 15px;
            width: 100vw;
        }
    </style>
</head>

<body>
    <div>
        <a href="{{ route('documentation.index') }}">Torna all'Indice</a>
        <hr>
        {!! $formattedContent !!}
    </div>
</body>

</html>