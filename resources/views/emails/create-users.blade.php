<!DOCTYPE html>
<html>
    <head>Tourist APP</head>

    <body>
        <p>Bienvenido a Tourist App</p>

        <p><strong>Usuario: </strong>{{$usuario['username']}}</p>
        <p><strong>Nombre completo: </strong>{{$usuario['nombres']}} {{$usuario['apellido1']}} {{$usuario['apellido2']}}</p>
        <p><strong>Rol: </strong> {{$usuario['rol']}}</p>

        <a href="{{url(env('APP_URL').'/auth/valid_user/'.$usuario['password'])}}"></a>

        <br>
        <p>Con estos datos puede acceder a nuestro sistema. Gracias Tourist.</p>
    </body>
</html>
