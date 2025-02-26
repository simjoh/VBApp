<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $municipalities = [
            // Stockholm County (01)
            ['municipality_code' => '0114', 'name' => 'Upplands Väsby'],
            ['municipality_code' => '0115', 'name' => 'Vallentuna'],
            ['municipality_code' => '0117', 'name' => 'Österåker'],
            ['municipality_code' => '0120', 'name' => 'Värmdö'],
            ['municipality_code' => '0123', 'name' => 'Järfälla'],
            ['municipality_code' => '0125', 'name' => 'Ekerö'],
            ['municipality_code' => '0126', 'name' => 'Huddinge'],
            ['municipality_code' => '0127', 'name' => 'Botkyrka'],
            ['municipality_code' => '0128', 'name' => 'Salem'],
            ['municipality_code' => '0136', 'name' => 'Haninge'],
            ['municipality_code' => '0138', 'name' => 'Tyresö'],
            ['municipality_code' => '0139', 'name' => 'Upplands-Bro'],
            ['municipality_code' => '0140', 'name' => 'Nykvarn'],
            ['municipality_code' => '0160', 'name' => 'Täby'],
            ['municipality_code' => '0162', 'name' => 'Danderyd'],
            ['municipality_code' => '0163', 'name' => 'Sollentuna'],
            ['municipality_code' => '0180', 'name' => 'Stockholm'],
            ['municipality_code' => '0181', 'name' => 'Södertälje'],
            ['municipality_code' => '0182', 'name' => 'Nacka'],
            ['municipality_code' => '0183', 'name' => 'Sundbyberg'],
            ['municipality_code' => '0184', 'name' => 'Solna'],
            ['municipality_code' => '0186', 'name' => 'Lidingö'],
            ['municipality_code' => '0187', 'name' => 'Vaxholm'],
            ['municipality_code' => '0188', 'name' => 'Norrtälje'],
            ['municipality_code' => '0191', 'name' => 'Sigtuna'],
            ['municipality_code' => '0192', 'name' => 'Nynäshamn'],

            // Uppsala County (03)
            ['municipality_code' => '0305', 'name' => 'Håbo'],
            ['municipality_code' => '0319', 'name' => 'Älvkarleby'],
            ['municipality_code' => '0330', 'name' => 'Knivsta'],
            ['municipality_code' => '0331', 'name' => 'Heby'],
            ['municipality_code' => '0360', 'name' => 'Tierp'],
            ['municipality_code' => '0380', 'name' => 'Uppsala'],
            ['municipality_code' => '0381', 'name' => 'Enköping'],
            ['municipality_code' => '0382', 'name' => 'Östhammar'],

            // Södermanland County (04)
            ['municipality_code' => '0428', 'name' => 'Vingåker'],
            ['municipality_code' => '0461', 'name' => 'Gnesta'],
            ['municipality_code' => '0480', 'name' => 'Nyköping'],
            ['municipality_code' => '0481', 'name' => 'Oxelösund'],
            ['municipality_code' => '0482', 'name' => 'Flen'],
            ['municipality_code' => '0483', 'name' => 'Katrineholm'],
            ['municipality_code' => '0484', 'name' => 'Eskilstuna'],
            ['municipality_code' => '0486', 'name' => 'Strängnäs'],
            ['municipality_code' => '0488', 'name' => 'Trosa'],

            // Östergötland County (05)
            ['municipality_code' => '0509', 'name' => 'Ödeshög'],
            ['municipality_code' => '0512', 'name' => 'Ydre'],
            ['municipality_code' => '0513', 'name' => 'Kinda'],
            ['municipality_code' => '0560', 'name' => 'Boxholm'],
            ['municipality_code' => '0561', 'name' => 'Åtvidaberg'],
            ['municipality_code' => '0562', 'name' => 'Finspång'],
            ['municipality_code' => '0563', 'name' => 'Valdemarsvik'],
            ['municipality_code' => '0580', 'name' => 'Linköping'],
            ['municipality_code' => '0581', 'name' => 'Norrköping'],
            ['municipality_code' => '0582', 'name' => 'Söderköping'],
            ['municipality_code' => '0583', 'name' => 'Motala'],
            ['municipality_code' => '0584', 'name' => 'Vadstena'],
            ['municipality_code' => '0586', 'name' => 'Mjölby'],

            // Jönköping County (06)
            ['municipality_code' => '0604', 'name' => 'Aneby'],
            ['municipality_code' => '0617', 'name' => 'Gnosjö'],
            ['municipality_code' => '0642', 'name' => 'Mullsjö'],
            ['municipality_code' => '0643', 'name' => 'Habo'],
            ['municipality_code' => '0662', 'name' => 'Gislaved'],
            ['municipality_code' => '0665', 'name' => 'Vaggeryd'],
            ['municipality_code' => '0680', 'name' => 'Jönköping'],
            ['municipality_code' => '0682', 'name' => 'Nässjö'],
            ['municipality_code' => '0683', 'name' => 'Värnamo'],
            ['municipality_code' => '0684', 'name' => 'Sävsjö'],
            ['municipality_code' => '0685', 'name' => 'Vetlanda'],
            ['municipality_code' => '0686', 'name' => 'Eksjö'],
            ['municipality_code' => '0687', 'name' => 'Tranås'],

            // Kronoberg County (07)
            ['municipality_code' => '0760', 'name' => 'Uppvidinge'],
            ['municipality_code' => '0761', 'name' => 'Lessebo'],
            ['municipality_code' => '0763', 'name' => 'Tingsryd'],
            ['municipality_code' => '0764', 'name' => 'Alvesta'],
            ['municipality_code' => '0765', 'name' => 'Älmhult'],
            ['municipality_code' => '0767', 'name' => 'Markaryd'],
            ['municipality_code' => '0780', 'name' => 'Växjö'],
            ['municipality_code' => '0781', 'name' => 'Ljungby'],

            // Kalmar County (08)
            ['municipality_code' => '0821', 'name' => 'Högsby'],
            ['municipality_code' => '0834', 'name' => 'Torsås'],
            ['municipality_code' => '0840', 'name' => 'Mörbylånga'],
            ['municipality_code' => '0860', 'name' => 'Hultsfred'],
            ['municipality_code' => '0861', 'name' => 'Mönsterås'],
            ['municipality_code' => '0862', 'name' => 'Emmaboda'],
            ['municipality_code' => '0880', 'name' => 'Kalmar'],
            ['municipality_code' => '0881', 'name' => 'Nybro'],
            ['municipality_code' => '0882', 'name' => 'Oskarshamn'],
            ['municipality_code' => '0883', 'name' => 'Västervik'],
            ['municipality_code' => '0884', 'name' => 'Vimmerby'],
            ['municipality_code' => '0885', 'name' => 'Borgholm'],

            // Gotland County (09)
            ['municipality_code' => '0980', 'name' => 'Gotland'],

            // Blekinge County (10)
            ['municipality_code' => '1060', 'name' => 'Olofström'],
            ['municipality_code' => '1080', 'name' => 'Karlskrona'],
            ['municipality_code' => '1081', 'name' => 'Ronneby'],
            ['municipality_code' => '1082', 'name' => 'Karlshamn'],
            ['municipality_code' => '1083', 'name' => 'Sölvesborg'],

            // Skåne County (12)
            ['municipality_code' => '1214', 'name' => 'Svalöv'],
            ['municipality_code' => '1230', 'name' => 'Staffanstorp'],
            ['municipality_code' => '1231', 'name' => 'Burlöv'],
            ['municipality_code' => '1233', 'name' => 'Vellinge'],
            ['municipality_code' => '1256', 'name' => 'Östra Göinge'],
            ['municipality_code' => '1257', 'name' => 'Örkelljunga'],
            ['municipality_code' => '1260', 'name' => 'Bjuv'],
            ['municipality_code' => '1261', 'name' => 'Kävlinge'],
            ['municipality_code' => '1262', 'name' => 'Lomma'],
            ['municipality_code' => '1263', 'name' => 'Svedala'],
            ['municipality_code' => '1264', 'name' => 'Skurup'],
            ['municipality_code' => '1265', 'name' => 'Sjöbo'],
            ['municipality_code' => '1266', 'name' => 'Hörby'],
            ['municipality_code' => '1267', 'name' => 'Höör'],
            ['municipality_code' => '1270', 'name' => 'Tomelilla'],
            ['municipality_code' => '1272', 'name' => 'Bromölla'],
            ['municipality_code' => '1273', 'name' => 'Osby'],
            ['municipality_code' => '1275', 'name' => 'Perstorp'],
            ['municipality_code' => '1276', 'name' => 'Klippan'],
            ['municipality_code' => '1277', 'name' => 'Åstorp'],
            ['municipality_code' => '1278', 'name' => 'Båstad'],
            ['municipality_code' => '1280', 'name' => 'Malmö'],
            ['municipality_code' => '1281', 'name' => 'Lund'],
            ['municipality_code' => '1282', 'name' => 'Landskrona'],
            ['municipality_code' => '1283', 'name' => 'Helsingborg'],
            ['municipality_code' => '1284', 'name' => 'Höganäs'],
            ['municipality_code' => '1285', 'name' => 'Eslöv'],
            ['municipality_code' => '1286', 'name' => 'Ystad'],
            ['municipality_code' => '1287', 'name' => 'Trelleborg'],
            ['municipality_code' => '1290', 'name' => 'Kristianstad'],
            ['municipality_code' => '1291', 'name' => 'Simrishamn'],
            ['municipality_code' => '1292', 'name' => 'Ängelholm'],
            ['municipality_code' => '1293', 'name' => 'Hässleholm'],

            // Halland County (13)
            ['municipality_code' => '1315', 'name' => 'Hylte'],
            ['municipality_code' => '1380', 'name' => 'Halmstad'],
            ['municipality_code' => '1381', 'name' => 'Laholm'],
            ['municipality_code' => '1382', 'name' => 'Falkenberg'],
            ['municipality_code' => '1383', 'name' => 'Varberg'],
            ['municipality_code' => '1384', 'name' => 'Kungsbacka'],

            // Västra Götaland County (14)
            ['municipality_code' => '1401', 'name' => 'Härryda'],
            ['municipality_code' => '1402', 'name' => 'Partille'],
            ['municipality_code' => '1407', 'name' => 'Öckerö'],
            ['municipality_code' => '1415', 'name' => 'Stenungsund'],
            ['municipality_code' => '1419', 'name' => 'Tjörn'],
            ['municipality_code' => '1421', 'name' => 'Orust'],
            ['municipality_code' => '1427', 'name' => 'Sotenäs'],
            ['municipality_code' => '1430', 'name' => 'Munkedal'],
            ['municipality_code' => '1435', 'name' => 'Tanum'],
            ['municipality_code' => '1438', 'name' => 'Dals-Ed'],
            ['municipality_code' => '1439', 'name' => 'Färgelanda'],
            ['municipality_code' => '1440', 'name' => 'Ale'],
            ['municipality_code' => '1441', 'name' => 'Lerum'],
            ['municipality_code' => '1442', 'name' => 'Vårgårda'],
            ['municipality_code' => '1443', 'name' => 'Bollebygd'],
            ['municipality_code' => '1444', 'name' => 'Grästorp'],
            ['municipality_code' => '1445', 'name' => 'Essunga'],
            ['municipality_code' => '1446', 'name' => 'Karlsborg'],
            ['municipality_code' => '1447', 'name' => 'Gullspång'],
            ['municipality_code' => '1452', 'name' => 'Tranemo'],
            ['municipality_code' => '1460', 'name' => 'Bengtsfors'],
            ['municipality_code' => '1461', 'name' => 'Mellerud'],
            ['municipality_code' => '1462', 'name' => 'Lilla Edet'],
            ['municipality_code' => '1463', 'name' => 'Mark'],
            ['municipality_code' => '1465', 'name' => 'Svenljunga'],
            ['municipality_code' => '1466', 'name' => 'Herrljunga'],
            ['municipality_code' => '1470', 'name' => 'Vara'],
            ['municipality_code' => '1471', 'name' => 'Götene'],
            ['municipality_code' => '1472', 'name' => 'Tibro'],
            ['municipality_code' => '1473', 'name' => 'Töreboda'],
            ['municipality_code' => '1480', 'name' => 'Göteborg'],
            ['municipality_code' => '1481', 'name' => 'Mölndal'],
            ['municipality_code' => '1482', 'name' => 'Kungälv'],
            ['municipality_code' => '1484', 'name' => 'Lysekil'],
            ['municipality_code' => '1485', 'name' => 'Uddevalla'],
            ['municipality_code' => '1486', 'name' => 'Strömstad'],
            ['municipality_code' => '1487', 'name' => 'Vänersborg'],
            ['municipality_code' => '1488', 'name' => 'Trollhättan'],
            ['municipality_code' => '1489', 'name' => 'Alingsås'],
            ['municipality_code' => '1490', 'name' => 'Borås'],
            ['municipality_code' => '1491', 'name' => 'Ulricehamn'],
            ['municipality_code' => '1492', 'name' => 'Åmål'],
            ['municipality_code' => '1493', 'name' => 'Mariestad'],
            ['municipality_code' => '1494', 'name' => 'Lidköping'],
            ['municipality_code' => '1495', 'name' => 'Skara'],
            ['municipality_code' => '1496', 'name' => 'Skövde'],
            ['municipality_code' => '1497', 'name' => 'Hjo'],
            ['municipality_code' => '1498', 'name' => 'Tidaholm'],
            ['municipality_code' => '1499', 'name' => 'Falköping'],

            // Värmland County (17)
            ['municipality_code' => '1715', 'name' => 'Kil'],
            ['municipality_code' => '1730', 'name' => 'Eda'],
            ['municipality_code' => '1737', 'name' => 'Torsby'],
            ['municipality_code' => '1760', 'name' => 'Storfors'],
            ['municipality_code' => '1761', 'name' => 'Hammarö'],
            ['municipality_code' => '1762', 'name' => 'Munkfors'],
            ['municipality_code' => '1763', 'name' => 'Forshaga'],
            ['municipality_code' => '1764', 'name' => 'Grums'],
            ['municipality_code' => '1765', 'name' => 'Årjäng'],
            ['municipality_code' => '1766', 'name' => 'Sunne'],
            ['municipality_code' => '1780', 'name' => 'Karlstad'],
            ['municipality_code' => '1781', 'name' => 'Kristinehamn'],
            ['municipality_code' => '1782', 'name' => 'Filipstad'],
            ['municipality_code' => '1783', 'name' => 'Hagfors'],
            ['municipality_code' => '1784', 'name' => 'Arvika'],
            ['municipality_code' => '1785', 'name' => 'Säffle'],

            // Örebro County (18)
            ['municipality_code' => '1814', 'name' => 'Lekeberg'],
            ['municipality_code' => '1860', 'name' => 'Laxå'],
            ['municipality_code' => '1861', 'name' => 'Hallsberg'],
            ['municipality_code' => '1862', 'name' => 'Degerfors'],
            ['municipality_code' => '1863', 'name' => 'Hällefors'],
            ['municipality_code' => '1864', 'name' => 'Ljusnarsberg'],
            ['municipality_code' => '1880', 'name' => 'Örebro'],
            ['municipality_code' => '1881', 'name' => 'Kumla'],
            ['municipality_code' => '1882', 'name' => 'Askersund'],
            ['municipality_code' => '1883', 'name' => 'Karlskoga'],
            ['municipality_code' => '1884', 'name' => 'Nora'],
            ['municipality_code' => '1885', 'name' => 'Lindesberg'],

            // Västmanland County (19)
            ['municipality_code' => '1904', 'name' => 'Skinnskatteberg'],
            ['municipality_code' => '1907', 'name' => 'Surahammar'],
            ['municipality_code' => '1960', 'name' => 'Kungsör'],
            ['municipality_code' => '1961', 'name' => 'Hallstahammar'],
            ['municipality_code' => '1962', 'name' => 'Norberg'],
            ['municipality_code' => '1980', 'name' => 'Västerås'],
            ['municipality_code' => '1981', 'name' => 'Sala'],
            ['municipality_code' => '1982', 'name' => 'Fagersta'],
            ['municipality_code' => '1983', 'name' => 'Köping'],
            ['municipality_code' => '1984', 'name' => 'Arboga'],

            // Dalarna County (20)
            ['municipality_code' => '2021', 'name' => 'Vansbro'],
            ['municipality_code' => '2023', 'name' => 'Malung-Sälen'],
            ['municipality_code' => '2026', 'name' => 'Gagnef'],
            ['municipality_code' => '2029', 'name' => 'Leksand'],
            ['municipality_code' => '2031', 'name' => 'Rättvik'],
            ['municipality_code' => '2034', 'name' => 'Orsa'],
            ['municipality_code' => '2039', 'name' => 'Älvdalen'],
            ['municipality_code' => '2061', 'name' => 'Smedjebacken'],
            ['municipality_code' => '2062', 'name' => 'Mora'],
            ['municipality_code' => '2080', 'name' => 'Falun'],
            ['municipality_code' => '2081', 'name' => 'Borlänge'],
            ['municipality_code' => '2082', 'name' => 'Säter'],
            ['municipality_code' => '2083', 'name' => 'Hedemora'],
            ['municipality_code' => '2084', 'name' => 'Avesta'],
            ['municipality_code' => '2085', 'name' => 'Ludvika'],

            // Gävleborg County (21)
            ['municipality_code' => '2101', 'name' => 'Ockelbo'],
            ['municipality_code' => '2104', 'name' => 'Hofors'],
            ['municipality_code' => '2121', 'name' => 'Ovanåker'],
            ['municipality_code' => '2132', 'name' => 'Nordanstig'],
            ['municipality_code' => '2161', 'name' => 'Ljusdal'],
            ['municipality_code' => '2180', 'name' => 'Gävle'],
            ['municipality_code' => '2181', 'name' => 'Sandviken'],
            ['municipality_code' => '2182', 'name' => 'Söderhamn'],
            ['municipality_code' => '2183', 'name' => 'Bollnäs'],
            ['municipality_code' => '2184', 'name' => 'Hudiksvall'],

            // Västernorrland County (22)
            ['municipality_code' => '2260', 'name' => 'Ånge'],
            ['municipality_code' => '2262', 'name' => 'Timrå'],
            ['municipality_code' => '2280', 'name' => 'Härnösand'],
            ['municipality_code' => '2281', 'name' => 'Sundsvall'],
            ['municipality_code' => '2282', 'name' => 'Kramfors'],
            ['municipality_code' => '2283', 'name' => 'Sollefteå'],
            ['municipality_code' => '2284', 'name' => 'Örnsköldsvik'],

            // Jämtland County (23)
            ['municipality_code' => '2303', 'name' => 'Ragunda'],
            ['municipality_code' => '2305', 'name' => 'Bräcke'],
            ['municipality_code' => '2309', 'name' => 'Krokom'],
            ['municipality_code' => '2313', 'name' => 'Strömsund'],
            ['municipality_code' => '2321', 'name' => 'Åre'],
            ['municipality_code' => '2326', 'name' => 'Berg'],
            ['municipality_code' => '2361', 'name' => 'Härjedalen'],
            ['municipality_code' => '2380', 'name' => 'Östersund'],

            // Västerbotten County (24)
            ['municipality_code' => '2401', 'name' => 'Nordmaling'],
            ['municipality_code' => '2403', 'name' => 'Bjurholm'],
            ['municipality_code' => '2404', 'name' => 'Vindeln'],
            ['municipality_code' => '2409', 'name' => 'Robertsfors'],
            ['municipality_code' => '2417', 'name' => 'Norsjö'],
            ['municipality_code' => '2418', 'name' => 'Malå'],
            ['municipality_code' => '2421', 'name' => 'Storuman'],
            ['municipality_code' => '2422', 'name' => 'Sorsele'],
            ['municipality_code' => '2425', 'name' => 'Dorotea'],
            ['municipality_code' => '2460', 'name' => 'Vännäs'],
            ['municipality_code' => '2462', 'name' => 'Vilhelmina'],
            ['municipality_code' => '2463', 'name' => 'Åsele'],
            ['municipality_code' => '2480', 'name' => 'Umeå'],
            ['municipality_code' => '2481', 'name' => 'Lycksele'],
            ['municipality_code' => '2482', 'name' => 'Skellefteå'],

            // Norrbotten County (25)
            ['municipality_code' => '2505', 'name' => 'Arvidsjaur'],
            ['municipality_code' => '2506', 'name' => 'Arjeplog'],
            ['municipality_code' => '2510', 'name' => 'Jokkmokk'],
            ['municipality_code' => '2513', 'name' => 'Överkalix'],
            ['municipality_code' => '2514', 'name' => 'Kalix'],
            ['municipality_code' => '2518', 'name' => 'Övertorneå'],
            ['municipality_code' => '2521', 'name' => 'Pajala'],
            ['municipality_code' => '2523', 'name' => 'Gällivare'],
            ['municipality_code' => '2560', 'name' => 'Älvsbyn'],
            ['municipality_code' => '2580', 'name' => 'Luleå'],
            ['municipality_code' => '2581', 'name' => 'Piteå'],
            ['municipality_code' => '2582', 'name' => 'Boden'],
            ['municipality_code' => '2583', 'name' => 'Haparanda'],
            ['municipality_code' => '2584', 'name' => 'Kiruna'],
        ];

        foreach ($municipalities as $municipality) {
            // Get county code (first 2 digits of municipality code)
            $county_code = substr($municipality['municipality_code'], 0, 2);

            // Get the county ID based on county_code
            $county_id = DB::table('countys')
                ->where('county_code', $county_code)
                ->value('id');

            if ($county_id) {
                DB::table('municipalities')->insert([
                    'municipality_code' => $municipality['municipality_code'],
                    'name' => $municipality['name'],
                    'county_id' => $county_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('municipalities')->truncate();
    }
};
