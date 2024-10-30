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

use ChameleonSystem\CoreBundle\Service\RequestInfoServiceInterface;
use Monolog\LogRecord;
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
    public function __invoke(array|LogRecord $record)
    {
        if ($record instanceof LogRecord) $record = $record->toArray();
        $requestId = $this->requestInfoService->getRequestId();

        if (true === \array_key_exists('extra', $record)) {
            $record['extra']['request_id'] = $requestId;
        } else {
            $record['extra'] = ['request_id' => $requestId];
        }

        return $record;
    }
}
