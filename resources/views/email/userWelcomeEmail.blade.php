<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Welcome to Our Team</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f7f7f7; padding: 20px;">

    <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: auto; background: #ffffff; border-radius: 8px; overflow: hidden;">
        <tr>
            <td style="background: #4a90e2; padding: 20px; text-align: center; color: #ffffff;">
                <h1 style="margin: 0; font-size: 24px;">Welcome to the Team!</h1>
            </td>
        </tr>

        <tr>
            <td style="padding: 25px; color: #333333;">
                <p style="font-size: 16px; margin-bottom: 20px;">
                    Hi <strong>{{name}}</strong>,
                </p>

                <p style="font-size: 15px; margin-bottom: 20px;">
                    We’re happy to inform you that your account has been successfully created.
                    Below are your login details:
                </p>

                <table cellpadding="6" cellspacing="0" width="100%" style="background: #f1f1f1; border-radius: 5px; margin-bottom: 20px;">
                    <tr>
                        <td><strong>Email:</strong></td>
                        <td>{{$data->user->name}}</td>
                    </tr>
                    <tr>
                        <td><strong>Employee PIN:</strong></td>
                        <td>{{$data->user->employee_pin}}</td>
                    </tr>

                </table>

                <p style="font-size: 15px;">
                    For security reasons, please change your password after your first login.
                </p>

                <p style="margin-top: 30px; text-align: center;">
                    <a href="{{login_url}}"
                       style="background: #4a90e2; color: #ffffff; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-size: 16px;">
                        Login to Dashboard
                    </a>
                </p>

                <p style="font-size: 14px; color: #777777; margin-top: 30px;">
                    If you need any help, feel free to reach out to us.
                </p>

                <p style="font-size: 14px; color: #555555;">
                    Thanks,<br>
                    <strong>{{$data->manager->company_name}}</strong>
                </p>
            </td>
        </tr>
    </table>

</body>
</html>
