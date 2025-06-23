<?php

namespace App\Domain\Model\Organizer\Rest;

use App\common\Rest\Link;
use Karriere\JsonDecoder\Bindings\FieldBinding;
use Karriere\JsonDecoder\ClassBindings;
use Karriere\JsonDecoder\Transformer;

class OrganizerRepresentationTransformer implements Transformer
{
    public function register(ClassBindings $classBindings)
    {
        $classBindings->register(new FieldBinding('id', 'id', OrganizerRepresentation::class));
        $classBindings->register(new FieldBinding('organization_name', 'organization_name', OrganizerRepresentation::class));
        $classBindings->register(new FieldBinding('description', 'description', OrganizerRepresentation::class));
        $classBindings->register(new FieldBinding('website', 'website', OrganizerRepresentation::class));
        $classBindings->register(new FieldBinding('website_pay', 'website_pay', OrganizerRepresentation::class));
        $classBindings->register(new FieldBinding('logo_svg', 'logo_svg', OrganizerRepresentation::class));
        $classBindings->register(new FieldBinding('contact_person_name', 'contact_person_name', OrganizerRepresentation::class));
        $classBindings->register(new FieldBinding('email', 'email', OrganizerRepresentation::class));
        $classBindings->register(new FieldBinding('active', 'active', OrganizerRepresentation::class));
        $classBindings->register(new FieldBinding('club_uid', 'club_uid', OrganizerRepresentation::class));
        $classBindings->register(new FieldBinding('link', 'link', Link::class));
    }

    public function transforms()
    {
        return OrganizerRepresentation::class;
    }
} 