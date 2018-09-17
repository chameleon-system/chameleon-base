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
     * @param bool $enableTransformation
     *
     * @deprecated since 6.3.0 - no longer used. Transformation is enabled/disabled by ChameleonSystemCoreExtension.
     */
    public function setEnableTransformation($enableTransformation);

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

    /**
     * @param string $prefix
     *
     * @deprecated since 6.3.0 - it is now up to the implementation how the prefix is managed.
     */
    public function setSubjectPrefix($prefix);
}
