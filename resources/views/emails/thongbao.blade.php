<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>{{ $tieuDe }}</title>
</head>
<body style="margin:0;padding:24px;background:#f3eefc;font-family:'Segoe UI',Roboto,Arial,sans-serif;color:#2b2440;">
    <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" role="presentation" style="max-width:600px;width:100%;background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 8px 24px rgba(90,60,140,.08);">
                    <tr>
                        <td style="background:linear-gradient(135deg,#1b1233,#7c3aed);padding:24px 28px;color:#ffffff;">
                            <div style="font-size:18px;font-weight:800;letter-spacing:.3px;">HappyTime QLNS</div>
                            <div style="font-size:13px;opacity:.85;margin-top:4px;">Hệ thống Quản lý Nhân sự &amp; Phân ca</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px;">
                            <h2 style="margin:0 0 12px;font-size:18px;color:#1b1233;">Xin chào {{ $nguoiNhan }},</h2>
                            <p style="margin:0 0 18px;font-size:14px;line-height:1.6;color:#4a435f;">{{ $noiDungChinh }}</p>

                            @if(!empty($chiTiet))
                                <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;margin-bottom:20px;background:#faf8ff;border-radius:10px;overflow:hidden;">
                                    @foreach($chiTiet as $label => $value)
                                        <tr>
                                            <td style="padding:10px 14px;border-bottom:1px solid #ede7f8;font-size:13px;color:#7c7394;width:38%;font-weight:600;">{{ $label }}</td>
                                            <td style="padding:10px 14px;border-bottom:1px solid #ede7f8;font-size:13px;color:#2b2440;font-weight:700;">{{ $value }}</td>
                                        </tr>
                                    @endforeach
                                </table>
                            @endif

                            @if($actionUrl && $actionText)
                                <p style="text-align:center;margin:24px 0 8px;">
                                    <a href="{{ $actionUrl }}" style="display:inline-block;background:#7c3aed;color:#ffffff;text-decoration:none;font-weight:700;font-size:14px;padding:12px 24px;border-radius:999px;">
                                        {{ $actionText }}
                                    </a>
                                </p>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#faf8ff;padding:16px 28px;text-align:center;font-size:12px;color:#8b8499;">
                            © {{ date('Y') }} HappyTime QLNS. Email được gửi tự động từ hệ thống.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
