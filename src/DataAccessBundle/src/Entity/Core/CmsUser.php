<?php

namespace ChameleonSystem\DataAccessBundle\Entity\Core;

use ChameleonSystem\DataAccessBundle\Entity\CoreMedia\CmsMedia;
use ChameleonSystem\DataAccessBundle\Entity\CoreMenu\CmsMenuItem;
use ChameleonSystem\DataAccessBundle\Entity\CorePortal\CmsPortal;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class CmsUser
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldVarchar
        /** @var string - Login */
        private string $login = '',
        // TCMSFieldPasswordEncrypted
        /** @var string - Password */
        private string $cryptedPw = '',
        // TCMSFieldVarchar
        /** @var string - First name */
        private string $firstname = '',
        // TCMSFieldVarchar
        /** @var string - Last name */
        private string $name = '',
        // TCMSFieldEmail
        /** @var string - Email address */
        private string $email = '',
        // TCMSFieldExtendedLookupMedia
        /** @var CmsMedia|null - Image */
        private ?CmsMedia $images = null,
        // TCMSFieldVarchar
        /** @var string - Company */
        private string $company = '',
        // TCMSFieldVarchar
        /** @var string - Department */
        private string $department = '',
        // TCMSFieldVarchar
        /** @var string - City */
        private string $city = '',
        // TCMSFieldVarchar
        /** @var string - Telephone */
        private string $tel = '',
        // TCMSFieldVarchar
        /** @var string - Fax */
        private string $fax = '',
        // TCMSFieldLookup
        /** @var CmsLanguage|null - CMS language */
        private ?CmsLanguage $cmsLanguage = null,
        // TCMSFieldVarchar
        /** @var string - Alternative languages */
        private string $languages = 'de',
        // TCMSFieldLookupGroups
        /** @var Collection<int, CmsUsergroup> - User groups */
        private Collection $cmsUsergroupCollection = new ArrayCollection(),
        // TCMSFieldLookupRoles
        /** @var Collection<int, CmsRole> - User roles */
        private Collection $cmsRoleCollection = new ArrayCollection(),
        // TCMSFieldLookupMultiselectCheckboxes
        /** @var Collection<int, CmsPortal> - Portal / websites */
        private Collection $cmsPortalCollection = new ArrayCollection(),
        // TCMSFieldLookupMultiselectCheckboxesPossibleLanguages
        /** @var Collection<int, CmsLanguage> - Editing languages */
        private Collection $cmsLanguageCollection = new ArrayCollection(),
        // TCMSFieldVarchar
        /** @var string - Current editing language */
        private string $cmsCurrentEditLanguage = '',
        // TCMSFieldBoolean
        /** @var bool - Allow CMS login */
        private bool $allowCmsLogin = true,
        // TCMSFieldPropertyTable
        /** @var Collection<int, CmsUserSso> - SSO IDs */
        private Collection $cmsUserSsoCollection = new ArrayCollection(),
        // TCMSFieldNumber
        /** @var int - Maximum displayed tasks */
        private int $taskShowCount = 5,
        // TCMSFieldBoolean
        /** @var bool - Required by the system */
        private bool $isSystem = false,
        // TCMSFieldBoolean
        /** @var bool - Can be used as a rights template */
        private bool $showAsRightsTemplate = false,
        // TCMSFieldLookupMultiselect
        /** @var Collection<int, CmsMenuItem> - Used menu entries */
        private Collection $cmsMenuItemCollection = new ArrayCollection(),
        // TCMSFieldTimestamp
        /** @var \DateTime|null - Last modified */
        private ?\DateTime $dateModified = null
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

    // TCMSFieldVarchar
    public function getLogin(): string
    {
        return $this->login;
    }

    public function setLogin(string $login): self
    {
        $this->login = $login;

        return $this;
    }

    // TCMSFieldPasswordEncrypted
    public function getCryptedPw(): string
    {
        return $this->cryptedPw;
    }

    public function setCryptedPw(string $cryptedPw): self
    {
        $this->cryptedPw = $cryptedPw;

        return $this;
    }

    // TCMSFieldVarchar
    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    // TCMSFieldVarchar
    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    // TCMSFieldEmail
    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    // TCMSFieldExtendedLookupMedia
    public function getImages(): ?CmsMedia
    {
        return $this->images;
    }

    public function setImages(?CmsMedia $images): self
    {
        $this->images = $images;

        return $this;
    }

    // TCMSFieldVarchar
    public function getCompany(): string
    {
        return $this->company;
    }

    public function setCompany(string $company): self
    {
        $this->company = $company;

        return $this;
    }

    // TCMSFieldVarchar
    public function getDepartment(): string
    {
        return $this->department;
    }

    public function setDepartment(string $department): self
    {
        $this->department = $department;

        return $this;
    }

    // TCMSFieldVarchar
    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    // TCMSFieldVarchar
    public function getTel(): string
    {
        return $this->tel;
    }

    public function setTel(string $tel): self
    {
        $this->tel = $tel;

        return $this;
    }

    // TCMSFieldVarchar
    public function getFax(): string
    {
        return $this->fax;
    }

    public function setFax(string $fax): self
    {
        $this->fax = $fax;

        return $this;
    }

    // TCMSFieldLookup
    public function getCmsLanguage(): ?CmsLanguage
    {
        return $this->cmsLanguage;
    }

    public function setCmsLanguage(?CmsLanguage $cmsLanguage): self
    {
        $this->cmsLanguage = $cmsLanguage;

        return $this;
    }

    // TCMSFieldVarchar
    public function getLanguages(): string
    {
        return $this->languages;
    }

    public function setLanguages(string $languages): self
    {
        $this->languages = $languages;

        return $this;
    }

    // TCMSFieldLookupGroups

    /**
     * @return Collection<int, CmsUsergroup>
     */
    public function getCmsUsergroupCollection(): Collection
    {
        return $this->cmsUsergroupCollection;
    }

    public function addCmsUsergroupCollection(CmsUsergroup $cmsUsergroupMlt): self
    {
        if (!$this->cmsUsergroupCollection->contains($cmsUsergroupMlt)) {
            $this->cmsUsergroupCollection->add($cmsUsergroupMlt);
            $cmsUsergroupMlt->set($this);
        }

        return $this;
    }

    public function removeCmsUsergroupCollection(CmsUsergroup $cmsUsergroupMlt): self
    {
        if ($this->cmsUsergroupCollection->removeElement($cmsUsergroupMlt)) {
            // set the owning side to null (unless already changed)
            if ($cmsUsergroupMlt->get() === $this) {
                $cmsUsergroupMlt->set(null);
            }
        }

        return $this;
    }

    // TCMSFieldLookupRoles

    /**
     * @return Collection<int, CmsRole>
     */
    public function getCmsRoleCollection(): Collection
    {
        return $this->cmsRoleCollection;
    }

    public function addCmsRoleCollection(CmsRole $cmsRoleMlt): self
    {
        if (!$this->cmsRoleCollection->contains($cmsRoleMlt)) {
            $this->cmsRoleCollection->add($cmsRoleMlt);
            $cmsRoleMlt->set($this);
        }

        return $this;
    }

    public function removeCmsRoleCollection(CmsRole $cmsRoleMlt): self
    {
        if ($this->cmsRoleCollection->removeElement($cmsRoleMlt)) {
            // set the owning side to null (unless already changed)
            if ($cmsRoleMlt->get() === $this) {
                $cmsRoleMlt->set(null);
            }
        }

        return $this;
    }

    // TCMSFieldLookupMultiselectCheckboxes

    /**
     * @return Collection<int, CmsPortal>
     */
    public function getCmsPortalCollection(): Collection
    {
        return $this->cmsPortalCollection;
    }

    public function addCmsPortalCollection(CmsPortal $cmsPortalMlt): self
    {
        if (!$this->cmsPortalCollection->contains($cmsPortalMlt)) {
            $this->cmsPortalCollection->add($cmsPortalMlt);
            $cmsPortalMlt->set($this);
        }

        return $this;
    }

    public function removeCmsPortalCollection(CmsPortal $cmsPortalMlt): self
    {
        if ($this->cmsPortalCollection->removeElement($cmsPortalMlt)) {
            // set the owning side to null (unless already changed)
            if ($cmsPortalMlt->get() === $this) {
                $cmsPortalMlt->set(null);
            }
        }

        return $this;
    }

    // TCMSFieldLookupMultiselectCheckboxesPossibleLanguages

    /**
     * @return Collection<int, CmsLanguage>
     */
    public function getCmsLanguageCollection(): Collection
    {
        return $this->cmsLanguageCollection;
    }

    public function addCmsLanguageCollection(CmsLanguage $cmsLanguageMlt): self
    {
        if (!$this->cmsLanguageCollection->contains($cmsLanguageMlt)) {
            $this->cmsLanguageCollection->add($cmsLanguageMlt);
            $cmsLanguageMlt->set($this);
        }

        return $this;
    }

    public function removeCmsLanguageCollection(CmsLanguage $cmsLanguageMlt): self
    {
        if ($this->cmsLanguageCollection->removeElement($cmsLanguageMlt)) {
            // set the owning side to null (unless already changed)
            if ($cmsLanguageMlt->get() === $this) {
                $cmsLanguageMlt->set(null);
            }
        }

        return $this;
    }

    // TCMSFieldVarchar
    public function getCmsCurrentEditLanguage(): string
    {
        return $this->cmsCurrentEditLanguage;
    }

    public function setCmsCurrentEditLanguage(string $cmsCurrentEditLanguage): self
    {
        $this->cmsCurrentEditLanguage = $cmsCurrentEditLanguage;

        return $this;
    }

    // TCMSFieldBoolean
    public function isAllowCmsLogin(): bool
    {
        return $this->allowCmsLogin;
    }

    public function setAllowCmsLogin(bool $allowCmsLogin): self
    {
        $this->allowCmsLogin = $allowCmsLogin;

        return $this;
    }

    // TCMSFieldPropertyTable

    /**
     * @return Collection<int, CmsUserSso>
     */
    public function getCmsUserSsoCollection(): Collection
    {
        return $this->cmsUserSsoCollection;
    }

    public function addCmsUserSsoCollection(CmsUserSso $cmsUserSso): self
    {
        if (!$this->cmsUserSsoCollection->contains($cmsUserSso)) {
            $this->cmsUserSsoCollection->add($cmsUserSso);
            $cmsUserSso->setCmsUser($this);
        }

        return $this;
    }

    public function removeCmsUserSsoCollection(CmsUserSso $cmsUserSso): self
    {
        if ($this->cmsUserSsoCollection->removeElement($cmsUserSso)) {
            // set the owning side to null (unless already changed)
            if ($cmsUserSso->getCmsUser() === $this) {
                $cmsUserSso->setCmsUser(null);
            }
        }

        return $this;
    }

    // TCMSFieldNumber
    public function getTaskShowCount(): int
    {
        return $this->taskShowCount;
    }

    public function setTaskShowCount(int $taskShowCount): self
    {
        $this->taskShowCount = $taskShowCount;

        return $this;
    }

    // TCMSFieldBoolean
    public function isIsSystem(): bool
    {
        return $this->isSystem;
    }

    public function setIsSystem(bool $isSystem): self
    {
        $this->isSystem = $isSystem;

        return $this;
    }

    // TCMSFieldBoolean
    public function isShowAsRightsTemplate(): bool
    {
        return $this->showAsRightsTemplate;
    }

    public function setShowAsRightsTemplate(bool $showAsRightsTemplate): self
    {
        $this->showAsRightsTemplate = $showAsRightsTemplate;

        return $this;
    }

    // TCMSFieldLookupMultiselect

    /**
     * @return Collection<int, CmsMenuItem>
     */
    public function getCmsMenuItemCollection(): Collection
    {
        return $this->cmsMenuItemCollection;
    }

    public function addCmsMenuItemCollection(CmsMenuItem $cmsMenuItemMlt): self
    {
        if (!$this->cmsMenuItemCollection->contains($cmsMenuItemMlt)) {
            $this->cmsMenuItemCollection->add($cmsMenuItemMlt);
            $cmsMenuItemMlt->set($this);
        }

        return $this;
    }

    public function removeCmsMenuItemCollection(CmsMenuItem $cmsMenuItemMlt): self
    {
        if ($this->cmsMenuItemCollection->removeElement($cmsMenuItemMlt)) {
            // set the owning side to null (unless already changed)
            if ($cmsMenuItemMlt->get() === $this) {
                $cmsMenuItemMlt->set(null);
            }
        }

        return $this;
    }

    // TCMSFieldTimestamp
    public function getDateModified(): ?\DateTime
    {
        return $this->dateModified;
    }

    public function setDateModified(?\DateTime $dateModified): self
    {
        $this->dateModified = $dateModified;

        return $this;
    }
}
