<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgNewsletterModuleSignupConfig {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to module instance */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance $cmsTplModuleInstanceId, 
    /** Belongs to newsletter module */
    public readonly \ChameleonSystem\CoreBundle\Entity\MainModuleInstance $mainModuleInstanceId, 
    /** @var \ChameleonSystem\CoreBundle\Entity\PkgNewsletterGroup[] Subscription possible for */
    public readonly array $pkgNewsletterGroupMlt, 
    /** Use double opt-in */
    public readonly bool $useDoubleoptin, 
    /** Signup (title) */
    public readonly string $signupHeadline, 
    /** Signup  (text) */
    public readonly string $signupText, 
    /** Confirmation (title) */
    public readonly string $confirmTitle, 
    /** Confirmation (text) */
    public readonly string $confirmText, 
    /** Successful subscription (title) */
    public readonly string $signedupHeadline, 
    /** Successful subscription (text) */
    public readonly string $signedupText, 
    /** Signup not possible anymore (title) */
    public readonly string $nonewsignupTitle, 
    /** Signup not possible anymore (text) */
    public readonly string $nonewsignupText  ) {}
}