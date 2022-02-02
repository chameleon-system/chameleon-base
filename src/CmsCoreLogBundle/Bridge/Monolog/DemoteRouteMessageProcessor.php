<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CmsCoreLogBundle\Bridge\Monolog;

use Monolog\Logger;
use Monolog\Processor\ProcessorInterface;

class DemoteRouteMessageProcessor implements ProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(array $record)
    {
        if ('request' !== $record['channel']) {
            return $record;
        }

        if (Logger::INFO !== $record['level']) {
            return $record;
        }

        if (0 !== \mb_strpos($record['message'], 'Matched route ')) {
            return $record;
        }

        $record['level'] = Logger::DEBUG;
        $record['level_name'] = Logger::getLevelName(Logger::DEBUG);

        return $record;
    }
}
