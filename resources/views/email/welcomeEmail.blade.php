<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Our Service</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }

        .email-container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header img {
            width: 120px;
            margin-bottom: 20px;
        }

        h1 {
            color: #333333;
            font-size: 28px;
            margin-bottom: 10px;
        }

        p {
            font-size: 16px;
            color: #555555;
            line-height: 1.5;
            margin-bottom: 20px;
        }

        .cta-button {
            display: inline-block;
            background-color: #4CAF50;
            color: #ffffff;
            padding: 12px 24px;
            font-size: 16px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            text-align: center;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #777777;
        }

        .footer a {
            color: #4CAF50;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header with company logo -->
        <div class="header">
            <img src="{{ $data['company_logo'] ?? 'https://via.placeholder.com/120' }}" alt="Company Logo">
        </div>
        <!-- Welcome message -->
        <h1>Welcome to {{ $data['company_name'] }}, {{ $data['manager_full_name'] }}!</h1>
        <p>Thank you for signing up with {{ $data['company_name'] }}. We are thrilled to have you with us. To get started, please follow the link below to log in to your account and explore our features:</p>
        <!-- Call to action button -->
        {{-- <a href="#" class="cta-button">Go to Dashboard</a> --}}
        <!-- Footer -->
        <div class="footer">
            <p>If you have any questions, feel free to reach out to our support team at <a href="mailto:support@example.com">support@example.com</a>.</p>
            <p>&copy; {{ date('Y') }} {{ $data['company_name'] }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
