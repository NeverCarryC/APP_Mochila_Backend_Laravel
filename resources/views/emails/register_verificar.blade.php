<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperación de contraseña</title>
    <style>
        .copy-container {
            text-align: center;
            margin-top: 10px;
        }
        #code {
            font-size: 18px;
            padding: 8px;
            width: 60%; /* Adjust width as needed */
            text-align: center;
            border: 2px solid #007bff;
            border-radius: 5px;
            background-color: #f8f9fa;
            margin-bottom: 10px;
        }

        #copy-message {
            margin-top: 5px;
            font-size: 14px;
            color: green;
            display: none; /* Initially hidden */
        }
    </style>
</head>
<body>
    <h2>Hola,</h2>
    <p>Tu código de verificartu cuenta es:</p>

    <div class="copy-container">
        <input type="text" value="{{ $code }}" id="code" readonly>
        <p id="copy-message">¡Código copiado!</p>
    </div>

    <p>Este código expirará en 5 minutos.</p>

  
</body>
</html>