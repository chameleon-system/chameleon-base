<?php

namespace ChameleonSystem\CmsCoreLogBundle\Bridge\Monolog;

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Service\RequestInfoServiceInterface;
use Monolog\Processor\ProcessorInterface;

class RequestIdProcessor implements ProcessorInterface
{
    /**
     * @var RequestInfoServiceInterface
     */
    private $requestInfoService;

    public function __construct(RequestInfoServiceInterface $requestInfoService)
    {
        $this->requestInfoService = $requestInfoService;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(array $record)
    {
        $extraData = ['request_id' => $this->requestInfoService->getRequestId()];

        if (true === \array_key_exists('extra', $record)) {
            $extraData = \array_merge(
                $record['extra'],
                $extraData
            );
        }

        $record['extra'] = $extraData;

        return $record;
    }
}
