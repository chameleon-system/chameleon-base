<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsUrlAlias {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to portal */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsPortal $cmsPortalId, 
    /** Name / notes */
    public readonly string $name, 
    /** Source */
    public readonly string $sourceUrl, 
    /** Exact match of the source path */
    public readonly bool $exactMatch, 
    /** Target */
    public readonly string $targetUrl, 
    /** Ignore these parameters */
    public readonly string $ignoreParameter, 
    /** Parameter mapping */
    public readonly string $parameterMapping, 
    /** Created by */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsUser $cmsUserId, 
    /** Creation date */
    public readonly \DateTime $datecreated, 
    /** Expiry date */
    public readonly \DateTime $expirationDate, 
    /** Active */
    public readonly bool $active  ) {}
}