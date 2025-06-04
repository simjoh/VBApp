<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
	<!--<h1 style="padding-top: 4pt;padding-left: 4pt;text-indent: 0pt;text-align: left;">Super Randonné Series</h1>-->
	<p style="text-indent: 0pt;text-align: left;">
		<br />
	</p>
	<h2 style="padding-left: 5pt;text-indent: 0pt;text-align: left;">Bekräftelse på anmälan</h2>
	<p style="text-indent: 0pt;text-align: left;">
		<br />
	</p>
	<table style="border-collapse:collapse;margin-left:6.0245pt" cellspacing="0">
		<tr style="height:53pt">
			<td style="width:227pt">
				<p class="s1" style="padding-top: 2pt;padding-left: 0pt;text-indent: 0pt;text-align: left;">Referensnummer: </p>
				<p style="text-indent: 0pt;text-align: left;"></p>
				<p class="s2" style="padding-left: 0pt;text-indent: 0pt;text-align: left;">{{$registration->ref_nr}}</p>
			</td>
			<td style="width:14pt">
				<p style="text-indent: 0pt;text-align: left;">
					<br />
				</p>
			</td>
			<td style="width:227pt">
				<p class="s1" style="padding-left: 0pt;text-indent: 0pt;text-align: left;">Betalning mottagen:</p>
				<p style="text-indent: 0pt;text-align: left;"></p>
				<p class="s2" style="padding-left: 0pt;text-indent: 0pt;text-align: left;">{{date('Y-m-d') }}</p>
			</td>
		</tr>
		<tr style="height:53pt">
			<td style="width:227pt">
				<p class="s1" style="padding-top: 0pt;padding-left: 0pt;text-indent: 0pt;text-align: left;">Pris: </p>
				<p style="text-indent: 0pt;text-align: left;"></p>
				<p class="s2" style="padding-left: 0pt;text-indent: 0pt;text-align: left;">
					@if(trim($event->title) == trim('BP 40 VÄSTERBOTTEN BREVET') || trim($event->title) == trim('BP 80 VÄSTERBOTTEN BREVET'))
						260 kr
					@elseif(trim($event->title) == trim('BP 130 VÄSTERBOTTEN BREVET') || trim($event->title) == trim('BP 200 VÄSTERBOTTEN BREVET') || trim($event->title) == trim('BP 300 VÄSTERBOTTEN BREVET'))
						360 kr
					@else
						100 kr
					@endif
				</p>
			</td>
			<td style="width:14pt">
				<p style="text-indent: 0pt;text-align: left;"></p>
			</td>
			<td style="width:227pt">
				<p class="s1" style="padding-left: 0pt;text-indent: 0pt;text-align: left;">Varav moms:</p>
				<p style="text-indent: 0pt;text-align: left;"></p>
				<p class="s2" style="padding-left: 0pt;text-indent: 0pt;text-align: left;">0 kr</p>
			</td>
		</tr>
		<tr style="height:61pt">
			<td style="width:227pt">
				<p style="text-indent: 0pt;text-align: left;"></p>
				<p class="s1" style="padding-left: 0pt;text-indent: 0pt;text-align: left;">Organisatör:</p>
				<p style="text-indent: 0pt;text-align: left;"></p>
				<p class="s3" style="padding-left: 0pt;text-indent: 0pt;text-align: left;">{{$organizer}}</p>
			</td>
			<td style="width:14pt">
				<p style="text-indent: 0pt;text-align: left;"></p>
			</td>
			<td style="width:227pt">
				<p style="text-indent: 0pt;text-align: left;"></p>
				<p class="s1" style="padding-left: 4pt;text-indent: 0pt;text-align: left;">Event:</p>
				<p style="text-indent: 0pt;text-align: left;"></p>
				<p class="s3" style="padding-left: 0pt;text-indent: 0pt;text-align: left;">{{$event->title}}</p>
			</td>
		</tr>
		<tr style="height:26pt">
			<td style="width:227pt">
				<p class="s1" style="padding-top: 0pt;padding-left: 0pt;text-indent: 0pt;text-align: left;">Epostaddress:</p>
			</td>
			<td style="width:14pt">
				<p style="text-indent: 0pt;text-align: left;"></p>
			</td>
			<td style="width:227pt">
				<p class="s1" style="padding-top: 0pt;padding-left: 0pt;text-indent: 0pt;text-align: left;">Webbsida:</p>
			</td>
		</tr>
		<tr style="height:36pt">
			<td style="width:227pt">
				<a href="mailto:info@ebrevet.org" class="s4">info@ebrevet.se</a>
			</td>
			<td style="width:14pt">
				<p style="text-indent: 0pt;text-align: left;"></p>
			</td>
			<td style="width:227pt">
				<a href="http://www.ebrevet.org/" class="s4">www.ebrevet.org</a>
			</td>
		</tr>
		<tr style="height:38pt">
			<td style="width:227pt">
				<p class="s5" style="padding-top: 15pt;padding-left: 0pt;text-indent: 0pt;text-align: left;">Detaljinformation</p>
				<p style="text-indent:0pt;text-align:left"></p>
			</td>
			<td style="width:14pt">
				<p style="text-indent: 0pt;text-align: left;">
					<br />
				</p>
			</td>
			<td style="width:227pt">
				<p style="text-indent: 0pt;text-align: left;">
					<br />
				</p>
			</td>
			<td style="width:14pt">
				<p style="text-indent: 0pt;text-align: left;">
					<br />
				</p>
			</td>
			<td style="width:227pt">
				<p style="text-indent: 0pt;text-align: left;">
					<br />
				</p>
			</td>
		</tr>
		<tr style="height:15pt">
			<td style="width:227pt;border-bottom-style:solid;border-bottom-width:1pt">
				<p class="s1" style="padding-top:5pt;padding-left: 0pt;text-indent: 0pt;text-align: left;">Startnummer:</p>
			</td>
			<td style="width:14pt">
				<p style="text-indent: 0pt;text-align: left;"></p>
			</td>
			<td style="width:227pt;border-bottom-style:solid;border-bottom-width:1pt">
				<p class="s1" style="padding-top: 5pt;padding-left: 0pt;text-indent: 0pt;text-align: left;">Förnamn:</p>
			</td>
			<td style="width:14pt">
				<p style="text-indent: 0pt;text-align: left;"></p>
			</td>
			<td style="width:227pt;border-bottom-style:solid;border-bottom-width:1pt">
				<p class="s1" style="padding-top: 5pt;padding-left: 0pt;text-indent: 0pt;text-align: left;">Efternamn:</p>
			</td>
		</tr>
		<tr style="height:22pt">
			<td style="width:227pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
				<p class="s3" style="padding-top: 5pt;padding-left: 0pt;text-indent: 0pt;text-align: left;">
					{{$registration->startnumber}}
				</p>
			</td>
			<td style="width:14pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
				<p style="text-indent: 0pt;text-align: left;">
					<br />
				</p>
			</td>
			<td style="width:227pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
				<p class="s3" style="padding-top: 5pt;padding-left: 0pt;text-indent: 0pt;text-align: left;">{{$person->firstname}}</p>
			</td>
			<td style="width:14pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
				<p style="text-indent: 0pt;text-align: left;"></p>
			</td>
			<td style="width:227pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
				<p class="s3" style="padding-top: 5pt;padding-left: 0pt;text-indent: 0pt;text-align: left;">{{$person->surname}}</p>
			</td>
		</tr>
		<tr style="height:31pt">
			<td style="width:227pt;border-top-style:solid;border-top-width:1pt;border-bottom-style:solid;border-bottom-width:1pt">
				<p style="text-indent: 0pt;text-align: left;"></p>
				<br>
				<p class="s1" style="padding-left: 0pt;text-indent: 0pt;text-align: left;">Födelsedatum:</p>
			</td>
			<td style="width:14pt">
				<p style="text-indent: 0pt;text-align: left;">
					<br />
				</p>
			</td>
			<td style="width:227pt;border-top-style:solid;border-top-width:1pt;border-bottom-style:solid;border-bottom-width:1pt">
				<p style="text-indent: 0pt;text-align: left;">
					<br />
				</p>
				<p class="s1" style="padding-left: 0pt;text-indent: 0pt;text-align: left;">Epost address:</p>
			</td>
			<td style="width:14pt">
				<p style="text-indent: 0pt;text-align: left;">
					<br />
				</p>
			</td>
			<td style="width:227pt;border-top-style:solid;border-top-width:1pt;border-bottom-style:solid;border-bottom-width:1pt">
				<p style="text-indent: 0pt;text-align: left;">
					<br />
				</p>
				<p class="s1" style="padding-left: 0pt;text-indent: 0pt;text-align: left;">Tel mobil:</p>
			</td>
		</tr>
		<tr style="height:22pt">
			<td style="width:227pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
				<p class="s3" style="padding-top: 5pt;padding-left: 0pt;text-indent: 0pt;text-align: left;">{{$person->birthdate}}</p>
			</td>
			<td style="width:14pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
				<p style="text-indent: 0pt;text-align: left;">
					<br />
				</p>
			</td>
			<td style="width:227pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
				<p style="padding-top: 4pt;padding-left: 4pt;text-indent: 0pt;text-align: left;">
					<a href="mailto:floriank2@gmail.com" class="s4">{{$person->contactinformation->email}}</a>
				</p>
			</td>
			<td style="width:14pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
				<p style="text-indent: 0pt;text-align: left;">
					<br />
				</p>
			</td>
			<td style="width:227pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
				<p class="s3" style="padding-top: 5pt;padding-left: 0pt;text-indent: 0pt;text-align: left;">
					{{$person->contactinformation->tel}}
				</p>
			</td>
		</tr>
		<tr style="height:31pt">
			<td style="width:227pt;border-top-style:solid;border-top-width:1pt;border-bottom-style:solid;border-bottom-width:1pt">
				<p style="text-indent: 0pt;text-align: left;">
					<br />
				</p>
				<p class="s1" style="padding-left: 0pt;text-indent: 0pt;text-align: left;">Gatuadress:</p>
			</td>
			<td style="width:14pt">
				<p style="text-indent: 0pt;text-align: left;">
					<br />
				</p>
			</td>
			<td style="width:227pt;border-top-style:solid;border-top-width:1pt;border-bottom-style:solid;border-bottom-width:1pt">
				<p style="text-indent: 0pt;text-align: left;">
					<br />
				</p>
				<p class="s1" style="padding-left: 0pt;text-indent: 0pt;text-align: left;">Postkod:</p>
			</td>
			<td style="width:14pt">
				<p style="text-indent: 0pt;text-align: left;">
					<br />
				</p>
			</td>
			<td style="width:227pt;border-top-style:solid;border-top-width:1pt;border-bottom-style:solid;border-bottom-width:1pt">
				<p style="text-indent: 0pt;text-align: left;">
					<br />
				</p>
				<p class="s1" style="padding-left: 0pt;text-indent: 0pt;text-align: left;">Ort:</p>
			</td>
		</tr>
		<tr style="height:22pt">
			<td style="width:227pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
				<p class="s3" style="padding-top: 5pt;padding-left: 0pt;text-indent: 0pt;text-align: left;">{{$person->adress->adress}}</p>
			</td>
			<td style="width:14pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
				<p style="text-indent: 0pt;text-align: left;">
					<br />
				</p>
			</td>
			<td style="width:227pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
				<p class="s3" style="padding-top: 5pt;padding-left: 0pt;text-indent: 0pt;text-align: left;">
					{{$person->adress->postal_code}}
				</p>
			</td>
			<td style="width:14pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
				<p style="text-indent: 0pt;text-align: left;">
					<br />
				</p>
			</td>
			<td style="width:227pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
				<p class="s3" style="padding-top: 5pt;padding-left: 0pt;text-indent: 0pt;text-align: left;">{{$person->adress->city}}</p>
			</td>
		</tr>
		<tr style="height:31pt">
			<td style="width:227pt;border-top-style:solid;border-top-width:1pt;border-bottom-style:solid;border-bottom-width:1pt">
				<p style="text-indent: 0pt;text-align: left;">
					<br />
				</p>
				<p class="s1" style="padding-left: 0pt;text-indent: 0pt;text-align: left;">Land</p>
			</td>
			<td style="width:14pt">
				<p style="text-indent: 0pt;text-align: left;">
					<br />
				</p>
			</td>
			<td style="width:227pt;border-top-style:solid;border-top-width:1pt">
				<p style="text-indent: 0pt;text-align: left;">
					<br />
				</p>
			</td>
			<td style="width:14pt">
				<p style="text-indent: 0pt;text-align: left;">
					<br />
				</p>
			</td>
			<td style="width:227pt;border-top-style:solid;border-top-width:1pt">
				<p style="text-indent: 0pt;text-align: left;">
					<br />
				</p>
			</td>
		</tr>
		<tr style="height:22pt">
			<td style="width:227pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
				<p class="s3" style="padding-top: 5pt;padding-left: 0pt;text-indent: 0pt;text-align: left;">{{$country}}</p>
			</td>
			<td style="width:14pt;border-left-style:solid;border-left-width:1pt">
				<p style="text-indent: 0pt;text-align: left;">
					<br />
				</p>
			</td>
			<td style="width:227pt">
				<p style="text-indent: 0pt;text-align: left;">
					<br />
				</p>
			</td>
			<td style="width:14pt">
				<p style="text-indent: 0pt;text-align: left;">
					<br />
				</p>
			</td>
			<td style="width:227pt">
				<p style="text-indent: 0pt;text-align: left;">
					<br />
				</p>
			</td>
		</tr>
	</table>
	<table cellspacing="0" style="border-collapse:collapse;margin-left:6.0245pt">
		<tr style="height:24pt">
			<td style="width:227pt;border-bottom-style:solid;border-bottom-width:1pt">
				<p style="text-indent: 0pt;text-align: left;">
					<br />
				</p>
				<p class="s1" style="padding-top: 5pt;padding-left: 0pt;text-indent: 0pt;text-align: left;">Övrig information:</p>
			</td>
			<td style="width:14pt">
				<p style="text-indent: 0pt;text-align: left;">
					<br />
				</p>
			</td>
			<td style="width:227pt;border-bottom-style:solid;border-bottom-width:1pt">
				<p class="s1" style="padding-top: 5pt;padding-left: 0pt;text-indent: 0pt;text-align: left;">&nbsp;</p>
			</td>
		</tr>
		<tr style="height:22pt">
			<td colspan="3" style="width:227pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
				<p class="s3" style="padding-top: 5pt;padding-left: 0pt;text-indent: 0pt;text-align: left;">
					{{$registration->additional_information}}
				</p>
				<p style="text-indent: 0pt;text-align: left;">
					<br />
				</p>
				<p class="s3" style="padding-top: 5pt;padding-left: 0pt;text-indent: 0pt;text-align: left;">&nbsp;</p>
			</td>
		</tr>
	</table>
	<p>&nbsp;</p> @if(!$optionals->isEmpty()) <h2 style="padding-top: 5pt;padding-left: 10pt;text-indent: 0pt;text-align: left;">Medalj</h2> @foreach ($optionals as $optional) @if ($optional->categoryID === 8) <p style="padding-top: 5pt;padding-left: 10pt;text-indent: 0pt;text-align: left;">{{$optional->description}}</p> @endif @endforeach @endif <p style="text-indent: 0pt;text-align: left;">&nbsp;</p>
	<table cellspacing="0" style="border-collapse:collapse;margin-left:6.0245pt">
		<tr style="height:38pt">
			<td style="width:227pt">
				<p class="s5" style="padding-top: 15pt;padding-left: 0pt;text-indent: 0pt;text-align: left;">Inloggningsuppgifter</p>
			</td>
			<td style="width:14pt">
				<p style="text-indent: 0pt;text-align: left;">
					<br />
				</p>
			</td>
			<td style="width:227pt">
				<p style="text-indent: 0pt;text-align: left;">
					<br />
				</p>
			</td>
		</tr>
		<tr style="height:15pt">
			<td style="width:227pt;border-bottom-style:solid;border-bottom-width:1pt">
				<p class="s1" style="padding-top: 5pt;padding-left: 0pt;text-indent: 0pt;text-align: left;">
					<span class="s1" style="padding-left:0pt;text-indent:0pt;text-align:left">Användarnamn:</span>
				</p>
			</td>
			<td style="width:14pt">
				<p style="text-indent: 0pt;text-align: left;">
					<br />
				</p>
			</td>
			<td style="width:227pt;border-bottom-style:solid;border-bottom-width:1pt">
				<p class="s1" style="padding-top: 5pt;padding-left: 0pt;text-indent: 0pt;text-align: left;">
					<span class="s8" style="padding-left:0pt;text-indent:0pt;text-align:left">Lösenord:</span>
				</p>
			</td>
		</tr>
		<tr style="height:22pt">
			<td style="width:227pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
				<p class="s3" style="padding-top: 5pt;padding-left: 0pt;text-indent: 0pt;text-align: left;">
					{{$registration->startnumber}}
				</p>
			</td>
			<td style="width:14pt;border-left-style:solid;border-left-width:1pt;border-right-style:solid;border-right-width:1pt">
				<p style="text-indent: 0pt;text-align: left;">
					<br />
				</p>
			</td>
			<td style="width:227pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
				<p class="s3" style="padding-top: 5pt;padding-left: 0pt;text-indent: 0pt;text-align: left;">{{$registration->ref_nr}}</p>
			</td>
		</tr>
	</table>
	<p class="s9" style="padding-left: 10pt;text-indent: 0pt;text-align: left;">&nbsp;</p>
	<p class="s9" style="padding-left: 10pt;text-indent: 0pt;text-align: left;">&nbsp;</p>
	<p style="text-indent: 0pt;text-align: left;">
		<br />
	</p>
	<h2 style="padding-left: 10pt;text-indent: 0pt;text-align: left;">Övrig information</h2>
	<!--  <p class="s10" style="padding-top: 5pt;padding-left: 10pt;text-indent: 0pt;text-align: left;"><a href="https://cycling.lachemise.se/collections/midnight-sun">Webshop jersey</a></p>-->
	<!--  <p class="s10" style="padding-top: 5pt;padding-left: 10pt;text-indent: 0pt;line-height: 133%;text-align: left;"><a href="https://www.vasterbottenbrevet.se/program">Event program 15-21 june</a></p>-->
	<p class="s10" style="padding-top: 5pt;padding-left: 10pt;text-indent: 0pt;text-align: left;">
		<a href="{{$startlistlink}}">Startlista</a>
	</p>

	<p class="s10" style="padding-top: 5pt;padding-left: 10pt;text-indent: 0pt;text-align: left;">
		<a href="{{$updatelink}}">Redigera dina uppgifter</a>
	</p>

	@if(isset($dnslink) && !empty($dnslink))
	<p>
		Tänker du inte delta: <a href="{{ $dnslink }}">Klicka här</a>
	</p>
	@endif


	<p class="s10" style="padding-top: 5pt;text-indent: 0pt;text-align: left;"><a href="https://www.ebrevet.org/datapolicy" style="font-size: 12pt">Allmänna villkor</a><br />
	</p>
	<!--  <p class="s10" style="padding-top: 8pt;padding-left: 10pt;text-indent: 0pt;text-align: left;"><a href="{{$updatelink}}">Follow this link to change your registered information </a><br></p>-->

	<p style="text-indent: 0pt;text-align: left;">
		<br />
	</p>
</body>

</html>