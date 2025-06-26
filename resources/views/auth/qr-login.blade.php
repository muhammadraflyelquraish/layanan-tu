<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR - Login</title>
    <link href="{{ asset('build/assets') }}/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="{{ asset('logo/uinxs.png') }}" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        /* Container utama */
        .qr-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f8f9fa;
            padding: 20px;
        }

        /* Konten utama */
        .text-center {
            text-align: center;
        }

        .qr-title {
            margin-bottom: 20px;
            font-size: 1.5rem;
            color: #333;
        }

        /* .qr-code {
            margin: 20px 0;
            margin-left: 70px;
        } */

        .qr-subtitle {
            margin-top: 20px;
            font-size: 1rem;
            color: #555;
        }
    </style>
</head>

<body>
    <div class="qr-container">
        <div class="text-center">
            <h2 class="qr-title">Scan QR untuk Login</h2>
            <div class="qr-code">
                {!! $qrCode !!}
            </div>
            <p class="qr-subtitle">Arahkan kamera ke QR untuk login menggunakan Google.</p>

            <p><small><a href="{{ route('login') }}"><i class="fa fa-arrow-left"></i> Kembali</a></small></p>
        </div>
    </div>
</body>

</html>