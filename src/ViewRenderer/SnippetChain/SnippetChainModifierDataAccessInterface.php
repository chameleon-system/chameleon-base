<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\ViewRenderer\SnippetChain;

use ChameleonSystem\ViewRenderer\Exception\DataAccessException;

/**
 * SnippetChainModifierDataAccessInterface defines a service that accesses snippet chain data from theme configuration
 * in the data store.
 */
interface SnippetChainModifierDataAccessInterface
{
    /**
     * Returns snippet chain data for specific or all themes.
     *
     * @param array $themes A list of theme IDs. Returns snippet chains for all themes if this list is empty.
     *
     * @return string[] a list of snippet chains (array key is the theme ID, array value is the snippet chain)
     *
     * @throws DataAccessException
     */
    public function getThemeData(array $themes = []);

    /**
     * Updates the snippet chain for a theme.
     *
     * @param string $themeId
     * @param string $snippetChain
     *
     * @return void
     *
     * @throws DataAccessException
     */
    public function updateSnippetChain($themeId, $snippetChain);
}
