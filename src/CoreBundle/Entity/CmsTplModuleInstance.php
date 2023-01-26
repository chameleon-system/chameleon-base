<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsTplModuleInstance {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Instance name */
    public readonly string $name, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsTplPageCmsMasterPagedefSpot[] CMS pages dynamic spots */
    public readonly array $cmsTplPageCmsMasterPagedefSpot, 
    /** was created in portal */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsPortal $cmsPortalId, 
    /** File name of the module template */
    public readonly string $template, 
    /** Module ID */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTplModule $cmsTplModuleId  ) {}
}