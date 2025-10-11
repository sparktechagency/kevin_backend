<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your OTP Code</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
        }

        .email-container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .email-header {
            background-color: #4CAF50;
            color: #ffffff;
            padding: 20px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
        }

        .email-body {
            padding: 30px;
            text-align: center;
            font-size: 18px;
            color: #333333;
        }

        .otp-code {
            font-size: 32px;
            font-weight: bold;
            color: #4CAF50;
            padding: 10px;
            background-color: #f0f9f3;
            border-radius: 6px;
            margin: 20px 0;
        }

        .email-footer {
            background-color: #f7f7f7;
            color: #777777;
            text-align: center;
            padding: 20px;
            font-size: 14px;
        }

        .email-footer a {
            color: #4CAF50;
            text-decoration: none;
        }

        @media only screen and (max-width: 600px) {
            .email-container {
                width: 100%;
                padding: 10px;
            }

            .email-header {
                font-size: 20px;
            }

            .email-body {
                font-size: 16px;
            }

            .otp-code {
                font-size: 28px;
            }
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="email-header">
            OTP Verification
        </div>
        <div class="email-body">
            <p>Hello {{ $data['name'] ?? '' }} ,</p>
            <p>To complete your verification, use the OTP code below. This code is valid for 10 minute.</p>
            <div class="otp-code">{{ $data['otp']}}</div>
            <p>If you did not request this, please ignore this email.</p>
        </div>
        <div class="email-footer">
            <p>Thank you for choosing us!</p>
            <p><a href="https://www.example.com">Visit our website</a></p>
        </div>
    </div>
</body>

</html>
