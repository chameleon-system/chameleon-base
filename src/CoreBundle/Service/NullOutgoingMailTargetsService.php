<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Service;

use ChameleonSystem\CoreBundle\Interfaces\TransformOutgoingMailTargetsServiceInterface;

class NullOutgoingMailTargetsService implements TransformOutgoingMailTargetsServiceInterface
{
    /**
     * {@inheritdoc}
     */
    public function setEnableTransformation($enableTransformation)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function transform($mail)
    {
        return $mail;
    }

    /**
     * {@inheritdoc}
     */
    public function transformSubject($subject)
    {
        return $subject;
    }

    /**
     * {@inheritdoc}
     */
    public function setSubjectPrefix($prefix)
    {
    }
}
