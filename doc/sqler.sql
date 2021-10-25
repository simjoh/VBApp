--- Cyklists kontroller i en brevet
SELECT * FROM `v_partisipant_to_pass_checkpoint` WHERE participant_uid = 'e8f9557e-4b96-41a0-b6c7-be6c45d81259'

-- Cyklister som ska passera en viss kontroll i detta fall brännäset för en viss bana
SELECT * FROM `v_partisipant_to_pass_checkpoint` WHERE track_uid = '8a5a0649-6aee-4b64-803e-4f083f746d2d' and checkpoint_uid = '63e8f1de-22ad-416d-b181-4a1a004a2959';