<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Your Hyper Adz Login OTP</title>
    <style>
        body {
            font-family: 'Outfit', 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #f8fafc;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }
        .container {
            max-width: 500px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
            border: 1px solid #f1f5f9;
        }
        .header {
            background: linear-gradient(135deg, #1e40af, #1155cc);
            padding: 35px 20px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            font-size: 20px;
            margin: 0;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .body {
            padding: 40px 35px;
            text-align: center;
        }
        .body p {
            color: #475569;
            font-size: 13px;
            line-height: 1.6;
            margin: 0 0 25px 0;
        }
        .otp-box {
            display: inline-block;
            background-color: #f1f5f9;
            border: 1px dashed #cbd5e1;
            padding: 15px 35px;
            border-radius: 16px;
            margin: 10px 0 30px 0;
        }
        .otp-code {
            font-size: 32px;
            font-weight: 800;
            color: #1155cc;
            letter-spacing: 6px;
            font-family: 'Courier New', Courier, monospace;
        }
        .footer {
            background-color: #f8fafc;
            padding: 20px;
            text-align: center;
            border-t: 1px solid #f1f5f9;
        }
        .footer p {
            color: #94a3b8;
            font-size: 11px;
            margin: 0;
        }
        .warning {
            color: #ef4444;
            font-size: 11px;
            font-weight: 600;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Hyper Adz</h1>
        </div>
        <div class="body">
            <p>Hello,</p>
            <p>You requested a secure verification code to access your Hyper Adz partner portal. Use the one-time passcode (OTP) below to authorize this session:</p>
            
            <div class="otp-box">
                <div class="otp-code">{{ $otpCode }}</div>
            </div>

            <p class="warning">Valid for 10 minutes only. Do not share this code with anyone.</p>
            <p>If you did not request this code, please ignore this email or contact support.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Hyper Adz. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
