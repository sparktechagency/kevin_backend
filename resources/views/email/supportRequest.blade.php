<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Support Request</title>
<style>
  body {
    font-family: 'Helvetica Neue', Arial, sans-serif;
    background-color: #f8fafc;
    margin: 0;
    padding: 0;
  }
  .email-container {
    max-width: 600px;
    margin: 30px auto;
    background-color: #ffffff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 10px rgba(0,0,0,0.05);
  }
  .header {
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    color: #ffffff;
    padding: 25px 20px;
    text-align: center;
  }
  .header h1 {
    font-size: 22px;
    margin: 0;
    letter-spacing: 0.5px;
  }
  .content {
    padding: 30px 25px;
    color: #374151;
  }
  .content h2 {
    font-size: 18px;
    margin-bottom: 15px;
    color: #111827;
  }
  .content p {
    line-height: 1.6;
    margin: 10px 0;
  }
  .info-box {
    background-color: #f3f4f6;
    border-radius: 8px;
    padding: 15px;
    margin-top: 20px;
  }
  .info-box strong {
    display: inline-block;
    width: 90px;
  }
  .footer {
    background-color: #f9fafb;
    text-align: center;
    padding: 20px;
    font-size: 13px;
    color: #6b7280;
  }
  @media (max-width: 600px) {
    .content {
      padding: 20px 15px;
    }
    .header h1 {
      font-size: 20px;
    }
  }
</style>
</head>
<body>
  <div class="email-container">
    <div class="header">
      <h1>Support Request Received</h1>
    </div>
    <div class="content">
      <h2>Hello Admin 👋</h2>
      <p>You have received a new support request from the app.</p>

      <div class="info-box">
        <p><strong>Name:</strong> {{ $data['name'] ?? 'N/A' }}</p>
        <p><strong>Email:</strong> {{ $data['email'] ?? 'N/A' }}</p>
        <p><strong>Contact Number:</strong> {{ $data['contact_number'] ?? 'N/A' }}</p>
        <p><strong>Employee Pin:</strong> {{ $data['employee_pin'] ?? 'N/A' }}</p>
      </div>

      <p style="margin-top:20px;"><strong>Message:</strong></p>
      <p>{{ $data['content'] ?? 'No message provided.' }}</p>
    </div>

    <div class="footer">
      <p>© {{ date('Y') }} <strong>{{ config('app.name') }}</strong>. All rights reserved.</p>
    </div>
  </div>
</body>
</html>
