<?php

namespace App\common;

class GlobalConfig
{
    private static $data = [
        'd32650ff-15f8-4df1-9845-d3dc252a7a84' => '0beda5d8-bbce-42d1-b7b6-4405a2e03db9', // msr 2024
        '0f1f3440-7b2e-4346-9087-57ce8daec838' => '295a767e-01a1-416a-97ae-cb89778ccb23', // Hjo tur retur 2025-06-28
        '3a23f847-7429-4d1a-a6d8-1c4bc82ec36d' => '4a6fc57a-4aba-466c-82d3-5a34f93ee646', // BRM 400 Halmstad 2025-06-14
        '1f897e79-79e9-48d0-bdcd-04899d3120e2' => '63036d97-e106-4ee8-834c-996fb05a70a2', // M 400 Halmstad 2025-05-24
        'f72918b7-caf1-40ca-bc68-86cdbd4cd118' => '8f3b106b-766a-4fb9-88ba-4d0977da95a9', // BRM 300 Torpa stenhus 	2025-05-24
        '707fee62-c3df-4e62-a6ea-8e589cb6a89c' => '9fc453c2-d742-4640-9b6b-e5d8bfc08ffe', // BRM 300 Torpa stenhus 	2025-05-10
        '43d0fd99-73c9-49b3-af9d-ffab4eb4682f' => '43d0fd99-73c9-49b3-af9d-ffab4eb4682f', // BRM 200 Öströö fårfarm 2025-04-26
        '3fb4e1f1-b907-4225-9d90-74263d75f499' => '79604d41-de6a-4a9b-9db6-c56239814cab', // BRM 200 Öströö fårfarm 2025-05-10
        '01679016-013b-4a52-b3e7-b0d5ee29b1c6' => '584b3007-f672-4088-ab44-91d81c396140', // BRM 200 Bönhamn 2025-05-17
        '747be5e5-c8b3-47dc-87ba-a6db4476702a' => 'b1c3db9e-0790-4d51-bbb4-fba53e1f40ef', // BRM 600 MOSJØEN 	2025-07-05
        'ecc0fccc-ced8-493d-b671-e3379e2f5743' => '15689abe-ebdd-459a-8209-5b04815af486', // BP 100 GRANÖ GRAVEL 2025-05-31
        '539e737d-8606-41b6-93e4-a81b7a0e901f' => '93e95dd1-1339-4402-8f14-edb6ad42f6b5', // BP 100 BÖNHAMN 2025-05-17
        '47654042-4dc1-42b2-945e-53d7ac035e96' => 'f29e8bcb-f9ad-4a1d-b398-6048554cdfbf', // BRM 600 Två Älvar 2025-07-05 använd samma id på den under för gardering
        'd3107051-7788-4f2e-b63e-9d0e244d452a' => 'f29e8bcb-f9ad-4a1d-b398-6048554cdfbf', // BRM 600 Två Älvar 2025-07-05 använd samma som ovan
        '3d9e0a18-86bd-4e5b-95ad-9f86b81729c7' => 'e54ffabb-466d-43ab-a662-1519ff9ec9e4', // BRM 300 LYCKSELE 2025-05-24
        'd22534a2-bd9c-4463-ab98-d3dc7c765f32' => '5f7f684f-46b0-49f5-8db5-f3e65bdf13d2', // BRM 400 Åmliden 2025-06-07
        '2a8cb207-c9a9-4edf-874d-0fdeff0c302d' => 'dff10e9e-0e37-4092-877a-f8b9306ad054', // BRM 200 BOTSMARK 2025-05-10
        '4cf47163-a5fb-408f-b6a3-a0a788c63406' => '', // BP 300 VÄSTERBOTTEN BREVET 	2025-06-28
        'ca84c085-15bd-4024-ac9d-cb32ec7cf237' => '', // BP 200 VÄSTERBOTTEN BREVET 	2025-06-28
        '442553ec-d323-4bb3-8126-c7d266c9b852' => '', // BP 140 VÄSTERBOTTEN BREVET 	2025-06-28
        '3d03e556-4b5d-43f9-b25a-8d4aea5d53a6' => '', // BP 80 VÄSTERBOTTEN BREVET 	2025-06-28
        '6e04b6c7-3179-4fde-b62c-3972c2260b2a' => '',
        '8cbf3550-6ce2-42c8-a59c-7408cca72502' => '6b2291b7-b8c6-491b-8e3e-be07d29378db',
        '3035676c-4c3f-4222-9977-344b22f08c21' => '3b48ab13-599a-43b8-8fb4-566be94820e3',// Södertörns Pärla 200 2025-03-29
	'8cbf3550-6ce2-42c8-a59c-7408cca72502' => '1f96e70c-7cd8-40a4-9816-57231db08907',// Bromma 200 2025-04-27
	'beeadf7b-974c-4ef0-9c4a-833b5aa9a32a' => '912db6a2-74b7-41a0-9b56-0ae06bfc1333',// Barkarby 200 Järlåsa 2025-04-05
	'86d5e597-9809-4632-a5bd-45d40f826f55' => '1c39a6bc-7175-4f05-832f-9dfa7f57025d',// Vamos ala Öregrund 300 2025-04-18
	'aa776445-afe8-46c0-9733-c5a16cef5fa0' => '46fbccf2-951c-438b-880a-821d5706b7ba',// Täby 200 Fredagsmys 2025-05-02
	'6d4ad865-ff0f-4a04-a666-209bb90f5d74' => '58951ed9-401a-4839-8cc7-84cd898c8eb4'// BRM 200 Artjärvi 2025-03-29

    ];

    public static function get($key)
    {
        return isset(self::$data[$key]) ? self::$data[$key] : null;
    }

    public static function set($key, $value)
    {
        self::$data[$key] = $value;
    }

    public static function getAll()
    {
        return self::$data;
    }
} 