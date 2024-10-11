<!DOCTYPE html
	PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>Order recieved</title>
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
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tbody>
		<tr>
			<td class="s3"><strong>DINNER BUFFET</strong></td>
		</tr>
		<tr>
			<td class="s3">Address: Brännland Inn, Brännland 35,
				Umeå
			</td>
		</tr>
		<tr>
			<td class="s3">Date/Time: Saturday 14th June, 17:00 - 20:00</td>
		</tr>
		<tr>
			<td class="s3">Casual Attire. Free parking.</td>
		</tr>
		<tr>
			<td class="s3">Hosted by: Randonneurs Laponia</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td class="s3">For payment of: {{$product-&gt;productname}}</td>
		</tr>
		<tr>
			<td class="s3">Date of payment:</td>
		</tr>
		<tr>
			<td class="s3">Quantity: {{$optionals-&gt;quantity}}</td>
		</tr>
		<tr>
			<td class="s3">Amount Received: {{38 * $optionals-&gt;quantity}}
				€
			</td>
		</tr>
		<tr>
			<td class="s3">Paid by: {{$optionals-&gt;firstname}}
				{{$optionals-&gt;surname}}
			</td>
		</tr>
		<tr>
			<td><p>&nbsp;</p>

				<h4><span class="s3"><strong>MENU</strong></span></h4>
				<span class="s3">Roast beef<br/>
            BBQ-grilled Carré sausage with Västerbotten cheese <br/>
            Västerbotten pie<br/>
            Pasta salad with sun-dried tomato and olives<br/>
            Creamy potato salad<br/>
            Mixed green salad <br/>
            Coleslaw<br/>
            Tomato salad<br/>
            Marinated vegetables <br/>
            Bearnaise sauce and BBQ sauce <br/>
            Bread and butter<br/>
            Cookies<br/>
            Coffee/Tea</span><br/>
			</td>
		</tr>
		<tr>
			<td class="s3">&nbsp;</td>
		</tr>
		<tr>
			<td class="s3">Brännland Inn: info@brannlandswardshus.se | +46 (0)90-301 30</td>
		</tr>
		<tr>
			<td class="s3">For more information please visit: https://midnightsunrandonnee.se/program/#dinner</td>
		</tr>
		</tbody>
	</table>
</div>

<div style=";text-indent: 0pt;text-align: left;">
</body>
</html>
