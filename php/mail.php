<?php

function sendMail($to,$subject,$content){
  $headers = "From: " .'"Eenmaal Andermaal" <noreply@iproject2.icasites.nl>'. "\r\n";
  $headers .= "Content-Type: text/html;\r\n";
  $message = '
  <html>
  <body style="margin: 0; padding: 0;">
      <table border="0" cellpadding="0" cellspacing="0" width="100%">
          <tr>
              <td style="padding: 10px 0 20px 0;">
                  <table align="center" cellpadding="0" cellspacing="0" width="600" style="border: 1px solid #cccccc;">
                      <tr>
                          <td align="center" bgcolor="#5484a4" style="padding: 30px 0 20px 0;">

                              <h1 style="font-family: '.'Varela Round'.', sans-serif; color:#FFFFFF;">Eenmaal Andermaal</h1>
                              <img src="http://iproject2.icasites.nl/images/logo.png" alt="Eenmaal Andermaal" width="230" height="230" style="display: block;"/>
                          </td>
                      </tr>
                      <tr>
                          <td align="center" bgcolor="#FFFFFF" style="padding: 20px 30px 20px 30px;">
                              '.$content.'
                          </td>
                      </tr>
                      <tr>
                          <td align="center" bgcolor="#5484a4" style="padding: 20px 30px 20px 30px;">
                              <table border="0" cellpadding="0" cellspacing="0" width="100%" style="font-family: '.'Varela Round'.', sans-serif; color:#DFDFDF;">
                                  <tr>
                                      <td width="166" valign="top">
                                          <h3>Start hier</h3>
                                          <p style="font-size: 14px"><a style="text-decoration: none; color:#DFDFDF" href="http://iproject2.icasites.nl">Home</a></p>
                                          <p style="font-size: 14px"><a style="text-decoration: none; color:#DFDFDF" href="http://iproject2.icasites.nl/login.php">Inloggen</a></p>
                                      </td>
                                      <td style="font-size: 0; line-height: 0;" width="21">
                                          &nbsp;
                                      </td>
                                      <td width="166" valign="top">
                                          <h3>Over ons</h3>
                                          <p style="font-size: 14px"><a style="text-decoration: none; color:#DFDFDF" href="http://iproject2.icasites.nl">Bedrijfsinformatie</a></p>
                                          <p style="font-size: 14px"><a style="text-decoration: none; color:#DFDFDF" href="http://iproject2.icasites.nl/pdf/voorwaarden.pdf">Algemene voorwaarden</a></p>
                                      </td>
                                      <td style="font-size: 0; line-height: 0;" width="21">
                                          &nbsp;
                                      </td>
                                      <td width="166" valign="top">
                                          <h3>Support</h3>
                                          <p style="font-size: 14px"><a style="text-decoration: none; color:#DFDFDF" href="http://iproject2.icasites.nl">Veelgestelde vragen</a></p>
                                          <p style="font-size: 14px"><a style="text-decoration: none; color:#DFDFDF" href="http://iproject2.icasites.nl">Contact</a></p>
                                      </td>
                                  </tr>
                              </table>
                          </td>
                      </tr>
                  </table>
              </td>
          </tr>
      </table>
  </body>
  </html>';

  mail($to,$subject,$message,$headers);

}
?>
