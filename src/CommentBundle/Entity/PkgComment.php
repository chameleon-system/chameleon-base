<?php

namespace ChameleonSystem\CommentBundle\Entity;

use ChameleonSystem\ExtranetBundle\Entity\DataExtranetUser;

class PkgComment
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldExtendedLookup
        /** @var PkgCommentType|null - Comment type */
        private ?PkgCommentType $pkgCommentType = null,
        // TCMSFieldVarchar
        /** @var string - Object ID */
        private string $itemId = '',
        // TCMSFieldExtendedLookup
        /** @var DataExtranetUser|null - User */
        private ?DataExtranetUser $dataExtranetUser = null,
        // TCMSFieldDateTime
        /** @var \DateTime|null - Creation date */
        private ?\DateTime $createdTimestamp = null,
        // TCMSFieldText
        /** @var string - Comment text */
        private string $comment = '',
        // TCMSFieldExtendedLookup
        /** @var PkgComment|null - Comment feedback */
        private ?PkgComment $pkgComment = null,
        // TCMSFieldBoolean
        /** @var bool - Comment has been deleted */
        private bool $markAsDeleted = false,
        // TCMSFieldBoolean
        /** @var bool - Comment has been reported */
        private bool $markAsReported = false
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

    // TCMSFieldExtendedLookup
    public function getPkgCommentType(): ?PkgCommentType
    {
        return $this->pkgCommentType;
    }

    public function setPkgCommentType(?PkgCommentType $pkgCommentType): self
    {
        $this->pkgCommentType = $pkgCommentType;

        return $this;
    }

    // TCMSFieldVarchar
    public function getItemId(): string
    {
        return $this->itemId;
    }

    public function setItemId(string $itemId): self
    {
        $this->itemId = $itemId;

        return $this;
    }

    // TCMSFieldExtendedLookup
    public function getDataExtranetUser(): ?DataExtranetUser
    {
        return $this->dataExtranetUser;
    }

    public function setDataExtranetUser(?DataExtranetUser $dataExtranetUser): self
    {
        $this->dataExtranetUser = $dataExtranetUser;

        return $this;
    }

    // TCMSFieldDateTime
    public function getCreatedTimestamp(): ?\DateTime
    {
        return $this->createdTimestamp;
    }

    public function setCreatedTimestamp(?\DateTime $createdTimestamp): self
    {
        $this->createdTimestamp = $createdTimestamp;

        return $this;
    }

    // TCMSFieldText
    public function getComment(): string
    {
        return $this->comment;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    // TCMSFieldExtendedLookup
    public function getPkgComment(): ?PkgComment
    {
        return $this->pkgComment;
    }

    public function setPkgComment(?PkgComment $pkgComment): self
    {
        $this->pkgComment = $pkgComment;

        return $this;
    }

    // TCMSFieldBoolean
    public function isMarkAsDeleted(): bool
    {
        return $this->markAsDeleted;
    }

    public function setMarkAsDeleted(bool $markAsDeleted): self
    {
        $this->markAsDeleted = $markAsDeleted;

        return $this;
    }

    // TCMSFieldBoolean
    public function isMarkAsReported(): bool
    {
        return $this->markAsReported;
    }

    public function setMarkAsReported(bool $markAsReported): self
    {
        $this->markAsReported = $markAsReported;

        return $this;
    }
}
