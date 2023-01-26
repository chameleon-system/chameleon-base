<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsPortalDomains {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Portal */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsPortal $cmsPortalId, 
    /** Domain name */
    public readonly string $name, 
    /** SSL domain name */
    public readonly string $sslname, 
    /** Language */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsLanguage $cmsLanguageId, 
    /** Primary domain of the portal */
    public readonly bool $isMasterDomain, 
    /** Google API key */
    public readonly string $googleApiKey  ) {}
}