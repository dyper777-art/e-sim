<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>KHQR QR Code</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    </head>
    <body>
        <div class="container text-center mt-5">
            <h2>KHQR QR Code</h2>


            @if($qrUrl)
                <img src="{{ $qrUrl }}" alt="KHQR QR Code" class="img-fluid">
            @else
                <p class="text-danger">Failed to generate QR code.</p>
    @endif
        </div>
    </body>
    </html>
