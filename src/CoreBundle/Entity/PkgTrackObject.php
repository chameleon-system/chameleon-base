<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgTrackObject {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /**  */
    public readonly string $count, 
    /**  */
    public readonly string $tableName, 
    /**  */
    public readonly string $ownerId, 
    /**  */
    public readonly \DateTime $datecreated, 
    /**  */
    public readonly string $timeBlock  ) {}
}