<!DOCTYPE html
	PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>order recieved</title>
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

        .info {
            margin-bottom: 20px;
        }

        .info p {
            padding-left: 10px;
        }


        table,
        tbody {
            vertical-align: top;
            overflow: visible;
        }
	</style>
</head>
<body>

<div style=";text-indent: 0pt;text-align: left; padding-top: 15pt; padding-bottom: 10pt">
	<h2>RECEIPT</h2>
</div>

<div class="info">
	<p>Date of payment: 2024-03-19</p>
	<p style="padding-top: 5pt;padding-left: 10pt;text-indent: 0pt;text-align: left;">Quantity: {{$optionals->quantity}}</p>
	<p style="padding-top: 5pt;padding-left: 10pt;text-indent: 0pt;text-align: left;">Amount Received: {{36 * $optionals->quantity}}
		€</p>
	<p style="padding-top: 5pt;padding-left: 10pt;text-indent: 0pt;text-align: left;">Paid by: {{$optionals->firstname}}
		{{$optionals->surname}}</p>
</div>

<div style=";text-indent: 0pt;text-align: left;">
	<div class="info">
		<h2>For payment of {{$product->productname}}</h2>
		<p style="padding-top: 5pt;padding-left: 10pt;text-indent: 0pt;text-align: left;">Location: Brännland Inn, Brännland 35,
			Umeå </p>
		<p style="padding-top: 5pt;padding-left: 10pt;text-indent: 0pt;text-align: left;">Date: Saturday the 15th of June, 17:00 -
			20:00.</p>
	</div>

	<div class="info">
		<p>Casual Attire. Free parking.</p>
	</div>

	<div class="info">
		<p>For more information please visit: <a href="https://vasterbottenbrevet.se/program">https://vasterbottenbrevet.se/program</a>
		</p>

	</div>


</body>
</html>



