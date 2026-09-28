<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>New enquiry — Get in Touch</title>
</head>
{{--
  Email HTML: tables + inline styles only. Mail clients (Outlook especially)
  ignore <style> blocks, flexbox and grid, so everything is laid out with
  nested tables and every rule is inlined.
--}}
<body style="margin:0;padding:0;background:#f6f7f9;">
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f6f7f9;padding:24px 12px;">
    <tr>
      <td align="center">
        <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="width:600px;max-width:100%;background:#ffffff;border:1px solid #e9eaee;border-radius:14px;overflow:hidden;font-family:'Segoe UI',Arial,Helvetica,sans-serif;">

          <!-- header -->
          <tr>
            <td style="background:#f5a623;padding:20px 26px;">
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                  <td style="font-size:18px;font-weight:bold;color:#ffffff;letter-spacing:-.2px;">New Website Enquiry</td>
                  <td align="right" style="font-size:12px;color:#fff6e6;">Get in Touch</td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- intro -->
          <tr>
            <td style="padding:22px 26px 6px;">
              <p style="margin:0;font-size:14.5px;color:#475467;line-height:1.6;">
                <strong style="color:#101828;">{{ $name }}</strong> sent a message from the Get in Touch page.
                Just hit reply — your response goes straight back to them.
              </p>
            </td>
          </tr>

          <!-- details -->
          <tr>
            <td style="padding:16px 26px 4px;">
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border:1px solid #eceef1;border-radius:10px;">
                <tr>
                  <td width="120" style="padding:11px 14px;font-size:12.5px;color:#8a93a3;border-bottom:1px solid #f1f2f4;">Name</td>
                  <td style="padding:11px 14px;font-size:13.5px;color:#101828;font-weight:600;border-bottom:1px solid #f1f2f4;">{{ $name }}</td>
                </tr>
                <tr>
                  <td style="padding:11px 14px;font-size:12.5px;color:#8a93a3;border-bottom:1px solid #f1f2f4;">Email</td>
                  <td style="padding:11px 14px;font-size:13.5px;border-bottom:1px solid #f1f2f4;">
                    <a href="mailto:{{ $email }}" style="color:#d99017;text-decoration:none;font-weight:600;">{{ $email }}</a>
                  </td>
                </tr>
                <tr>
                  <td style="padding:11px 14px;font-size:12.5px;color:#8a93a3;{{ !empty($account) ? 'border-bottom:1px solid #f1f2f4;' : '' }}">Subject</td>
                  <td style="padding:11px 14px;font-size:13.5px;color:#101828;font-weight:600;{{ !empty($account) ? 'border-bottom:1px solid #f1f2f4;' : '' }}">{{ $subject ?: 'General enquiry' }}</td>
                </tr>
                @if(!empty($account))
                  <tr>
                    <td style="padding:11px 14px;font-size:12.5px;color:#8a93a3;">Account</td>
                    <td style="padding:11px 14px;font-size:13px;color:#667085;">{{ $account }}</td>
                  </tr>
                @endif
              </table>
            </td>
          </tr>

          <!-- message -->
          <tr>
            <td style="padding:16px 26px 4px;">
              <div style="font-size:12.5px;color:#8a93a3;margin-bottom:7px;">Message</div>
              <div style="background:#fffaf1;border-left:3px solid #f5a623;border-radius:8px;padding:14px 16px;font-size:14px;color:#344054;line-height:1.65;white-space:pre-line;">{{ $body }}</div>
            </td>
          </tr>

          <!-- reply button -->
          <tr>
            <td style="padding:20px 26px 24px;">
              <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                <tr>
                  <td style="background:#f5a623;border-radius:9px;">
                    <a href="mailto:{{ $email }}?subject={{ rawurlencode('Re: ' . ($subject ?: 'Your enquiry')) }}"
                       style="display:inline-block;padding:11px 22px;font-size:14px;font-weight:bold;color:#ffffff;text-decoration:none;">
                      Reply to {{ $name }}
                    </a>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- footer -->
          <tr>
            <td style="background:#fafbfc;border-top:1px solid #f1f2f4;padding:14px 26px;font-size:11.5px;color:#98a2b3;">
              Sent automatically from the Get in Touch form on the KW Learning site.
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>
