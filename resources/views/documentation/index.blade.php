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
            padding: 30px;
            width: 100vw;
        }
    </style>
</head>

<body>
    <h1 style="color: black;">CODEX API v1 Documentation Index</h1>

    <h2>Totale APIs: {{ $totalAPIs }}</h2>
    
    <h3>Prefix: /api/</h3>

    @foreach ($indices as $folder => $routeIndices)

    <h2>{{ ucfirst($folder) }}</h2>
    <ul>
        @foreach ($routeIndices as $routeIndex)
        <li>
            <a href="{{ $routeIndex['link'] }}">{{ $routeIndex['route'] }}</a>
        </li>

        @endforeach
    </ul>
    @endforeach
</body>

</html>