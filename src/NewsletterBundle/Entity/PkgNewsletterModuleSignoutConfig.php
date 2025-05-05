<?php

namespace ChameleonSystem\NewsletterBundle\Entity;

use ChameleonSystem\DataAccessBundle\Entity\CorePagedef\CmsTplModuleInstance;

class PkgNewsletterModuleSignoutConfig
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldLookupParentID
        /** @var CmsTplModuleInstance|null - Belongs to module instance */
        private ?CmsTplModuleInstance $cmsTplModuleInstance = null,
        // TCMSFieldVarchar
        /** @var string - Signout (title) */
        private string $signoutTitle = '',
        // TCMSFieldWYSIWYG
        /** @var string - Signout (text) */
        private string $signoutText = '',
        // TCMSFieldVarchar
        /** @var string - Signout confirmation (title) */
        private string $signoutConfirmTitle = '',
        // TCMSFieldWYSIWYG
        /** @var string - Signout confirmation (text) */
        private string $signoutConfirmText = '',
        // TCMSFieldVarchar
        /** @var string - Signed out (title) */
        private string $signedoutTitle = '',
        // TCMSFieldWYSIWYG
        /** @var string - Signed out (text) */
        private string $signedoutText = '',
        // TCMSFieldVarchar
        /** @var string - No newsletter signed up for (title) */
        private string $noNewsletterSignedup = '',
        // TCMSFieldWYSIWYG
        /** @var string - No newsletter signed up for (text) */
        private string $noNewsletterSignedupText = '',
        // TCMSFieldBoolean
        /** @var bool - Use double opt-out */
        private bool $useDoubleOptOut = false
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getCmsident(): ?int
    {
        return $this->cmsident;
    }

    public function setCmsident(int $cmsident): self
    {
        $this->cmsident = $cmsident;

        return $this;
    }

    // TCMSFieldLookupParentID
    public function getCmsTplModuleInstance(): ?CmsTplModuleInstance
    {
        return $this->cmsTplModuleInstance;
    }

    public function setCmsTplModuleInstance(?CmsTplModuleInstance $cmsTplModuleInstance): self
    {
        $this->cmsTplModuleInstance = $cmsTplModuleInstance;

        return $this;
    }

    // TCMSFieldVarchar
    public function getSignoutTitle(): string
    {
        return $this->signoutTitle;
    }

    public function setSignoutTitle(string $signoutTitle): self
    {
        $this->signoutTitle = $signoutTitle;

        return $this;
    }

    // TCMSFieldWYSIWYG
    public function getSignoutText(): string
    {
        return $this->signoutText;
    }

    public function setSignoutText(string $signoutText): self
    {
        $this->signoutText = $signoutText;

        return $this;
    }

    // TCMSFieldVarchar
    public function getSignoutConfirmTitle(): string
    {
        return $this->signoutConfirmTitle;
    }

    public function setSignoutConfirmTitle(string $signoutConfirmTitle): self
    {
        $this->signoutConfirmTitle = $signoutConfirmTitle;

        return $this;
    }

    // TCMSFieldWYSIWYG
    public function getSignoutConfirmText(): string
    {
        return $this->signoutConfirmText;
    }

    public function setSignoutConfirmText(string $signoutConfirmText): self
    {
        $this->signoutConfirmText = $signoutConfirmText;

        return $this;
    }

    // TCMSFieldVarchar
    public function getSignedoutTitle(): string
    {
        return $this->signedoutTitle;
    }

    public function setSignedoutTitle(string $signedoutTitle): self
    {
        $this->signedoutTitle = $signedoutTitle;

        return $this;
    }

    // TCMSFieldWYSIWYG
    public function getSignedoutText(): string
    {
        return $this->signedoutText;
    }

    public function setSignedoutText(string $signedoutText): self
    {
        $this->signedoutText = $signedoutText;

        return $this;
    }

    // TCMSFieldVarchar
    public function getNoNewsletterSignedup(): string
    {
        return $this->noNewsletterSignedup;
    }

    public function setNoNewsletterSignedup(string $noNewsletterSignedup): self
    {
        $this->noNewsletterSignedup = $noNewsletterSignedup;

        return $this;
    }

    // TCMSFieldWYSIWYG
    public function getNoNewsletterSignedupText(): string
    {
        return $this->noNewsletterSignedupText;
    }

    public function setNoNewsletterSignedupText(string $noNewsletterSignedupText): self
    {
        $this->noNewsletterSignedupText = $noNewsletterSignedupText;

        return $this;
    }

    // TCMSFieldBoolean
    public function isUseDoubleOptOut(): bool
    {
        return $this->useDoubleOptOut;
    }

    public function setUseDoubleOptOut(bool $useDoubleOptOut): self
    {
        $this->useDoubleOptOut = $useDoubleOptOut;

        return $this;
    }
}
