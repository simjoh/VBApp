-- Migration: Update users table with organizer_id data
-- Date: 2025-07-02
-- Description: Update existing users with their corresponding organizer_id based on provided mapping

-- Update users with organizer_id = 1
UPDATE `users` SET `organizer_id` = 1 WHERE `user_uid` IN (
    '0bd34c22-c30e-4229-9a98-97d5d91a4be3',
    '78a8fd17-dcc1-497d-b12e-5242102643a5',
    'ff672d62-ea59-46b9-84a2-ac57671eb099'
);

-- Update users with organizer_id = 2
UPDATE `users` SET `organizer_id` = 2 WHERE `user_uid` IN (
    '0996857e-fb2f-4246-94a6-bef45a286938',
    '3d138ea5-eda0-443f-9f64-cf3518b5c4c1',
    '3f23a9c1-7b09-4de6-8933-024e4a592e01',
    '496b1f53-bf30-450a-b98d-a2c3bd775e39',
    '57b2804a-3230-4308-8e78-c4829e6fc8a8',
    '5c6be410-b1ed-42b9-8bb4-68f780b62085',
    '6bf2e3a2-a9cf-47bb-99ad-722c41c62066',
    '72c78fb9-158a-4162-8206-769217327e52',
    '73a7b8d6-9d34-42d0-b6e8-f20423c9d2f9',
    '7b4b3130-93f7-4a4a-9e8f-16011575c7b2',
    '8181e6e5-ad2b-4df1-b5d0-f587863672b6',
    '82fbb2ec-d998-4b8a-861f-46f2b0fdbc4e',
    '8573bf24-d88c-4590-b2a0-d6e6458a8e2e',
    '9ca3684e-4903-4e50-977e-1e7eaf9c6859',
    '9e4eb941-bd20-4a25-b348-18922033c0f7',
    '9f604a3b-e108-4836-810d-5909d5bbfa04',
    'd10bc12f-b5fd-4076-88b3-f1c19f5965a2',
    'e1bf5a89-e6c3-4470-9bfe-4ced574c69c0',
    'e4b1256f-8311-4c0d-9696-a1224cd67be1',
    'e8e8749f-4576-4aee-9516-923c65aa9d86',
    'f962d1b8-2aca-4e96-9e83-ce96353f27f0'
);

-- Update users with organizer_id = 4
UPDATE `users` SET `organizer_id` = 4 WHERE `user_uid` IN (
    '76af00f4-8525-495e-b2fe-b231a44b2859'
);

-- Update users with organizer_id = 9
UPDATE `users` SET `organizer_id` = 9 WHERE `user_uid` IN (
    'a3357a6f-7ac5-4d6f-a4f9-b9b4734b967d'
);

-- Update users with organizer_id = 11
UPDATE `users` SET `organizer_id` = 11 WHERE `user_uid` IN (
    '056a08e1-41d7-416c-8cd8-c5f7c0024ec4'
);

-- Add a comment to document this migration
-- This migration updates the organizer_id for existing users based on the provided mapping
-- Total users updated: 26 users across 5 different organizers 