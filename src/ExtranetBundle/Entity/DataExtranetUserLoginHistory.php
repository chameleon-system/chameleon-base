<?php

namespace ChameleonSystem\ExtranetBundle\Entity;

use DateTime;

class DataExtranetUserLoginHistory
{
    public function __construct(
        private string $id,
        private int|null $cmsident = null,

        // TCMSFieldLookupParentID
        /** @var DataExtranetUser|null - Corresponding user */
        private ?DataExtranetUser $dataExtranetUser = null
        ,
        // TCMSFieldDateTimeNow
        /** @var DateTime|null - Date */
        private ?DateTime $datecreated = new DateTime(),
        // TCMSFieldVarchar
        /** @var string - User IP */
        private string $userIp = ''
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


    // TCMSFieldDateTimeNow
    public function getDatecreated(): ?DateTime
    {
        return $this->datecreated;
    }

    public function setDatecreated(?DateTime $datecreated): self
    {
        $this->datecreated = $datecreated;

        return $this;
    }


    // TCMSFieldVarchar
    public function getUserIp(): string
    {
        return $this->userIp;
    }

    public function setUserIp(string $userIp): self
    {
        $this->userIp = $userIp;

        return $this;
    }


}
