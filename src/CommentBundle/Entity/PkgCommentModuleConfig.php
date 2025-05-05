<?php

namespace ChameleonSystem\CommentBundle\Entity;

use ChameleonSystem\DataAccessBundle\Entity\CorePagedef\CmsTplModuleInstance;

class PkgCommentModuleConfig
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldLookupParentID
        /** @var CmsTplModuleInstance|null - Belongs to module instance */
        private ?CmsTplModuleInstance $cmsTplModuleInstance = null,
        // TCMSFieldVarchar
        /** @var string - Headline */
        private string $name = '',
        // TCMSFieldLookup
        /** @var PkgCommentType|null - Type of comment */
        private ?PkgCommentType $pkgCommentType = null,
        // TCMSFieldWYSIWYG
        /** @var string - Introductory text */
        private string $introText = '',
        // TCMSFieldWYSIWYG
        /** @var string - Closing text */
        private string $closingText = '',
        // TCMSFieldNumber
        /** @var int - Comments per page */
        private int $numberOfCommentsPerPage = 20,
        // TCMSFieldBoolean
        /** @var bool - Visible comments for guests */
        private bool $guestCanSeeComments = true,
        // TCMSFieldBoolean
        /** @var bool - Comments from guests allowed */
        private bool $guestCommentAllowed = false,
        // TCMSFieldVarchar
        /** @var string - Display if comment is deleted */
        private string $commentOnDelete = 'Dieser Kommentar wurde gelöscht',
        // TCMSFieldBoolean
        /** @var bool - Show new comments first */
        private bool $newestOnTop = true,
        // TCMSFieldBoolean
        /** @var bool - Use simple comment reporting function */
        private bool $useSimpleReporting = false,
        // TCMSFieldBoolean
        /** @var bool - Show reported comments */
        private bool $showReportedComments = true
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
    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    // TCMSFieldLookup
    public function getPkgCommentType(): ?PkgCommentType
    {
        return $this->pkgCommentType;
    }

    public function setPkgCommentType(?PkgCommentType $pkgCommentType): self
    {
        $this->pkgCommentType = $pkgCommentType;

        return $this;
    }

    // TCMSFieldWYSIWYG
    public function getIntroText(): string
    {
        return $this->introText;
    }

    public function setIntroText(string $introText): self
    {
        $this->introText = $introText;

        return $this;
    }

    // TCMSFieldWYSIWYG
    public function getClosingText(): string
    {
        return $this->closingText;
    }

    public function setClosingText(string $closingText): self
    {
        $this->closingText = $closingText;

        return $this;
    }

    // TCMSFieldNumber
    public function getNumberOfCommentsPerPage(): int
    {
        return $this->numberOfCommentsPerPage;
    }

    public function setNumberOfCommentsPerPage(int $numberOfCommentsPerPage): self
    {
        $this->numberOfCommentsPerPage = $numberOfCommentsPerPage;

        return $this;
    }

    // TCMSFieldBoolean
    public function isGuestCanSeeComments(): bool
    {
        return $this->guestCanSeeComments;
    }

    public function setGuestCanSeeComments(bool $guestCanSeeComments): self
    {
        $this->guestCanSeeComments = $guestCanSeeComments;

        return $this;
    }

    // TCMSFieldBoolean
    public function isGuestCommentAllowed(): bool
    {
        return $this->guestCommentAllowed;
    }

    public function setGuestCommentAllowed(bool $guestCommentAllowed): self
    {
        $this->guestCommentAllowed = $guestCommentAllowed;

        return $this;
    }

    // TCMSFieldVarchar
    public function getCommentOnDelete(): string
    {
        return $this->commentOnDelete;
    }

    public function setCommentOnDelete(string $commentOnDelete): self
    {
        $this->commentOnDelete = $commentOnDelete;

        return $this;
    }

    // TCMSFieldBoolean
    public function isNewestOnTop(): bool
    {
        return $this->newestOnTop;
    }

    public function setNewestOnTop(bool $newestOnTop): self
    {
        $this->newestOnTop = $newestOnTop;

        return $this;
    }

    // TCMSFieldBoolean
    public function isUseSimpleReporting(): bool
    {
        return $this->useSimpleReporting;
    }

    public function setUseSimpleReporting(bool $useSimpleReporting): self
    {
        $this->useSimpleReporting = $useSimpleReporting;

        return $this;
    }

    // TCMSFieldBoolean
    public function isShowReportedComments(): bool
    {
        return $this->showReportedComments;
    }

    public function setShowReportedComments(bool $showReportedComments): self
    {
        $this->showReportedComments = $showReportedComments;

        return $this;
    }
}
