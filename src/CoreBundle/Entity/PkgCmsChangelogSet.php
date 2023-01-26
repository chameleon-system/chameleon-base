<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgCmsChangelogSet {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Change date */
    public readonly \DateTime $modifyDate, 
    /** User who made the change */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsUser $cmsUser, 
    /** The main table that was changed */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTblConf $cmsTblConf, 
    /** ID of the changed data record */
    public readonly string $modifiedId, 
    /** Name of the changed data record */
    public readonly string $modifiedName, 
    /** Type of change (INSERT, UPDATE, DELETE) */
    public readonly string $changeType, 
    /** @var \ChameleonSystem\CoreBundle\Entity\PkgCmsChangelogItem[] Changes */
    public readonly array $pkgCmsChangelogItem  ) {}
}