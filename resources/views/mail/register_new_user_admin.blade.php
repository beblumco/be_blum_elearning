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

        <!-- start preheader -->
        <div class="preheader"
            style="display: none; max-width: 0; max-height: 0; overflow: hidden; font-size: 1px; line-height: 1px; color: #fff; opacity: 0;">
            A preheader is the short summary text that follows the subject line when an email is viewed in the inbox.
        </div>
        <!-- end preheader -->

        <!-- start body -->
        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-top: 15px;">

            <!-- start logo -->
            <tr>
                <td align="center" bgcolor="#e9ecef">
                    <!--[if (gte mso 9)|(IE)]>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="600">
<tr>
<td align="center" valign="top" width="600">
<![endif]-->

                    <!--[if (gte mso 9)|(IE)]>
</td>
</tr>
</table>
<![endif]-->
                </td>
            </tr>
            <!-- end logo -->

            <!-- start hero -->
            <tr>
                <td align="center" bgcolor="#e9ecef">


                    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
                        <tr>
                            <td align="center" bgcolor="#ffffff"
                                style="padding: 36px 24px 0; font-family: 'Source Sans Pro', Helvetica, Arial, sans-serif; border-top: 3px solid #d4dadf;">
                                <a href="https://klaxen.co/savk/" target="_blank"
                                    style="display: inline-flex; text-decoration: none; max-height: 180px;
                                    align-items: center;">
                                    <img src="{{ENV('APP_URL').'/img/logo_principal_primary.png'}}" alt="BeBlum"
                                        border="0" width="120"
                                        style="display: block; width: 270px; padding: 2px;">
                                </a>
                            </td>
                        </tr>

                        <tr>
                            <td style='padding:18.0pt 18.0pt 0pt 18.0pt;background:white'>
                                <h3 style='text-align:justify;line-height:18.0pt'><span
                                        style='font-size:19.0pt;font-family:Roboto;color:black'>Hola Administrador,</span><span style='font-size:12.0pt;font-family:Roboto'>
                                        <o:p></o:p>
                                    </span></h3>
                            </td>
                        </tr>

                        <tr>
                            <td align="left" bgcolor="#ffffff"
                                style="padding: 24px; font-family: 'Source Sans Pro', Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px;">
                                <p style="margin: 0;">
                                    Se ha registrado <b>{{ $fullname }}</b> de la empresa <b>{{ $company }}</b> con cargo de <b>{{ $job }}</b> y su cuenta está pendiente por activar, puedes activarla dando click en el botón que aparece a continuación.
                                </p>
                            </td>
                        </tr>

                        <tr>
                            <td align="center" bgcolor="#ffffff" style="padding: 12px;">
                                <table border="0" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td align="center" bgcolor="#ff7f00" style="border-radius: 6px;">
                                            <a href="{{ $url }}" target="_blank"
                                                style="display: inline-block; padding: 16px 36px; font-family: 'Source Sans Pro', Helvetica, Arial, sans-serif; font-size: 16px; color: #ffffff; text-decoration: none; border-radius: 6px;">Activar cuenta</a>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                    <!--[if (gte mso 9)|(IE)]>
</td>
</tr>
</table>
<![endif]-->
                </td>
            </tr>
            <!-- end hero -->




        </table>
        <!-- end body -->

    </body>

</html>
