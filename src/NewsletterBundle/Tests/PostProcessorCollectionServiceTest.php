<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\NewsletterBundle\Test;

use ChameleonSystem\NewsletterBundle\PostProcessing\PostProcessorCollectionService;
use PHPUnit\Framework\TestCase;

class PostProcessorCollectionServiceTest extends TestCase
{
    /**
     * @test
     */
    public function itProcessesTextUsingAProcessor()
    {
        $processor1 = $this->getMockBuilder('\ChameleonSystem\NewsletterBundle\PostProcessing\PostProcessorInterface')->getMock();
        $processor1->expects($this->once())->method('process')->will($this->returnValue('1'));

        $processorService = new PostProcessorCollectionService();
        $processorService->addPostProcessor($processor1);

        $userData = $this->getMockBuilder('\ChameleonSystem\NewsletterBundle\PostProcessing\Bridge\NewsletterUserDataModel')->disableOriginalConstructor()->getMock();

        $result = $processorService->process('foo', $userData);

        $this->assertEquals('1', $result);
    }

    /**
     * @test
     */
    public function itProcessesTextUsingMultipleProcessors()
    {
        $userData = $this->getMockBuilder('\ChameleonSystem\NewsletterBundle\PostProcessing\Bridge\NewsletterUserDataModel')->disableOriginalConstructor()->getMock();
        $processor1 = $this->getMockBuilder('\ChameleonSystem\NewsletterBundle\PostProcessing\PostProcessorInterface')->getMock();
        $processor1->expects($this->once())->method('process')->will($this->returnValue('1'));
        $processor2 = $this->getMockBuilder('\ChameleonSystem\NewsletterBundle\PostProcessing\PostProcessorInterface')->getMock();
        $processor2->expects($this->once())->method('process')->will(
            $this->returnValueMap(
                [
                    ['1', $userData, 'correct'],
                ]
            )
        );

        $processorService = new PostProcessorCollectionService();
        $processorService->addPostProcessor($processor1);
        $processorService->addPostProcessor($processor2);

        $result = $processorService->process('foo', $userData);

        $this->assertEquals('correct', $result);
    }

    /**
     * @test
     */
    public function itProcessesTextUsingNoProcessors()
    {
        $userData = $this->getMockBuilder('\ChameleonSystem\NewsletterBundle\PostProcessing\Bridge\NewsletterUserDataModel')->disableOriginalConstructor()->getMock();
        $processorService = new PostProcessorCollectionService();
        $result = $processorService->process('foo', $userData);

        $this->assertEquals('foo', $result);
    }
}
