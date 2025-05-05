<?php

namespace ChameleonSystem\NewsletterBundle\Entity;

use ChameleonSystem\DataAccessBundle\Entity\CorePagedef\CmsTplModuleInstance;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class PkgNewsletterModuleSignupConfig
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldLookupParentID
        /** @var CmsTplModuleInstance|null - Belongs to module instance */
        private ?CmsTplModuleInstance $cmsTplModuleInstance = null,
        // TCMSFieldModuleInstance
        /** @var CmsTplModuleInstance|null - Belongs to newsletter module */
        private ?CmsTplModuleInstance $mainModuleInstance = null,
        // TCMSFieldLookupMultiselectCheckboxes
        /** @var Collection<int, PkgNewsletterGroup> - Subscription possible for */
        private Collection $pkgNewsletterGroupCollection = new ArrayCollection(),
        // TCMSFieldBoolean
        /** @var bool - Use double opt-in */
        private bool $useDoubleoptin = true,
        // TCMSFieldVarchar
        /** @var string - Signup (title) */
        private string $signupHeadline = '',
        // TCMSFieldWYSIWYG
        /** @var string - Signup  (text) */
        private string $signupText = '',
        // TCMSFieldVarchar
        /** @var string - Confirmation (title) */
        private string $confirmTitle = '',
        // TCMSFieldWYSIWYG
        /** @var string - Confirmation (text) */
        private string $confirmText = '',
        // TCMSFieldVarchar
        /** @var string - Successful subscription (title) */
        private string $signedupHeadline = '',
        // TCMSFieldWYSIWYG
        /** @var string - Successful subscription (text) */
        private string $signedupText = '',
        // TCMSFieldVarchar
        /** @var string - Signup not possible anymore (title) */
        private string $nonewsignupTitle = '',
        // TCMSFieldWYSIWYG
        /** @var string - Signup not possible anymore (text) */
        private string $nonewsignupText = ''
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

    // TCMSFieldModuleInstance
    public function getMainModuleInstance(): ?CmsTplModuleInstance
    {
        return $this->mainModuleInstance;
    }

    public function setMainModuleInstance(?CmsTplModuleInstance $mainModuleInstance): self
    {
        $this->mainModuleInstance = $mainModuleInstance;

        return $this;
    }

    // TCMSFieldLookupMultiselectCheckboxes

    /**
     * @return Collection<int, PkgNewsletterGroup>
     */
    public function getPkgNewsletterGroupCollection(): Collection
    {
        return $this->pkgNewsletterGroupCollection;
    }

    public function addPkgNewsletterGroupCollection(PkgNewsletterGroup $pkgNewsletterGroupMlt): self
    {
        if (!$this->pkgNewsletterGroupCollection->contains($pkgNewsletterGroupMlt)) {
            $this->pkgNewsletterGroupCollection->add($pkgNewsletterGroupMlt);
            $pkgNewsletterGroupMlt->set($this);
        }

        return $this;
    }

    public function removePkgNewsletterGroupCollection(PkgNewsletterGroup $pkgNewsletterGroupMlt): self
    {
        if ($this->pkgNewsletterGroupCollection->removeElement($pkgNewsletterGroupMlt)) {
            // set the owning side to null (unless already changed)
            if ($pkgNewsletterGroupMlt->get() === $this) {
                $pkgNewsletterGroupMlt->set(null);
            }
        }

        return $this;
    }

    // TCMSFieldBoolean
    public function isUseDoubleoptin(): bool
    {
        return $this->useDoubleoptin;
    }

    public function setUseDoubleoptin(bool $useDoubleoptin): self
    {
        $this->useDoubleoptin = $useDoubleoptin;

        return $this;
    }

    // TCMSFieldVarchar
    public function getSignupHeadline(): string
    {
        return $this->signupHeadline;
    }

    public function setSignupHeadline(string $signupHeadline): self
    {
        $this->signupHeadline = $signupHeadline;

        return $this;
    }

    // TCMSFieldWYSIWYG
    public function getSignupText(): string
    {
        return $this->signupText;
    }

    public function setSignupText(string $signupText): self
    {
        $this->signupText = $signupText;

        return $this;
    }

    // TCMSFieldVarchar
    public function getConfirmTitle(): string
    {
        return $this->confirmTitle;
    }

    public function setConfirmTitle(string $confirmTitle): self
    {
        $this->confirmTitle = $confirmTitle;

        return $this;
    }

    // TCMSFieldWYSIWYG
    public function getConfirmText(): string
    {
        return $this->confirmText;
    }

    public function setConfirmText(string $confirmText): self
    {
        $this->confirmText = $confirmText;

        return $this;
    }

    // TCMSFieldVarchar
    public function getSignedupHeadline(): string
    {
        return $this->signedupHeadline;
    }

    public function setSignedupHeadline(string $signedupHeadline): self
    {
        $this->signedupHeadline = $signedupHeadline;

        return $this;
    }

    // TCMSFieldWYSIWYG
    public function getSignedupText(): string
    {
        return $this->signedupText;
    }

    public function setSignedupText(string $signedupText): self
    {
        $this->signedupText = $signedupText;

        return $this;
    }

    // TCMSFieldVarchar
    public function getNonewsignupTitle(): string
    {
        return $this->nonewsignupTitle;
    }

    public function setNonewsignupTitle(string $nonewsignupTitle): self
    {
        $this->nonewsignupTitle = $nonewsignupTitle;

        return $this;
    }

    // TCMSFieldWYSIWYG
    public function getNonewsignupText(): string
    {
        return $this->nonewsignupText;
    }

    public function setNonewsignupText(string $nonewsignupText): self
    {
        $this->nonewsignupText = $nonewsignupText;

        return $this;
    }
}
