<?php

namespace App\Domain\Model\Partisipant\Rest;

use App\common\Rest\Link;
use App\Domain\Model\Club\Rest\ClubRepresentation;
use App\Domain\Model\Competitor\Rest\CompetitorInforepresentation;
use App\Domain\Model\Competitor\Rest\CompetitorRepresentation;
use Karriere\JsonDecoder\Bindings\FieldBinding;
use Karriere\JsonDecoder\ClassBindings;
use Karriere\JsonDecoder\Transformer;

class ParticipantInformationRepresentationTransformer  implements Transformer
{

    public function register(ClassBindings $classBindings)
    {
        $classBindings->register(new FieldBinding('link', 'link', Link::class));
        $classBindings->register(new FieldBinding('participant', 'participant', ParticipantRepresentation::class));
        $classBindings->register(new FieldBinding('competitorRepresentation', 'competitorRepresentation', CompetitorRepresentation::class));
        $classBindings->register(new FieldBinding('competitorInforepresentation', 'competitorInforepresentation', CompetitorInforepresentation::class));
        $classBindings->register(new FieldBinding('clubRepresentation', 'clubRepresentation', ClubRepresentation::class));
    }

    public function transforms()
    {
        return ParticipantInformationRepresentation::class;
    }
}