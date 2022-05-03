<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">
    <title>laravel-hprose</title>
</head>
<body>


<table class="table">
    <thead>
    <tr>
        <th scope="col">class</th>
        <th scope="col">alias</th>
        <th scope="col">method</th>
        <th scope="col">arguments</th>
        <th scope="col">使用</th>
    </tr>
    </thead>
    @if(!empty($routers))
        <tbody>
        @foreach ($routers as $info)
            <tr>
                <td>{{ $info['class'] }}</td>
                <td>{{ $info['alias'] }}</td>
                <td>{{ $info['method'] }}</td>
                <td>{{ implode(',',$info['args'] )}}</td>
                <td>app('hprose.socket.client')->{{ $info['methods'] }}</td>
            </tr>
        @endforeach
        </tbody>
    @endif
</table>


</body>
</html>
