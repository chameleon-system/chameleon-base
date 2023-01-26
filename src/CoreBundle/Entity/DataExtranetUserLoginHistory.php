<?php
namespace ChameleonSystem\CoreBundle\Entity;

class DataExtranetUserLoginHistory {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Corresponding user */
    public readonly \ChameleonSystem\CoreBundle\Entity\DataExtranetUser $dataExtranetUserId, 
    /** Date */
    public readonly \DateTime $datecreated, 
    /** User IP */
    public readonly string $userIp  ) {}
}