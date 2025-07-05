<?php

namespace App\Domain\Model\Partisipant\Rest;

use App\common\Rest\Link;
use Psr\Container\ContainerInterface;

class MoveParticipantResponseAssembly
{
    private $settings;

    public function __construct(ContainerInterface $c)
    {
        $this->settings = $c->get('settings');
    }

    /**
     * Create a move participant response with HATEOAS links for conflict resolution
     */
    public function toMoveResponse(array $results): MoveParticipantResponseRepresentation
    {
        $response = new MoveParticipantResponseRepresentation();
        
        // Add successful moves
        $response->setSuccess($results['success'] ?? []);
        
        // Add failed moves with HATEOAS links
        $failedWithLinks = [];
        if (isset($results['failed'])) {
            foreach ($results['failed'] as $failed) {
                $failedItem = $failed;
                
                // Add HATEOAS links for start number conflicts
                if (strpos($failed['reason'], 'start number') !== false || strpos($failed['reason'], 'already exists') !== false) {
                    $failedItem['links'] = [
                        new Link(
                            'resolveStartnumberConflict',
                            'POST',
                            $this->settings['path'] . 'participant/' . $failed['participant_uid'] . '/resolve-startnumber-conflict'
                        )
                    ];
                }
                
                $failedWithLinks[] = $failedItem;
            }
        }
        $response->setFailed($failedWithLinks);
        
        // Add skipped moves
        $response->setSkipped($results['skipped'] ?? []);
        
        return $response;
    }

    /**
     * Create a single participant move response with HATEOAS links
     */
    public function toSingleMoveResponse(string $participant_uid, string $reason, string $target_track_uid = null): MoveParticipantResponseRepresentation
    {
        $response = new MoveParticipantResponseRepresentation();
        
        // Check if it's a start number conflict
        if (strpos($reason, 'start number') !== false || strpos($reason, 'already exists') !== false) {
            $response->setFailed([
                [
                    'participant_uid' => $participant_uid,
                    'reason' => $reason,
                    'links' => [
                        new Link(
                            'resolveStartnumberConflict',
                            'POST',
                            $this->settings['path'] . 'participant/' . $participant_uid . '/resolve-startnumber-conflict'
                        )
                    ]
                ]
            ]);
        } else {
            $response->setFailed([
                [
                    'participant_uid' => $participant_uid,
                    'reason' => $reason
                ]
            ]);
        }
        
        return $response;
    }
} 