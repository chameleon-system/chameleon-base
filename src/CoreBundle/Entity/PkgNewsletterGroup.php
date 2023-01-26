<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgNewsletterGroup {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Logo header image of newsletter */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsMedia $logoHeader, 
    /** From (name) */
    public readonly string $fromName, 
    /** Reply email address */
    public readonly string $replyEmail, 
    /** Name of the recipient list */
    public readonly string $name, 
    /** From (email address) */
    public readonly string $fromEmail, 
    /** Imprint */
    public readonly string $imprint, 
    /** Portal */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsPortal $cmsPortalId, 
    /** Send to all newsletter users */
    public readonly bool $includeAllNewsletterUsers, 
    /** Newsletter users without assignment to a newsletter group */
    public readonly bool $includeNewsletterUserNotAssignedToAnyGroup, 
    /** Include all newsletter users WITHOUT extranet account in the list */
    public readonly bool $includeAllNewsletterUsersWithNoExtranetAccount, 
    /** @var \ChameleonSystem\CoreBundle\Entity\DataExtranetGroup[] Send to users with following extranet groups */
    public readonly array $dataExtranetGroupMlt  ) {}
}