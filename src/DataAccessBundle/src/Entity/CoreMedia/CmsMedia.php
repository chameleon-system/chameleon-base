<?php

namespace ChameleonSystem\DataAccessBundle\Entity\CoreMedia;

use ChameleonSystem\DataAccessBundle\Entity\Core\CmsTags;
use ChameleonSystem\DataAccessBundle\Entity\Core\CmsUser;
use ChameleonSystem\DataAccessBundle\Entity\CorePagedef\CmsTree;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class CmsMedia
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldNumber
        /** @var int - Height */
        private int $height = 0,
        // TCMSFieldLookup
        /** @var CmsFiletype|null - Image type */
        private ?CmsFiletype $cmsFiletype = null,
        // TCMSFieldNumber
        /** @var int - File size */
        private int $filesize = 0,
        // TCMSFieldTreeNode
        /** @var CmsTree|null - Image category */
        private ?CmsTree $cmsMediaTree = null,
        // TCMSFieldNumber
        /** @var int - Width */
        private int $width = 0,
        // TCMSFieldVarchar
        /** @var string - Title / Description */
        private string $description = '',
        // TCMSFieldText
        /** @var string - Keywords / Tags */
        private string $metatags = '',
        // TCMSFieldVarchar
        /** @var string - Supported file types */
        private string $filetypes = '',
        // TCMSFieldVarchar
        /** @var string - Alt tag */
        private string $altTag = '',
        // TCMSFieldVarchar
        /** @var string - Systemname */
        private string $systemname = '',
        // TCMSFieldLookupMultiselectTags
        /** @var Collection<int, CmsTags> - Tags */
        private Collection $cmsTagsCollection = new ArrayCollection(),
        // TCMSFieldVarchar
        /** @var string - Custom file name */
        private string $customFilename = '',
        // TCMSFieldMediaPath
        /** @var string - Path */
        private string $path = '',
        // TCMSFieldExtendedLookupMedia
        /** @var CmsMedia|null - Preview image */
        private ?CmsMedia $cmsMedia = null,
        // TCMSFieldExternalVideoCode
        /** @var string - Video HTML code */
        private string $externalEmbedCode = '',
        // TCMSFieldText
        /** @var string - Thumbnail of an external video */
        private string $externalVideoThumbnail = '',
        // TCMSFieldTimestamp
        /** @var \DateTime|null - Last changed on */
        private ?\DateTime $timeStamp = null,
        // TCMSFieldDateTimeNow
        /** @var \DateTime|null - Last changed */
        private ?\DateTime $dateChanged = new \DateTime(),
        // TCMSFieldVarchar
        /** @var string - Refresh Token */
        private string $refreshToken = '',
        // TCMSFieldCMSUser
        /** @var CmsUser|null - Last changed by */
        private ?CmsUser $cmsUser = null,
        // TCMSFieldExternalVideoID
        /** @var string - Video ID with external host */
        private string $externalVideoId = ''
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

    // TCMSFieldNumber
    public function getHeight(): int
    {
        return $this->height;
    }

    public function setHeight(int $height): self
    {
        $this->height = $height;

        return $this;
    }

    // TCMSFieldLookup
    public function getCmsFiletype(): ?CmsFiletype
    {
        return $this->cmsFiletype;
    }

    public function setCmsFiletype(?CmsFiletype $cmsFiletype): self
    {
        $this->cmsFiletype = $cmsFiletype;

        return $this;
    }

    // TCMSFieldNumber
    public function getFilesize(): int
    {
        return $this->filesize;
    }

    public function setFilesize(int $filesize): self
    {
        $this->filesize = $filesize;

        return $this;
    }

    // TCMSFieldTreeNode
    public function getCmsMediaTree(): ?CmsTree
    {
        return $this->cmsMediaTree;
    }

    public function setCmsMediaTree(?CmsTree $cmsMediaTree): self
    {
        $this->cmsMediaTree = $cmsMediaTree;

        return $this;
    }

    // TCMSFieldNumber
    public function getWidth(): int
    {
        return $this->width;
    }

    public function setWidth(int $width): self
    {
        $this->width = $width;

        return $this;
    }

    // TCMSFieldVarchar
    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    // TCMSFieldText
    public function getMetatags(): string
    {
        return $this->metatags;
    }

    public function setMetatags(string $metatags): self
    {
        $this->metatags = $metatags;

        return $this;
    }

    // TCMSFieldVarchar
    public function getFiletypes(): string
    {
        return $this->filetypes;
    }

    public function setFiletypes(string $filetypes): self
    {
        $this->filetypes = $filetypes;

        return $this;
    }

    // TCMSFieldVarchar
    public function getAltTag(): string
    {
        return $this->altTag;
    }

    public function setAltTag(string $altTag): self
    {
        $this->altTag = $altTag;

        return $this;
    }

    // TCMSFieldVarchar
    public function getSystemname(): string
    {
        return $this->systemname;
    }

    public function setSystemname(string $systemname): self
    {
        $this->systemname = $systemname;

        return $this;
    }

    // TCMSFieldLookupMultiselectTags

    /**
     * @return Collection<int, CmsTags>
     */
    public function getCmsTagsCollection(): Collection
    {
        return $this->cmsTagsCollection;
    }

    public function addCmsTagsCollection(CmsTags $cmsTagsMlt): self
    {
        if (!$this->cmsTagsCollection->contains($cmsTagsMlt)) {
            $this->cmsTagsCollection->add($cmsTagsMlt);
            $cmsTagsMlt->set($this);
        }

        return $this;
    }

    public function removeCmsTagsCollection(CmsTags $cmsTagsMlt): self
    {
        if ($this->cmsTagsCollection->removeElement($cmsTagsMlt)) {
            // set the owning side to null (unless already changed)
            if ($cmsTagsMlt->get() === $this) {
                $cmsTagsMlt->set(null);
            }
        }

        return $this;
    }

    // TCMSFieldVarchar
    public function getCustomFilename(): string
    {
        return $this->customFilename;
    }

    public function setCustomFilename(string $customFilename): self
    {
        $this->customFilename = $customFilename;

        return $this;
    }

    // TCMSFieldMediaPath
    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    // TCMSFieldExtendedLookupMedia
    public function getCmsMedia(): ?CmsMedia
    {
        return $this->cmsMedia;
    }

    public function setCmsMedia(?CmsMedia $cmsMedia): self
    {
        $this->cmsMedia = $cmsMedia;

        return $this;
    }

    // TCMSFieldExternalVideoCode
    public function getExternalEmbedCode(): string
    {
        return $this->externalEmbedCode;
    }

    public function setExternalEmbedCode(string $externalEmbedCode): self
    {
        $this->externalEmbedCode = $externalEmbedCode;

        return $this;
    }

    // TCMSFieldText
    public function getExternalVideoThumbnail(): string
    {
        return $this->externalVideoThumbnail;
    }

    public function setExternalVideoThumbnail(string $externalVideoThumbnail): self
    {
        $this->externalVideoThumbnail = $externalVideoThumbnail;

        return $this;
    }

    // TCMSFieldTimestamp
    public function getTimeStamp(): ?\DateTime
    {
        return $this->timeStamp;
    }

    public function setTimeStamp(?\DateTime $timeStamp): self
    {
        $this->timeStamp = $timeStamp;

        return $this;
    }

    // TCMSFieldDateTimeNow
    public function getDateChanged(): ?\DateTime
    {
        return $this->dateChanged;
    }

    public function setDateChanged(?\DateTime $dateChanged): self
    {
        $this->dateChanged = $dateChanged;

        return $this;
    }

    // TCMSFieldVarchar
    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    public function setRefreshToken(string $refreshToken): self
    {
        $this->refreshToken = $refreshToken;

        return $this;
    }

    // TCMSFieldCMSUser
    public function getCmsUser(): ?CmsUser
    {
        return $this->cmsUser;
    }

    public function setCmsUser(?CmsUser $cmsUser): self
    {
        $this->cmsUser = $cmsUser;

        return $this;
    }

    // TCMSFieldExternalVideoID
    public function getExternalVideoId(): string
    {
        return $this->externalVideoId;
    }

    public function setExternalVideoId(string $externalVideoId): self
    {
        $this->externalVideoId = $externalVideoId;

        return $this;
    }
}
