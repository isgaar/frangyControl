<!DOCTYPE html>
<html>
<head>
    <title>Credenciales de Acceso - FrangyControl</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; border: 1px solid #ddd;">
        <h2 style="color: #2c3e50; text-align: center;">¡Bienvenido a FrangyControl!</h2>
        
        <p>Hola <strong>{{ $user->name }}</strong>,</p>
        
        <p>Se ha creado una cuenta para ti en el sistema FrangyControl. A continuación, te proporcionamos tus credenciales de acceso:</p>
        
        <div style="background-color: #ffffff; padding: 15px; border-radius: 5px; margin: 20px 0; border: 1px solid #eee;">
            <p style="margin: 5px 0;"><strong>Correo Electrónico:</strong> {{ $user->email }}</p>
            <p style="margin: 5px 0;"><strong>Contraseña:</strong> {{ $password }}</p>
        </div>
        
        <p>Puedes iniciar sesión en el siguiente enlace:</p>
        <div style="text-align: center; margin: 25px 0;">
            <a href="{{ url('/login') }}" style="background-color: #3490dc; color: #ffffff; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;">Acceder al Sistema</a>
        </div>
        
        <p style="font-size: 0.9em; color: #666;">Por tu seguridad, te recomendamos cambiar tu contraseña una vez que inicies sesión.</p>
        
        <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">
        <p style="font-size: 0.8em; color: #999; text-align: center;">Este es un mensaje automático, por favor no respondas a este correo.</p>
    </div>
</body>
</html>
