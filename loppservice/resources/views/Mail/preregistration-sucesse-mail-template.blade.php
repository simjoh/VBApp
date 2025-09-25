<!DOCTYPE html
  PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>form</title>
  <style type="text/css">
    * {
      margin: 0;
      padding: 0;
      text-indent: 0;
    }

    h1 {
      color: black;
      font-family: Verdana, sans-serif;
      font-style: normal;
      font-weight: bold;
      text-decoration: none;
      font-size: 22pt;
    }

    h2 {
      color: black;
      font-family: Verdana, sans-serif;
      font-style: normal;
      font-weight: bold;
      text-decoration: none;
      font-size: 14pt;
    }

    .s1 {
      color: black;
      font-family: Verdana, sans-serif;
      font-style: normal;
      font-weight: normal;
      text-decoration: none;
      font-size: 9pt;
    }

    .s2 {
      color: black;
      font-family: Verdana, sans-serif;
      font-style: normal;
      font-weight: bold;
      text-decoration: none;
      font-size: 12pt;
    }

    .s3 {
      color: black;
      font-family: Verdana, sans-serif;
      font-style: normal;
      font-weight: normal;
      text-decoration: none;
      font-size: 12pt;
    }

    .s4 {
      color: black;
      font-family: Verdana, sans-serif;
      font-style: normal;
      font-weight: normal;
      text-decoration: underline;
      font-size: 12pt;
    }

    .s5 {
      color: black;
      font-family: Verdana, sans-serif;
      font-style: normal;
      font-weight: bold;
      text-decoration: none;
      font-size: 14pt;
    }

    .s6 {
      color: #111827;
      font-family: Verdana, sans-serif;
      font-style: normal;
      font-weight: normal;
      text-decoration: none;
      font-size: 9pt;
    }

    p {
      color: black;
      font-family: Verdana, sans-serif;
      font-style: normal;
      font-weight: normal;
      text-decoration: none;
      font-size: 14pt;
      margin: 0pt;
    }

    .s7 {
      color: #4B5563;
      font-family: Verdana, sans-serif;
      font-style: normal;
      font-weight: normal;
      text-decoration: none;
      font-size: 9pt;
    }

    .s8 {
      color: black;
      font-family: Verdana, sans-serif;
      font-style: normal;
      font-weight: normal;
      text-decoration: none;
      font-size: 9pt;
    }

    .s9 {
      color: black;
      font-family: Verdana, sans-serif;
      font-style: normal;
      font-weight: normal;
      text-decoration: none;
      font-size: 12pt;
    }

    .s10 {
      color: #00A2FF;
      font-family: Verdana, sans-serif;
      font-style: normal;
      font-weight: normal;
      text-decoration: underline;
      font-size: 14pt;
    }

    .s11 {
      color: #00A2FF;
      font-family: Verdana, sans-serif;
      font-style: normal;
      font-weight: normal;
      text-decoration: none;
      font-size: 14pt;
    }

    .s12 {
      color: black;
      font-family: "Helvetica Neue", sans-serif;
      font-style: normal;
      font-weight: normal;
      text-decoration: none;
      font-size: 20pt;
    }

    table,
    tbody {
      vertical-align: top;
      overflow: visible;
    }
  </style>
</head>

