<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Service\Initializer;

use ChameleonSystem\CoreBundle\Service\ActivePageServiceInterface;

/**
 * Interface ActivePageServiceInitializerInterface defines a service that initializes the ActivePageService.
 * This cannot be a factory, as vital initialization information is only available when the request initializes,
 * and as there would inevitably be lots of circular dependencies.
 */
interface ActivePageServiceInitializerInterface
{
    /**
     * @return void
     */
    public function initialize(ActivePageServiceInterface $activePageService);
}
