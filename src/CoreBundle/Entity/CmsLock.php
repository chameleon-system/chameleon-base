<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsLock {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Record ID */
    public readonly string $recordid, 
    /** Editor */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsUser $cmsUserId, 
    /** last changed by */
    public readonly \DateTime $timeStamp, 
    /** Lock table */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTblConf $cmsTblConfId  ) {}
}