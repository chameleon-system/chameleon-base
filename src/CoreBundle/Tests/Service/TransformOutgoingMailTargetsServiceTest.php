<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Tests\Service;

use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use ChameleonSystem\CoreBundle\Service\TransformOutgoingMailTargetsService;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class TransformOutgoingMailTargetsServiceTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var string
     */
    private $targetMail;
    /**
     * @var array
     */
    private $whiteList;
    /**
     * @var PortalDomainServiceInterface|ObjectProphecy
     */
    private $portalDomainService;
    /**
     * @var TransformOutgoingMailTargetsService
     */
    private $service;
    /**
     * @var string
     */
    private $transformedMail;
    /**
     * @var string
     */
    private $inputMail;
    /**
     * @var string
     */
    private $subjectPrefix;
    /**
     * @var string
     */
    private $transformedSubject;
    /**
     * @var string
     */
    private $sourceSubject;

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->targetMail = null;
        $this->whiteList = null;
        $this->portalDomainService = null;
        $this->service = null;
        $this->transformedMail = null;
        $this->inputMail = null;
        $this->subjectPrefix = null;
        $this->transformedSubject = null;
        $this->sourceSubject = null;
    }

    /**
     * @test
     *
     * @dataProvider dataProviderTransformation
     */
    public function itShouldTransformMails($inputMail, $targetMail, $whiteList, array $domainList, $expected)
    {
        $this->given_a_target_mail($targetMail);
        $this->given_a_white_list($whiteList);
        $this->given_that_the_active_portal_has_the_following_domains($domainList);
        $this->given_an_instance_of_the_service();
        $this->when_we_call_transform_with($inputMail);
        $this->then_we_expect($expected);
    }

    /**
     * @test
     *
     * @dataProvider dataProviderSubjects
     */
    public function itShouldTransformTheSubject($subject, $prefix, $expectedSubject)
    {
        $this->given_a_target_mail('');
        $this->given_a_white_list('');
        $this->given_that_the_active_portal_has_the_following_domains([]);
        $this->given_a_subject_prefix($prefix);
        $this->given_an_instance_of_the_service();
        $this->when_we_call_transformSubject_with($subject);
        $this->then_we_expect_a_subject_matching($expectedSubject);
    }

    private function given_a_target_mail($targetMail)
    {
        $this->targetMail = $targetMail;
    }

    private function given_a_white_list($whiteList)
    {
        $this->whiteList = $whiteList;
    }

    private function given_an_instance_of_the_service()
    {
        $this->service = new TransformOutgoingMailTargetsService($this->targetMail, $this->whiteList, $this->portalDomainService, $this->subjectPrefix);
    }

    private function given_that_the_active_portal_has_the_following_domains($domainList)
    {
        /** @var $portalDomainService PortalDomainServiceInterface|ObjectProphecy */
        $portalDomainService = $this->prophesize(PortalDomainServiceInterface::class);
        $portalDomainService->getDomainNameList()->willReturn($domainList);
        $this->portalDomainService = $portalDomainService->reveal();
    }

    private function when_we_call_transform_with($inputMail)
    {
        $this->inputMail = $inputMail;
        $this->transformedMail = $this->service->transform($inputMail);
    }

    private function then_we_expect($expected)
    {
        $this->assertEquals($expected, $this->transformedMail, "failed for {$this->inputMail} given whiteList {$this->whiteList} and domains ".implode(', ', $this->portalDomainService->getDomainNameList()));
    }

    public function dataProviderTransformation()
    {
        return [
            // not white listed
            [
                'test@esono.de', // inputMail
                'debug@esono.de', // $targetMail
                '', // $whiteList
                [], // array $domainList
                'debug@esono.de', // $expected
            ],
            [
                'test@esono.de', // inputMail
                'debug@esono.de', // $targetMail
                'foo@bar.de', // $whiteList
                [], // array $domainList
                'debug@esono.de', // $expected
            ],
            [
                'test@esono.de', // inputMail
                'debug@esono.de', // $targetMail
                'foo@bar.de;bar@esono.de', // $whiteList
                [], // array $domainList
                'debug@esono.de', // $expected
            ],
            [
                'test@esono.de', // inputMail
                'debug@esono.de', // $targetMail
                '@PORTAL-DOMAINS', // $whiteList
                ['foo.de', 'www.bar.de'], // array $domainList
                'debug@esono.de', // $expected
            ],
            [
                'test@esono.de', // inputMail
                'debug@esono.de', // $targetMail
                '@somedomain.de', // $whiteList
                ['foo.de', 'www.bar.de'], // array $domainList
                'debug@esono.de', // $expected
            ],
            [
                'test@esono.de', // inputMail
                'debug@esono.de', // $targetMail
                '@swww.omedomain.de', // $whiteList
                ['foo.de', 'www.bar.de'], // array $domainList
                'debug@esono.de', // $expected
            ],

            // direct white list
            [
                'test@esono.de', // inputMail
                'debug@esono.de', // $targetMail
                'test@esono.de', // $whiteList
                [], // array $domainList
                'test@esono.de', // $expected
            ],

            // via domain exact match
            [
                'test@esono.de', // inputMail
                'debug@esono.de', // $targetMail
                '@esono.de', // $whiteList
                [], // array $domainList
                'test@esono.de', // $expected
            ],

            // via portal domain
            [
                'test@esono.de', // inputMail
                'debug@esono.de', // $targetMail
                '@esono.de', // $whiteList
                [], // array $domainList
                'test@esono.de', // $expected
            ],

            // via portal domain with www

            [
                'test@esono.de', // inputMail
                'debug@esono.de', // $targetMail
                '@PORTAL-DOMAINS', // $whiteList
                ['esono.de', 'bar.de'], // array $domainList
                'test@esono.de', // $expected
            ],

            [
                'test@esono.de', // inputMail
                'debug@esono.de', // $targetMail
                '@PORTAL-DOMAINS', // $whiteList
                ['www.esono.de', 'bar.de'], // array $domainList
                'test@esono.de', // $expected
            ],
        ];
    }

    private function given_a_subject_prefix($prefix)
    {
        $this->subjectPrefix = $prefix;
    }

    private function when_we_call_transformSubject_with($subject)
    {
        $this->sourceSubject = $subject;
        $this->transformedSubject = $this->service->transformSubject($subject);
    }

    private function then_we_expect_a_subject_matching($expectedSubject)
    {
        $this->assertEquals($expectedSubject, $this->transformedSubject, "failed {$this->sourceSubject} with prefix ".$this->subjectPrefix);
    }

    public function dataProviderSubjects()
    {
        return [
            [
                'test', // subject,
                null, // prefix,
                'test', // expected,
            ],
            [
                'test', // subject,
                'foo', // prefix,
                'footest', // expected,
            ],
        ];
    }
}
