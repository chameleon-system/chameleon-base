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

use ChameleonSystem\CoreBundle\i18n\TranslationConstants;
use TIterator;

interface FlashMessageServiceInterface
{
    /**
     * @param string $consumer
     * @param string $code
     * @param array  $parameter
     */
    public function addMessage($consumer, $code, array $parameter = array());

    /**
     * @param string $consumer
     * @param bool   $remove
     *
     * @return TIterator
     */
    public function consumeMessages($consumer, $remove = true);

    /**
     * @param string      $sConsumerName
     * @param string|null $sViewName
     * @param string|null $sViewType
     * @param array       $aCallTimeVars
     * @param bool        $bRemove
     *
     * @return string
     */
    public function renderMessages($sConsumerName, $sViewName = null, $sViewType = null, array $aCallTimeVars = array(), $bRemove = true);

    /**
     * @param string|null $sConsumerName
     */
    public function clearMessages($sConsumerName = null);

    /**
     * @param string $sConsumerName
     *
     * @return bool
     */
    public function consumerHasMessages($sConsumerName);

    /**
     * @param string $sConsumerName
     *
     * @return int
     */
    public function consumerMessageCount($sConsumerName);

    /**
     * @return int
     */
    public function totalMessageCount();

    /**
     * @param string $sText
     *
     * @return string
     */
    public function injectMessageIntoString($sText);

    /**
     * @return string[]
     */
    public function getConsumerListWithMessages();

    /**
     * @param string $sConsumerName
     * @param string $sDivider
     *
     * @return string
     */
    public function getClassesForConsumer($sConsumerName, $sDivider = ' ');

    /**
     * @param string $id
     * @param string $type
     * @param array  $parameters
     * @param string $domain
     */
    public function addBackendToasterMessage($id, $type = 'ERROR', array $parameters = array(), $domain = TranslationConstants::DOMAIN_BACKEND);
}
