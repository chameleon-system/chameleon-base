<?php
namespace ChameleonSystem\CoreBundle\Entity;

class PkgNewsletterModuleSignoutConfig {
  public function __construct(
    public readonly string $id,
    public readonly int $cmsident,
    /** Belongs to module instance */
    public readonly \ChameleonSystem\CoreBundle\Entity\CmsTplModuleInstance $cmsTplModuleInstanceId, 
    /** Signout (title) */
    public readonly string $signoutTitle, 
    /** Signout (text) */
    public readonly string $signoutText, 
    /** Signout confirmation (title) */
    public readonly string $signoutConfirmTitle, 
    /** Signout confirmation (text) */
    public readonly string $signoutConfirmText, 
    /** Signed out (title) */
    public readonly string $signedoutTitle, 
    /** Signed out (text) */
    public readonly string $signedoutText, 
    /** No newsletter signed up for (title) */
    public readonly string $noNewsletterSignedup, 
    /** No newsletter signed up for (text) */
    public readonly string $noNewsletterSignedupText, 
    /** Use double opt-out */
    public readonly bool $useDoubleOptOut  ) {}
}