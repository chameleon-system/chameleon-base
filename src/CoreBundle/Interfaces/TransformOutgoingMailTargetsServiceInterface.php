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
     * @param $enableTransformation
     *
     * @return mixed
     */
    public function setEnableTransformation($enableTransformation);

    /**
     * @param string $mail
     *
     * @return string
     */
    public function transform($mail);

    /**
     * @param string $subject
     *
     * @return string
     */
    public function transformSubject($subject);

    public function setSubjectPrefix($prefix);
}
