<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Interfaces;

interface TransformOutgoingMailTargetsServiceInterface
{
    /**
     * Returns an alternative email address for the passed email address (this may be the same one).
     *
     * @param string $mail
     *
     * @return string
     */
    public function transform($mail);

    /**
     * Returns a modified email subject.
     *
     * @param string $subject
     *
     * @return string
     */
    public function transformSubject($subject);
}
