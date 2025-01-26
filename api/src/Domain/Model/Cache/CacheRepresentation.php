<?php

namespace App\Domain\Model\Cache;

use JsonSerializable;

class CacheRepresentation  implements JsonSerializable
{

    private $id;
    private $organizer_id;
    private $svg_blob;
    private $created_at;
    private $updated_at;
    private array $links = [];

    /**
     * @return mixed
     */
    public function getSvgBlob()
    {
        return $this->svg_blob;
    }

    /**
     * @param mixed $svg_blob
     */
    public function setSvgBlob($svg_blob): void
    {
        $this->svg_blob = $svg_blob;
    }

    /**
     * @return mixed
     */
    public function getOrganizerId()
    {
        return $this->organizer_id;
    }

    /**
     * @param mixed $organizer_id
     */
    public function setOrganizerId($organizer_id): void
    {
        $this->organizer_id = $organizer_id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return array
     */
    public function getLinks(): array
    {
        return $this->links;
    }

    /**
     * @param array $links
     */
    public function setLinks(array $links): void
    {
        $this->links = $links;
    }



    public function jsonSerialize(): object
    {
        return (object)get_object_vars($this);
    }


}