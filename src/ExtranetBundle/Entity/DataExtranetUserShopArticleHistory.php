<?php

namespace ChameleonSystem\ExtranetBundle\Entity;

use ChameleonSystem\ShopBundle\Entity\Product\ShopArticle;

class DataExtranetUserShopArticleHistory
{
    public function __construct(
        private string $id,
        private ?int $cmsident = null,

        // TCMSFieldLookupParentID
        /** @var DataExtranetUser|null - Belongs to customer */
        private ?DataExtranetUser $dataExtranetUser = null,
        // TCMSFieldLookup
        /** @var ShopArticle|null - Article */
        private ?ShopArticle $shopArticle = null,
        // TCMSFieldDateTime
        /** @var \DateTime|null - Viewed on */
        private ?\DateTime $datecreated = null
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
    public function getDataExtranetUser(): ?DataExtranetUser
    {
        return $this->dataExtranetUser;
    }

    public function setDataExtranetUser(?DataExtranetUser $dataExtranetUser): self
    {
        $this->dataExtranetUser = $dataExtranetUser;

        return $this;
    }

    // TCMSFieldLookup
    public function getShopArticle(): ?ShopArticle
    {
        return $this->shopArticle;
    }

    public function setShopArticle(?ShopArticle $shopArticle): self
    {
        $this->shopArticle = $shopArticle;

        return $this;
    }

    // TCMSFieldDateTime
    public function getDatecreated(): ?\DateTime
    {
        return $this->datecreated;
    }

    public function setDatecreated(?\DateTime $datecreated): self
    {
        $this->datecreated = $datecreated;

        return $this;
    }
}
