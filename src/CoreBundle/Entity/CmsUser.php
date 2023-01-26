<?php
namespace ChameleonSystem\CoreBundle\Entity;

class CmsUser {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Login */
    public readonly string $login, 
    /** Password */
    public readonly string $cryptedPw, 
    /** First name */
    public readonly string $firstname, 
    /** Last name */
    public readonly string $name, 
    /** Email address */
    public readonly string $email, 
    /** Image */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsMedia $images, 
    /** Company */
    public readonly string $company, 
    /** Department */
    public readonly string $department, 
    /** City */
    public readonly string $city, 
    /** Telephone */
    public readonly string $tel, 
    /** Fax */
    public readonly string $fax, 
    /** CMS language */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsLanguage $cmsLanguageId, 
    /** Alternative languages */
    public readonly string $languages, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsUsergroup[] User groups */
    public readonly array $cmsUsergroupMlt, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsRole[] User roles */
    public readonly array $cmsRoleMlt, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsPortal[] Portal / websites */
    public readonly array $cmsPortalMlt, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsLanguage[] Editing languages */
    public readonly array $cmsLanguageMlt, 
    /** Current editing language */
    public readonly string $cmsCurrentEditLanguage, 
    /** Allow CMS login */
    public readonly bool $allowCmsLogin, 
    /** Maximum displayed tasks */
    public readonly string $taskShowCount, 
    /** Required by the system */
    public readonly bool $isSystem, 
    /** Active transactions */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsWorkflowTransaction $cmsWorkflowTransactionId, 
    /** Can be used as a rights template */
    public readonly bool $showAsRightsTemplate, 
    /** @var \ChameleonSystem\CoreBundle\Entity\CmsMenuItem[] Used menu entries */
    public readonly array $cmsMenuItemMlt, 
    /**  */
    public readonly \DateTime $dateModified, 
    /** Google User ID */
    public readonly string $googleId  ) {}
}