<body>
  <h1 style="padding-top: 4pt;padding-left: 10pt;text-indent: 0pt;text-align: left;">Midnight Sun Randonnée 2026</h1>
  <p style="text-indent: 0pt;text-align: left;"><br /></p>
  <h2 style="padding-left: 10pt;text-indent: 0pt;text-align: left;">Receipt for payment of reservation fee</h2>
  <p style="text-indent: 0pt;text-align: left;"><br /></p>
  <table style="border-collapse:collapse;margin-left:6.0245pt" cellspacing="0">
    <tr style="height:53pt">
      <td style="width:241pt" colspan="2" rowspan="4">
        <p style="text-indent: 0pt;text-align: left;"><br /></p>
      </td>
      <td style="width:227pt">
        <p class="s1" style="padding-top: 2pt;padding-left: 4pt;text-indent: 0pt;text-align: left;">Reference number:
        </p>
        <p style="text-indent: 0pt;text-align: left;"><br /></p>
        <p class="s2" style="padding-left: 4pt;text-indent: 0pt;text-align: left;">{{$registration->ref_nr}}</p>
      </td>
      <td style="width:14pt">
        <p style="text-indent: 0pt;text-align: left;"><br /></p>
      </td>
      <td style="width:227pt">
        <p class="s1" style="padding-left: 4pt;text-indent: 0pt;text-align: left;">Payment received:</p>
        <p style="text-indent: 0pt;text-align: left;"><br /></p>
        <p class="s2" style="padding-left: 8pt;text-indent: 0pt;text-align: left;">{{date('Y-m-d') }}</p>
      </td>
    </tr>
    <tr style="height:61pt">
      <td style="width:227pt">
        <p style="text-indent: 0pt;text-align: left;"><br /></p>
        <p class="s1" style="padding-left: 4pt;text-indent: 0pt;text-align: left;">Organiser:</p>
        <p style="text-indent: 0pt;text-align: left;"><br /></p>
        <p class="s3" style="padding-left: 4pt;text-indent: 0pt;text-align: left;">Randonneurs Laponia</p>
      </td>
      <td style="width:14pt">
        <p style="text-indent: 0pt;text-align: left;"><br /></p>
      </td>
      <td style="width:227pt">
        <p style="text-indent: 0pt;text-align: left;"><br /></p>
        <p class="s1" style="padding-left: 4pt;text-indent: 0pt;text-align: left;">Event:</p>
        <p style="text-indent: 0pt;text-align: left;"><br /></p>
        <p class="s3" style="padding-left: 4pt;text-indent: 0pt;text-align: left;">Midnight Sun Randonnée 2026</p>
      </td>
    </tr>
    <tr style="height:26pt">
      <td style="width:227pt">
        <p class="s1" style="padding-top: 8pt;padding-left: 4pt;text-indent: 0pt;text-align: left;">Email address:</p>
      </td>
      <td style="width:14pt">
        <p style="text-indent: 0pt;text-align: left;"><br /></p>
      </td>
      <td style="width:227pt">
        <p class="s1" style="padding-top: 8pt;padding-left: 4pt;text-indent: 0pt;text-align: left;">Website:</p>
      </td>
    </tr>
    <tr style="height:36pt">
      <td style="width:227pt">
        <p style="padding-top: 6pt;padding-left: 4pt;text-indent: 0pt;text-align: left;"><a
            href="mailto:info@midnightsunrandonnee.se" class="s4">info@midnightsunrandonnee.se</a></p>
      </td>
      <td style="width:14pt">
        <p style="text-indent: 0pt;text-align: left;"><br /></p>
      </td>
      <td style="width:227pt">
        <p style="padding-top: 6pt;padding-left: 4pt;text-indent: 0pt;text-align: left;"><a
            href="http://www.midnightsunrandonnee.se/" class="s4">www.midnightsunrandonnee.se</a></p>
      </td>
    </tr>
    <tr style="height:38pt">
      <td style="width:227pt">
        <p class="s5" style="padding-top: 15pt;padding-left: 4pt;text-indent: 0pt;text-align: left;">Registration
          details</p>
          <p style="text-indent:0pt;text-align:left"><br></p>
      </td>
      <td style="width:14pt">
        <p style="text-indent: 0pt;text-align: left;"><br /></p>
      </td>
      <td style="width:227pt">
        <p style="text-indent: 0pt;text-align: left;"><br /></p>
      </td>
      <td style="width:14pt">
        <p style="text-indent: 0pt;text-align: left;"><br /></p>
      </td>
      <td style="width:227pt">
        <p style="text-indent: 0pt;text-align: left;"><br /></p>
      </td>
    </tr>
    <tr style="height:24pt">
      <td style="width:227pt;border-bottom-style:solid;border-bottom-width:1pt">
        <p class="s1" style="padding-left:5pt;text-indent:0pt;text-align:left">Start number:</p>
      </td>
      <td style="width:14pt">
        <p style="text-indent: 0pt;text-align: left;"><br /></p>
      </td>
      <td style="width:227pt;border-bottom-style:solid;border-bottom-width:1pt">
        <p class="s1" style="padding-left:5pt;text-indent:0pt;text-align:left">First name:</p>
      </td>
      <td style="width:14pt">
        <p style="text-indent: 0pt;text-align: left;"><br /></p>
      </td>
      <td style="width:227pt;border-bottom-style:solid;border-bottom-width:1pt">
        <p class="s1" style="padding-left:5pt;text-indent:0pt;text-align:left">Last name:</p>
      </td>
    </tr>
    <tr style="height:22pt">
      <td
        style="width:227pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
        <p class="s3" style="padding-top: 5pt;padding-left: 4pt;text-indent: 0pt;text-align: left;">{{$registration->startnumber}}</p>
      </td>
      <td
        style="width:14pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
        <p style="text-indent: 0pt;text-align: left;"><br /></p>
      </td>
      <td
        style="width:227pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
        <p class="s3" style="padding-top: 5pt;padding-left: 4pt;text-indent: 0pt;text-align: left;">{{$person->firstname}}</p>
      </td>
      <td
        style="width:14pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
        <p style="text-indent: 0pt;text-align: left;"><br /></p>
      </td>
      <td
        style="width:227pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
        <p class="s3" style="padding-top: 5pt;padding-left: 4pt;text-indent: 0pt;text-align: left;">{{$person->surname}}</p>
      </td>
    </tr>
    <tr style="height:31pt">
      <td
        style="width:227pt;border-top-style:solid;border-top-width:1pt;border-bottom-style:solid;border-bottom-width:1pt">
        <p style="text-indent: 0pt;text-align: left;"><br /></p>
        <p class="s1" style="padding-left: 5pt;text-indent: 0pt;text-align: left;">Date of Birth:</p>
      </td>
      <td style="width:14pt">
        <p style="text-indent: 0pt;text-align: left;"><br /></p>
      </td>
      <td
        style="width:227pt;border-top-style:solid;border-top-width:1pt;border-bottom-style:solid;border-bottom-width:1pt">
        <p style="text-indent: 0pt;text-align: left;"><br /></p>
        <p class="s1" style="padding-left: 5pt;text-indent: 0pt;text-align: left;">Email address:</p>
      </td>
      <td style="width:14pt">
        <p style="text-indent: 0pt;text-align: left;"><br /></p>
      </td>
      <td
        style="width:227pt;border-top-style:solid;border-top-width:1pt;border-bottom-style:solid;border-bottom-width:1pt">
        <p style="text-indent: 0pt;text-align: left;"><br /></p>
        <p class="s1" style="padding-left: 5pt;text-indent: 0pt;text-align: left;">Mobile Telephone:</p>
      </td>
    </tr>
    <tr style="height:22pt">
      <td
        style="width:227pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
        <p class="s3" style="padding-top: 5pt;padding-left: 4pt;text-indent: 0pt;text-align: left;">{{$person->birthdate}}</p>
      </td>
      <td
        style="width:14pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
        <p style="text-indent: 0pt;text-align: left;"><br /></p>
      </td>
      <td
        style="width:227pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
        <p style="padding-top: 4pt;padding-left: 4pt;text-indent: 0pt;text-align: left;"><a
            href="mailto:floriank2@gmail.com" class="s4">{{$person->contactinformation->email}}</a></p>
      </td>
      <td
        style="width:14pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
        <p style="text-indent: 0pt;text-align: left;"><br /></p>
      </td>
      <td
        style="width:227pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
        <p class="s3" style="padding-top: 5pt;padding-left: 4pt;text-indent: 0pt;text-align: left;">{{$person->contactinformation->tel}}</p>
      </td>
    </tr>
    <tr style="height:31pt">
      <td
        style="width:227pt;border-top-style:solid;border-top-width:1pt;border-bottom-style:solid;border-bottom-width:1pt">
        <p style="text-indent: 0pt;text-align: left;"><br /></p>
        <p class="s1" style="padding-left: 5pt;text-indent: 0pt;text-align: left;">Street address:</p>
      </td>
      <td style="width:14pt">
        <p style="text-indent: 0pt;text-align: left;"><br /></p>
      </td>
      <td
        style="width:227pt;border-top-style:solid;border-top-width:1pt;border-bottom-style:solid;border-bottom-width:1pt">
        <p style="text-indent: 0pt;text-align: left;"><br /></p>
        <p class="s1" style="padding-left: 4pt;text-indent: 0pt;text-align: left;">Post code:</p>
      </td>
      <td style="width:14pt">
        <p style="text-indent: 0pt;text-align: left;"><br /></p>
      </td>
      <td
        style="width:227pt;border-top-style:solid;border-top-width:1pt;border-bottom-style:solid;border-bottom-width:1pt">
        <p style="text-indent: 0pt;text-align: left;"><br /></p>
        <p class="s1" style="padding-left: 5pt;text-indent: 0pt;text-align: left;">City:</p>
      </td>
    </tr>
    <tr style="height:22pt">
      <td
        style="width:227pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
        <p class="s3" style="padding-top: 5pt;padding-left: 4pt;text-indent: 0pt;text-align: left;">{{$person->adress->adress}}</p>
      </td>
      <td
        style="width:14pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
        <p style="text-indent: 0pt;text-align: left;"><br /></p>
      </td>
      <td
        style="width:227pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
        <p class="s3" style="padding-top: 5pt;padding-left: 4pt;text-indent: 0pt;text-align: left;">{{$person->adress->postal_code}}</p>
      </td>
      <td
        style="width:14pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
        <p style="text-indent: 0pt;text-align: left;"><br /></p>
      </td>
      <td
        style="width:227pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
        <p class="s3" style="padding-top: 5pt;padding-left: 4pt;text-indent: 0pt;text-align: left;">{{$person->adress->city}}</p>
      </td>
    </tr>
    <tr style="height:31pt">
      <td
        style="width:227pt;border-top-style:solid;border-top-width:1pt;border-bottom-style:solid;border-bottom-width:1pt">
        <p style="text-indent: 0pt;text-align: left;"><br /></p>
        <p class="s1" style="padding-left: 5pt;text-indent: 0pt;text-align: left;">Country</p>
      </td>
      <td style="width:14pt">
        <p style="text-indent: 0pt;text-align: left;"><br /></p>
      </td>
      <td style="width:227pt;border-top-style:solid;border-top-width:1pt">
        <p style="text-indent: 0pt;text-align: left;"><br /></p>
      </td>
      <td style="width:14pt">
        <p style="text-indent: 0pt;text-align: left;"><br /></p>
      </td>
      <td style="width:227pt;border-top-style:solid;border-top-width:1pt">
        <p style="text-indent: 0pt;text-align: left;"><br /></p>
      </td>
    </tr>
    <tr style="height:22pt">
      <td
        style="width:227pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
        <p class="s3" style="padding-top: 5pt;padding-left: 4pt;text-indent: 0pt;text-align: left;">{{$country}}</p>
      </td>
      <td style="width:14pt;border-left-style:solid;border-left-width:1pt">
        <p style="text-indent: 0pt;text-align: left;"><br /></p>
      </td>
      <td style="width:227pt">
        <p style="text-indent: 0pt;text-align: left;"><br /></p>
      </td>
      <td style="width:14pt">
        <p style="text-indent: 0pt;text-align: left;"><br /></p>
      </td>
      <td style="width:227pt">
        <p style="text-indent: 0pt;text-align: left;"><br /></p>
      </td>
    </tr>
  </table>
  <table cellspacing="0" style="border-collapse:collapse;margin-left:6.0245pt">
    <tr style="height:24pt">
      <td style="width:227pt;border-bottom-style:solid;border-bottom-width:1pt">
      <p style="text-indent: 0pt;text-align: left;"><br /></p>
        <p class="s1" style="padding-top: 5pt;padding-left: 4pt;text-indent: 0pt;text-align: left;">Special dietary
          requirements:</p>
      </td>
      <td style="width:14pt">
        <p style="text-indent: 0pt;text-align: left;"><br />
        </p>
      </td>
      <td style="width:227pt;border-bottom-style:solid;border-bottom-width:1pt">
        <p class="s1" style="padding-top: 5pt;padding-left: 4pt;text-indent: 0pt;text-align: left;">&nbsp;</p>
      </td>
    </tr>
    <tr style="height:22pt">
      <td colspan="3"
        style="width:227pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
        <p class="s3" style="padding-top: 5pt;padding-left: 4pt;text-indent: 0pt;text-align: left;">{{$registration->additional_information}}</p>
        <p style="text-indent: 0pt;text-align: left;"><br />
        </p>
        <p class="s3" style="padding-top: 5pt;padding-left: 4pt;text-indent: 0pt;text-align: left;">&nbsp;</p>
      </td>
    </tr>
  </table>
  <p>&nbsp;</p>

  <p style="text-indent: 0pt;text-align: left;">&nbsp;</p>
  <p class="s9" style="padding-left: 10pt;text-indent: 0pt;text-align: left;">&nbsp;</p>
  <p class="s9" style="padding-left: 10pt;text-indent: 0pt;text-align: left;">&nbsp;</p>
  <p style="text-indent: 0pt;text-align: left;"><br /></p>
  <h2 style="padding-left: 10pt;text-indent: 0pt;text-align: left;">Further information</h2>
  <p class="s10" style="padding-top: 8pt;padding-left: 10pt;text-indent: 0pt;text-align: left;"><a href="{{$completeregistrationlink}}">Follow this link to complete your registration</a><br></p>
  <p class="s10" style="padding-top: 5pt;padding-left: 10pt;text-indent: 0pt;text-align: left;"><a href="https://cycling.lachemise.se/collections/midnight-sun">Webshop jersey</a></p>
  <p class="s10" style="padding-top: 5pt;padding-left: 10pt;text-indent: 0pt;text-align: left;"><a href="{{$startlistlink}}">Start list</a></p>
  <p class="s10" style="padding-top: 8pt;padding-left: 10pt;text-indent: 0pt;text-align: left;"><a href="{{$updatelink}}">Follow this link to change your registered information </a><br></p>
  <p style="text-indent: 0pt;text-align: left;"><br /></p>
</body>

</html>
