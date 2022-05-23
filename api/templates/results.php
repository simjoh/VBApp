
<!DOCTYPE html>
<html lang="en"><head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>results-2021</title>
<!--	<script src="results-2017-filer/skapa-resultat.js"></script>-->
</head>
<body>


<meta charset="utf-8">
<style>

    body {
        font-family: Helvetica Neue, Helvetica, Arial, sans-serif;
        text-align: left;
    }

    table {
        border-collapse: collapse;
    }

    th, td {
        border-bottom: 1px solid black;
        padding: .6em;
    }

    th {
        background: black;
        color: white;
    }

    td:nth-child(1) {
        text-align: right;
    }

    td:nth-child(2) {
        text-align: right;
    }

    .dnf {
        background: #bbbbbb;
        color: #444444;
    }

    .done {
        background: #f8b62a;
        font-weight: bold;
    }

</style>


<table>
	<thead>
	<tr>
		<th width="5%">ID</th>
		<th width="7%">Bana</th>
		<th width="20%">Efternamn</th>
		<th width="15%">Förnamn</th>
		<th width="25%">Klubb</th>
		<th>Stämplat</th>
		<th width="5%">Tid</th>
	</tr>
	</thead>
	<tbody>
	<?php foreach($resultlist as $key=>$value){ ?>
    <?php foreach($value as $key1=>$val){ ?>
			<tr class="done">
        <?php foreach($val as $key2=>$va){ ?>
			<td><?php echo $va;?>

			</td>
            <?php } ?>
			</tr>
        <?php } ?>
    <?php } ?>

	</tbody>
</table>


Hello <?=$name?>!

