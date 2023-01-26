<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgCmsChangelogItem {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Changeset */
    public readonly \ChameleonSystem\CoreBundle\Entity\PkgCmsChangelogSet $pkgCmsChangelogSetId, 
    /** Changed field */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsFieldConf $cmsFieldConf, 
    /** Old value */
    public readonly string $valueOld, 
    /** New value */
    public readonly string $valueNew  ) {}
}