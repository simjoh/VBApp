<?php

use App\Models\Organizer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $newOrganizer = new Organizer([
            'organization_name' => 'Randonneurs Sverige',
            'description' => '',
            'website' => 'https://www.randonneurgoth.com',
            'contact_person_name' => 'Daniel Ridings',
            'email' => 'daniel.ridings@randonneurs.se',
            'active' => true,
            'logo_svg' => '<?xml version="1.0" encoding="UTF-8"?>
<svg id="a" xmlns="http://www.w3.org/2000/svg" width="20.2847mm" height="20.2847mm" viewBox="0 0 57.4999 57.4999">
  <defs>
    <style>
      .cls-1 {
        fill: #f8d047;
      }

      .cls-2 {
        fill: #fdf5e1;
      }

      .cls-3 {
        fill: #0082ba;
      }
    </style>
  </defs>
  <path class="cls-2" d="M28.7495,56.5337C13.4292,56.5337.9641,44.0695.9641,28.7487S13.4292.9643,28.7495.9643s27.7844,12.4641,27.7844,27.7844-12.4641,27.785-27.7844,27.785Z"/>
  <path class="cls-3" d="M28.7495.4025C13.1188.4025.4032,13.1185.4032,28.7487s12.7156,28.3467,28.3463,28.3467,28.3452-12.7165,28.3452-28.3467S44.3792.4025,28.7495.4025ZM28.7495,56.5337C13.4292,56.5337.9641,44.0695.9641,28.7487S13.4292.9643,28.7495.9643s27.7844,12.4641,27.7844,27.7844-12.4641,27.785-27.7844,27.785Z"/>
  <g>
    <path class="cls-3" d="M11.0305,15.3509c.0986.1968.1509.4199.1445.6113-.0044.2432-.106.5005-.2568.7246-.188.2783-.4121.4604-.7451.5249-.3335.0645-.6958-.0205-1.0093-.2319-.3232-.2183-.5596-.5444-.6357-.9141-.0566-.2915-.0112-.5942.1899-.8931.2251-.333.5137-.4497.7266-.4868.46-.0806.8647.1567,1.0835.3042l.0601.04-1.0142,1.5024c.1562.0908.3691.1401.5439.1064.1914-.0371.3057-.1631.3862-.2827.0903-.1343.1279-.2539.1201-.4038-.0073-.1279-.0483-.2495-.0977-.3481l.5039-.2534ZM9.8543,15.2591c-.1279-.0571-.2642-.0698-.3843-.042-.103.0244-.2334.0884-.3408.2476-.1143.1694-.1182.3257-.0972.4341.0176.1133.0801.2349.168.3306l.6543-.9702Z"/>
    <path class="cls-3" d="M9.9138,11.3929c.3569-.3726.6284-.561.9888-.5815.2969-.0146.5537.0898.792.3184.1128.1079.2134.2373.2598.4146.0503.1729.0234.3135-.0083.4077.0625-.04.2808-.1719.5649-.1738.314.002.5464.1582.7241.3281.208.1992.3228.4092.3374.689.0229.4619-.2827.8589-.4941,1.0801l-.6934.7236-2.8906-2.7681.4194-.438ZM11.1614,12.5799l.1289-.1343c.1035-.1084.2319-.2681.2319-.4678,0-.1909-.1221-.3325-.2217-.4282-.0864-.083-.2031-.1694-.377-.1616-.1909.0083-.3242.1216-.4443.2476l-.1455.1514.8276.793ZM12.3924,13.7586l.2573-.2686c.1201-.126.2896-.3462.3066-.5459.0137-.1528-.0537-.3257-.1836-.4502-.1172-.1118-.2715-.1768-.4155-.1733-.2334.0093-.4165.1743-.5659.3306l-.2656.2773.8667.8301Z"/>
    <path class="cls-3" d="M13.3211,8.304c.3916-.2866.6787-.4224.9893-.4346.5264-.0215.835.3394.938.48.3398.4644.3022,1.0117-.0576,1.4233l2.0151.6421-.5952.4355-1.8901-.644-.0869.0635.9663,1.3208-.4932.3608-2.3613-3.2261.5757-.4214ZM14.322,9.8441l.1548-.1133c.1353-.0991.6763-.5171.3008-1.0298-.3325-.4546-.7949-.1831-1.0371-.0063l-.1689.124.7505,1.0254Z"/>
    <path class="cls-3" d="M18.7561,5.7489l-1.4326.7065.4487.9102,1.3896-.6851.2549.5171-1.3896.6851.5576,1.1309,1.4326-.7061.2549.5171-1.9819.9771-1.771-3.5928,1.9819-.9771.2549.5171Z"/>
    <path class="cls-3" d="M20.6931,4.4012l1.832,2.3784.314-2.9858.6343-.1797-.5479,4.5786-2.8667-3.6118.6343-.1797Z"/>
    <path class="cls-3" d="M27.6116,3.6976l-1.5913.146.0928,1.0112,1.5435-.1421.0527.5742-1.5435.1421.1157,1.2559,1.5908-.146.0527.5742-2.2012.2021-.3662-3.9897,2.2012-.2021.0527.5742Z"/>
    <path class="cls-3" d="M30.6785,3.7772l-.2178,3.4155-.6099-.0391.2178-3.415-.915-.0586.0366-.5742,2.4404.1558-.0366.5742-.9155-.0586Z"/>
    <path class="cls-3" d="M36.3474,4.2728c.4897.1626.7764.3271.9492.644.1406.2617.1558.5386.0518.8516-.0493.1479-.123.2939-.2632.4116-.1348.1196-.2729.1558-.3716.1675.0625.0396.2749.1797.3984.4355.1328.2847.0913.5615.0142.7949-.0908.2729-.2314.4668-.4775.5996-.4077.2183-.897.1128-1.1875.0161l-.9502-.3159,1.2617-3.7964.5747.1914ZM35.2727,7.5243l.353.1172c.165.0552.4365.1138.624.0435.144-.0532.271-.188.3281-.3589.0508-.1538.043-.3203-.0215-.4492-.1084-.207-.3359-.3018-.541-.3696l-.3643-.1211-.3784,1.1382ZM35.8103,5.908l.1763.0586c.1426.0474.3418.0947.5215.0093.1729-.082.2485-.2524.292-.3838.0381-.1138.0664-.2559-.0156-.4097-.0894-.1685-.2485-.2402-.4136-.2954l-.1992-.0659-.3613,1.0869Z"/>
    <path class="cls-3" d="M40.6033,9.5096l-1.4873-.8438-.8579.624-.5737-.3257,3.8223-2.7012-.4409,4.6196-.5737-.3257.1113-1.0474ZM40.6731,8.887l.1523-1.4722-1.2061.8745,1.0537.5977Z"/>
    <path class="cls-3" d="M42.0052,11.5897l2.8794-3.1504.0918,4.2007,1.8682-2.0444.4512.4126-2.8633,3.1328-.0918-4.2007-1.8843,2.062-.4512-.4126Z"/>
    <path class="cls-3" d="M47.7781,16.5828l-.9736-1.4043-1.043.1904-.376-.5425,4.6143-.7769-2.3994,3.9707-.376-.542.5537-.8955ZM48.1111,16.0526l.7754-1.2603-1.4658.2646.6904.9956Z"/>
  </g>
  <g>
    <path class="cls-3" d="M6.8762,22.8856c-.0923.3398-.4258.5312-.7656.439-.3398-.0918-.5312-.4253-.439-.7651.0918-.3398.4253-.5312.7651-.4395.3398.0923.5312.4258.4395.7656Z"/>
    <path class="cls-3" d="M6.7776,27.5951c-.0176.4858-.0884.7954-.2715,1.0469-.3096.4272-.7847.4463-.9585.4404-.5757-.0205-.9829-.3892-1.084-.9272l-1.7534,1.1865.0264-.7378,1.6777-1.0869.0039-.1079-1.6372-.0586.022-.6113,4,.1421-.0254.7139ZM4.9461,27.4276l-.0068.1919c-.0059.168-.0122.8521.623.875.564.02.6367-.5117.6475-.8115l.0078-.21-1.2715-.0454Z"/>
    <path class="cls-3" d="M4.1087,33.2923l-.248-1.6919-1.019-.293-.0957-.6528,4.4795,1.3564-3.916,2.4897-.0957-.6528.895-.5557ZM4.6428,32.9657l1.2554-.7842-1.4312-.415.1758,1.1992Z"/>
    <path class="cls-3" d="M3.8499,36.4578l3.9873-1.5317-1.7979,3.8013,2.5874-.9941.2192.5713-3.9648,1.5234,1.7979-3.8013-2.6094,1.0024-.2197-.5713Z"/>
    <path class="cls-3" d="M10.0745,40.3983c.3091.4639.5288.9014.5005,1.4541-.0352.6064-.3628,1.1641-.9165,1.5332s-1.186.459-1.7881.2266c-.5337-.207-.8213-.541-1.1309-1.0049l-.459-.6885,3.3281-2.2188.4658.6982ZM7.0989,42.1082l.1465.2197c.166.249.4155.5801.8232.7266.3276.1133.7964.125,1.2505-.1777.439-.293.6436-.7324.6587-1.0879.0166-.4287-.1997-.7969-.3623-1.041l-.1465-.2197-2.3701,1.5801Z"/>
    <path class="cls-3" d="M13.8875,47.7137c-.8018.8584-2.0586.9141-2.9346.0957-.876-.8174-.9062-2.0752-.105-2.9336.8018-.8584,2.0591-.9141,2.9346-.0967.876.8184.9062,2.0762.105,2.9346ZM13.4407,47.2967c.605-.6484.584-1.5205-.0425-2.1055s-1.498-.5469-2.1035.1016c-.605.6484-.5835,1.5205.0425,2.1055.6265.585,1.498.5459,2.1035-.1016Z"/>
    <path class="cls-3" d="M14.8069,50.7674l2.0425-3.7588,1.1011,4.0645,1.3252-2.4385.5386.292-2.0312,3.7383-1.1006-4.0654-1.3369,2.4609-.5386-.293Z"/>
    <path class="cls-3" d="M20.5921,53.5037l1.0542-4.1387,2.0635,3.6631.6836-2.6855.5928.1514-1.0479,4.1152-2.063-3.6631-.6899,2.709-.5933-.1514Z"/>
    <path class="cls-3" d="M28.5984,51.3494l-1.5947-.0938-.0596,1.0137,1.5474.0908-.0337.5752-1.5474-.0908-.0737,1.2598,1.5952.0938-.0337.5752-2.2065-.1289.2339-4,2.2065.1289-.0337.5762Z"/>
    <path class="cls-3" d="M30.3753,50.702l.2695,2.3984c.0244.2148.0601.4766.2026.6719.1421.1895.4341.3682.7861.3281.3521-.0391.5972-.2783.6934-.4941.0962-.2227.0723-.4863.0483-.7002l-.27-2.3984.6084-.0693.2881,2.5596c.0356.3164.0132.7061-.2534,1.0742-.1836.25-.5088.5283-1.0518.5898-.543.0605-.9214-.1387-1.1558-.3418-.3418-.2998-.4507-.6738-.4863-.9902l-.2881-2.5596.6089-.0684Z"/>
    <path class="cls-3" d="M34.7742,49.9198c.4648-.1406.7803-.1738,1.0776-.082.5039.1533.6758.5967.7266.7627.167.5508-.0498,1.0557-.5259,1.3252l1.689,1.2725-.7056.2148-1.5708-1.2344-.103.0312.4751,1.5664-.5854.1777-1.1602-3.8271.6826-.207ZM35.2088,51.7049l.1836-.0557c.1606-.0488.8096-.2646.6255-.8721-.1636-.54-.6899-.4365-.9771-.3496l-.2007.0605.3687,1.2168Z"/>
    <path class="cls-3" d="M39.3284,49.0555c-.0718-.0654-.1567-.1172-.272-.1475-.1582-.0371-.2954-.0176-.4521.0586-.335.1631-.4106.4473-.2979.6787.0527.1084.1821.2793.6016.249l.4331-.0303c.7954-.0527,1.1626.2363,1.3623.6465.333.6865.0752,1.3926-.5996,1.7207-.416.2021-.7407.166-1.0239.0498-.2993-.1211-.5015-.3174-.644-.5547l.4883-.3838c.0864.1777.2324.3135.374.3857.1655.0791.3589.0918.5752-.0127.335-.1631.4761-.5322.2998-.8936-.1782-.3672-.5327-.3818-.8174-.3643l-.4165.0225c-.3574.0205-.916-.0156-1.1782-.5557-.2363-.4863-.084-1.1348.5913-1.4629.3887-.1885.6812-.1504.8418-.1084.1392.0391.3022.1133.4517.2275l-.3174.4746Z"/>
    <path class="cls-3" d="M44.0027,45.828c-.085-.0459-.1807-.0752-.2998-.0771-.1621.0029-.29.0566-.4233.168-.2847.2393-.2891.5332-.123.7305.0771.0918.2446.2256.644.0938l.4121-.1348c.7578-.2451,1.1836-.0547,1.4775.2949.4902.583.4121,1.3301-.1611,1.8125-.3545.2969-.6777.3418-.9805.2988-.3193-.0449-.5625-.1855-.7588-.3809l.3789-.4912c.1279.1514.3018.2471.457.2822.1797.0371.3701.002.5537-.1523.2852-.2393.332-.6309.0732-.9395-.2627-.3115-.6094-.2393-.8818-.1523l-.3975.123c-.3418.1064-.8916.208-1.2773-.251-.3477-.4141-.3579-1.0791.2158-1.5615.3311-.2783.6235-.3125.79-.3105.1436.0039.3203.0361.4922.1104l-.1914.5371Z"/>
    <path class="cls-3" d="M44.8094,43.825l2.8496.9512-1.4072-2.6533.4258-.5039,2.1025,4.1064-4.3965-1.3965.4258-.5039Z"/>
    <path class="cls-3" d="M48.7361,39.3241l-.8193,1.3672.8682.5215.7959-1.3271.4932.2959-.7949,1.3271,1.0801.6475.8193-1.3682.4941.2959-1.1348,1.8926-3.4297-2.0557,1.1348-1.8926.4932.2959Z"/>
    <path class="cls-3" d="M48.9812,37.4149c.1836-.4502.3564-.7163.6143-.8896.4375-.2949.8896-.1499,1.0508-.0845.5332.2168.7891.7026.6982,1.2427l2.0527-.5117-.2773.6826-1.9473.4443-.041.1006,1.5166.6162-.2305.5664-3.7051-1.5059.2686-.6611ZM50.6424,38.2l.0723-.1777c.0635-.1553.3047-.7949-.2842-1.0352-.5225-.2119-.7734.2617-.8857.54l-.0791.1943,1.1768.4785Z"/>
    <path class="cls-3" d="M49.9978,34.4813l3.8477,1.0889-.167.5884-3.8467-1.0889.166-.5884Z"/>
    <path class="cls-3" d="M52.5105,31.5223l.21-1.6362.0293.0034c.6787.0869,1.2061.3604,1.5312.7954.3457.4614.3906.9634.333,1.4155-.0742.5771-.2939.9668-.7148,1.2817-.4482.3354-1.0039.4697-1.5752.3965-.6191-.0791-1.123-.3979-1.4082-.7671-.3018-.3833-.4814-.957-.4033-1.5698.0459-.3569.1709-.7163.3828-1.0034.2266-.3037.4844-.4766.6777-.5664l.2773.5254c-.1826.0977-.3838.2534-.5156.4302-.1582.2031-.2305.436-.2617.6802-.0664.5176.1045.8784.2764,1.0996.2275.2954.6299.5103,1.0586.5654.3984.0508.8164-.0469,1.1289-.2725.3271-.2363.4775-.562.5264-.9429.0449-.3452-.0205-.6436-.2373-.9316-.1855-.2476-.4062-.3667-.6299-.4194l-.126.9878-.5596-.0718Z"/>
    <path class="cls-3" d="M51.2078,26.1419l.1016,1.5913,1.0107-.0645-.0986-1.5435.5742-.0366.0986,1.5435,1.2559-.0806-.1016-1.5908.5742-.0366.1406,2.2012-3.9893.2544-.1406-2.2012.5742-.0366Z"/>
    <path class="cls-3" d="M50.6053,22.828c-.0928-.3394.0986-.6738.4385-.7661.3398-.0928.6738.0981.7666.438.0928.3394-.0986.6738-.4385.7661-.3398.0928-.6738-.0981-.7666-.438Z"/>
  </g>
  <g>
    <path class="cls-3" d="M48.5977,26.3867c-1.0793-9.1537-8.3322-16.4064-17.4861-17.4857v17.4857h17.4861Z"/>
    <path class="cls-3" d="M26.3873,8.9008c-9.1542,1.0789-16.4068,8.3318-17.4861,17.4859h17.4861V8.9008Z"/>
    <path class="cls-3" d="M8.9011,31.1111c1.079,9.1545,8.3317,16.407,17.4861,17.486v-17.486H8.9011Z"/>
    <path class="cls-3" d="M31.1116,48.5969c9.1541-1.0793,16.4071-8.3317,17.4861-17.4858h-17.4861v17.4858Z"/>
    <path class="cls-1" d="M48.5977,26.3867h-17.4861V8.901c-.7762-.0916-1.5619-.1518-2.3627-.1518s-1.5858.0602-2.3616.1516v17.4859H8.9012c-.0915.7762-.1517,1.5618-.1517,2.3625s.0602,1.5859.1517,2.3619h17.4861v17.486c.7759.0914,1.5612.1516,2.3616.1516s1.5865-.0602,2.3627-.1518v-17.4858h17.4861c.0915-.7759.1517-1.5613.1517-2.3619s-.0602-1.5864-.1517-2.3625Z"/>
  </g>
</svg>', // Add logo SVG if available
        ]);

        $newOrganizer->save();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (App::isProduction()) {
            // Remove the added organizer
            Organizer::where('organization_name', 'CK Hymer')->delete();
        }
    }
};
