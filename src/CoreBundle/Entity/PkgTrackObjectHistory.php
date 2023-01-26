<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgTrackObjectHistory {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /**  */
    public readonly string $tableName, 
    /**  */
    public readonly string $ownerId, 
    /**  */
    public readonly \DateTime $datecreated, 
    /**  */
    public readonly \ChameleonSystem\CoreBundle\Entity\DataExtranetUser $dataExtranetUserId, 
    /**  */
    public readonly string $sessionId, 
    /**  */
    public readonly string $ip, 
    /**  */
    public readonly string $requestChecksum, 
    /**  */
    public readonly \ChameleonSystem\CoreBundle\Entity\PkgTrackObject $pkgTrackObjectId, 
    /**  */
    public readonly bool $itemCounted  ) {}
}