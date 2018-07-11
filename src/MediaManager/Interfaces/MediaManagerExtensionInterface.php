<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\MediaManager\Interfaces;

/**
 * Interface for media manager extensions. At the moment, only some parts of the detail view are extendable.
 */
interface MediaManagerExtensionInterface
{
    /**
     * Returns an array of mappers (service IDs) to be used for media manager detail view.
     *
     * @return string[]
     */
    public function registerDetailMappers();

    /**
     * Returns twig template paths to be included in detail buttons area.
     *
     * @return string[]
     */
    public function registerAdditionalTemplatesForDetailViewButtons();

    /**
     * Returns twig template paths to be included at the bottom of the media manager detail view.
     *
     * @return string[]
     */
    public function registerAdditionalTemplatesForDetailView();
}
