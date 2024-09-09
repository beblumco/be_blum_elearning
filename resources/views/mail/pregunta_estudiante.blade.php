<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Registro Exitoso</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style type="text/css">
        /**
   * Google webfonts. Recommended to include the .woff version for cross-client compatibility.
   */
        @media screen {
            @font-face {
                font-family: 'Source Sans Pro';
                font-style: normal;
                font-weight: 400;
                src: local('Source Sans Pro Regular'), local('SourceSansPro-Regular'), url(https://fonts.gstatic.com/s/sourcesanspro/v10/ODelI1aHBYDBqgeIAH2zlBM0YzuT7MdOe03otPbuUS0.woff) format('woff');
            }

            @font-face {
                font-family: 'Source Sans Pro';
                font-style: normal;
                font-weight: 700;
                src: local('Source Sans Pro Bold'), local('SourceSansPro-Bold'), url(https://fonts.gstatic.com/s/sourcesanspro/v10/toadOcfmlt9b38dHJxOBGFkQc6VGVFSmCnC_l7QZG60.woff) format('woff');
            }
        }

        /**
   * Avoid browser level font resizing.
   * 1. Windows Mobile
   * 2. iOS / OSX
   */
        body,
        table,
        td,
        a {
            -ms-text-size-adjust: 100%;
            /* 1 */
            -webkit-text-size-adjust: 100%;
            /* 2 */
        }

        /**
   * Remove extra space added to tables and cells in Outlook.
   */
        table,
        td {
            mso-table-rspace: 0pt;
            mso-table-lspace: 0pt;
        }

        /**
   * Better fluid images in Internet Explorer.
   */
        img {
            -ms-interpolation-mode: bicubic;
        }

        /**
   * Remove blue links for iOS devices.
   */
        a[x-apple-data-detectors] {
            font-family: inherit !important;
            font-size: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
            color: inherit !important;
            text-decoration: none !important;
        }

        /**
   * Fix centering issues in Android 4.4.
   */
        div[style*="margin: 16px 0;"] {
            margin: 0 !important;
        }

        body {
            width: 100% !important;
            height: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        /**
   * Collapse table borders to avoid space between cells.
   */
        table {
            border-collapse: collapse !important;
        }

        a {
            color: #1a82e2;
        }

        img {
            height: auto;
            line-height: 100%;
            text-decoration: none;
            border: 0;
            outline: none;
        }
    </style>

</head>

<body style="background-color: #e9ecef;">

    <div align=center>
        <table class=MsoNormalTable border=0 cellspacing=0 cellpadding=0
            style='max-width:450.0pt;border-collapse:collapse'>
            <tr>
                <td style='border:none;border-top:solid #D4DADF 2.25pt;background:white;padding:27.0pt 18.0pt 0cm 18.0pt'>
                    <p class=MsoNormal align=center style='text-align:center'>
                        <span style='font-family:"Source Sans Pro",sans-serif;color:black'>
                            <a href="https://klaxen.co/savk/" target="_blank">
                                <span style='text-decoration:none'>
                                    <img border=0 width=124 height=70 style='width:2.2916in;'
                                        id="Imagen_x0020_6"
                                        src="{{ENV("APP_URL").}}/img/logo_principal_primary.png">
                                </span>
                            </a>
                        </span>
                    </p>

                    <p class=MsoNormal align=center style='text-align:center'>
                        <o:p>&nbsp;</o:p>
                    </p>
                </td>
            </tr>

            <tr>
                <td style='padding:18.0pt 18.0pt 0pt 18.0pt;background:white'>
                    <h3 style='text-align:justify;line-height:18.0pt'><span
                            style='font-size:19.0pt;font-family:Roboto;color:black'> Hola,</span><span style='font-size:12.0pt;font-family:Roboto'>
                            <o:p></o:p>
                        </span></h3>
                </td>
            </tr>
            <tr>
                <td align="left" bgcolor="#ffffff"
                    style="padding: 24px; font-family: 'Source Sans Pro', Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px;">
                    <p style="margin: 0;color:black">
                        El usuario {{ $estudiante }} ha generado una pregunta sobre la capacitación {{ $capacitacion }}.
                    </p>
                </td>
            </tr>
            <tr>
                <td align="left" bgcolor="#ffffff"
                    style="padding: 24px; font-family: 'Source Sans Pro', Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px;">
                    <p style="margin: 0;color:black">
                        Por favor ingresa y realiza la debida respuesta.
                    </p>
                </td>
            </tr>
            <tr>
                <td align="center" bgcolor="#ffffff" style="padding: 12px;">
                    <table border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <td align="center" bgcolor="#ff7f00" style="border-radius: 6px;">
                                <a href="{{ env('URL') }}" target="_blank"
                                    style="display: inline-block; padding: 16px 36px; font-family: 'Source Sans Pro', Helvetica, Arial, sans-serif; font-size: 16px; color: #ffffff; text-decoration: none; border-radius: 6px;">Savk</a>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
