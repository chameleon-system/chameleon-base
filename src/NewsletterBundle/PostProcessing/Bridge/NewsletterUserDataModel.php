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
    /**
     * @var string
     */
    private $salutation;

    /**
     * @var string
     */
    private $firstName;

    /**
     * @var string
     */
    private $lastName;

    /**
     * @var string
     */
    private $eMail;

    /**
     * @var string
     */
    private $unsubscribeLink;

    /**
     * @var string
     */
    private $htmlLink;

    /**
     * @param string $salutation
     * @param string $firstName
     * @param string $lastName
     * @param string $eMail
     * @param string $unsubscribeLink
     * @param string $htmlLink
     */
    public function __construct($salutation, $firstName, $lastName, $eMail, $unsubscribeLink, $htmlLink)
    {
        $this->salutation = $salutation;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->eMail = $eMail;
        $this->unsubscribeLink = $unsubscribeLink;
        $this->htmlLink = $htmlLink;
    }

    /**
     * @return string
     */
    public function getSalutation()
    {
        return $this->salutation;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getEMail()
    {
        return $this->eMail;
    }

    /**
     * @return string
     */
    public function getUnsubscribeLink()
    {
        return $this->unsubscribeLink;
    }

    /**
     * @return string
     */
    public function getHtmlLink()
    {
        return $this->htmlLink;
    }
}
