<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgCmsResultCache {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Owner identification */
    public readonly string $ownerHash, 
    /** Identification */
    public readonly string $hash, 
    /** Creation date */
    public readonly \DateTime $dateCreated, 
    /** Entry invalid from */
    public readonly \DateTime $dateExpireAfter, 
    /** Content */
    public readonly string $data, 
    /** Delete if invalid */
    public readonly bool $garbageCollectWhenExpired  ) {}
}