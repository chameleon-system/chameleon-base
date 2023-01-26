<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgRunFrontendAction {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Class */
    public readonly string $class, 
    /**  */
    public readonly string $randomKey, 
    /** Expiry date */
    public readonly \DateTime $expireDate, 
    /**  */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsPortal $cmsPortalId, 
    /**  */
    public readonly string $parameter, 
    /** Language */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsLanguage $cmsLanguageId  ) {}
}