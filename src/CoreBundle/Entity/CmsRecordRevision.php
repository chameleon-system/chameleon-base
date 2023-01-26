<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsRecordRevision {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** belongs to revision */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsRecordRevision $cmsRecordRevisionId, 
    /** Table */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTblConf $cmsTblConfId, 
    /** Record ID */
    public readonly string $recordid, 
    /** Title */
    public readonly string $name, 
    /** Description */
    public readonly string $description, 
    /** Version number */
    public readonly string $revisionNr, 
    /** Editor */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsUser $cmsUserId, 
    /** Created on */
    public readonly \DateTime $createTimestamp, 
    /** Time of last activation */
    public readonly \DateTime $lastActiveTimestamp, 
    /** Serialized record */
    public readonly string $data  ) {}
}