<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\NewsletterBundle\PostProcessing\Bridge;

class NewsletterUserDataModel
{
    private string $salutation;
    private string $firstName;
    private string $lastName;
    private string $eMail;
    private string $unsubscribeLink;
    private string $htmlLink;
    private ?string $extranetUserId = null;

    public function __construct(string $salutation, string $firstName, string $lastName, string $eMail, string $unsubscribeLink, string $htmlLink)
    {
        $this->salutation = $salutation;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->eMail = $eMail;
        $this->unsubscribeLink = $unsubscribeLink;
        $this->htmlLink = $htmlLink;
    }

    public function getSalutation(): string
    {
        return $this->salutation;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getEMail(): string
    {
        return $this->eMail;
    }

    public function getUnsubscribeLink(): string
    {
        return $this->unsubscribeLink;
    }

    public function getHtmlLink(): string
    {
        return $this->htmlLink;
    }

    public function getExtranetUserId(): ?string
    {
        return $this->extranetUserId;
    }

    public function setExtranetUserId(?string $extranetUserId): void
    {
        $this->extranetUserId = $extranetUserId;
    }
}
