<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $subject ?? 'Document' }}</title>

    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
            color: #111;
            line-height: 1.6;
        }

        .container {
            max-width: 640px;
            margin: 0 auto;
        }

        .header {
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .company {
            font-size: 18px;
            font-weight: bold;
        }

        .content {
            margin-top: 20px;
        }

        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #555;
        }
    </style>
</head>
<body>

<div class="container">

    <div class="header">
        <div class="company">FoamVillage</div>
    </div>

    <div class="content">
        <p>
            {{ $bodyText }}
        </p>

        <p>
            If you have any questions, please feel free to contact us.
        </p>
    </div>

    <div class="footer">
        <p>
            Kind regards,<br>
            <strong>FoamVillage Team</strong>
        </p>
    </div>

</div>

</body>
</html>
