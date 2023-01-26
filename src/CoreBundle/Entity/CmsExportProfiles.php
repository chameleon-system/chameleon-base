<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsExportProfiles {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Profile name */
    public readonly string $name, 
    /** Editorial department */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsPortal $cmsPortalId, 
    /** Export format */
    public readonly string $exportType, 
    /** Table */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTblConf $cmsTblConfId, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsExportProfilesFields[] Fields to be exported */
    public readonly array $cmsExportProfilesFields  ) {}
}