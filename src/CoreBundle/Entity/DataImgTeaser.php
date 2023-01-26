<?php
namespace ChameleonSystem\CoreBundle\Entity;

class DataImgTeaser {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to module instance */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance $cmsTplModuleInstanceId, 
    /** Title */
    public readonly string $name, 
    /** ALT text */
    public readonly string $altText, 
    /** Link */
    public readonly string $link, 
    /** Image */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsMedia $cmsMediaId  ) {}
}