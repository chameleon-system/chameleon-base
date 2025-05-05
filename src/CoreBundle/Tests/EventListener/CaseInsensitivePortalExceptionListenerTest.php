<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Tests\EventListener;

use ChameleonSystem\CoreBundle\DataAccess\CmsPortalDomainsDataAccessInterface;
use ChameleonSystem\CoreBundle\EventListener\CaseInsensitivePortalExceptionListener;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelInterface;

class CaseInsensitivePortalExceptionListenerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var CaseInsensitivePortalExceptionListener
     */
    private $subject;
    /**
     * @var CmsPortalDomainsDataAccessInterface|ObjectProphecy
     */
    private $cmsPortalDomainsDataAccessMock;
    /**
     * @var ExceptionEvent
     */
    private $event;
    /**
     * @var Request|ObjectProphecy
     */
    private $request;

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->subject = null;
        $this->cmsPortalDomainsDataAccessMock = null;
        $this->event = null;
        $this->request = null;
    }

    /**
     * @dataProvider provideDataForTestOnKernelException
     */
    public function testOnKernelException(string $currentPath, string $requestMethod, ?RedirectResponse $expectedResponse): void
    {
        $this->givenRequest($currentPath, $requestMethod);
        $this->givenCaseInsensitivePortalExceptionListener();
        $this->whenOnKernelExceptionIsCalled(new NotFoundHttpException('test'));
        $this->thenTheExpectedResponseShouldBeSet($expectedResponse);
    }

    public function provideDataForTestOnKernelException(): array
    {
        return [
            'plain-portal-home' => [
                '/',
                'GET',
                null,
            ],
            'invalid-for-other-reason' => [
                '/invalid-path',
                'GET',
                null,
            ],
            'exact-portal-prefix' => [
                '/very-prefix',
                'GET',
                null,
            ],
            'all-caps-portal-prefix' => [
                '/VERY-PREFIX/so-path',
                'GET',
                new RedirectResponse('/very-prefix/so-path', 301),
            ],
            'some-caps-portal-prefix' => [
                '/vErY-pReFiX/so-path',
                'GET',
                new RedirectResponse('/very-prefix/so-path', 301),
            ],
            'start-capital-portal-prefix' => [
                '/Very-prefix/so-path',
                'GET',
                new RedirectResponse('/very-prefix/so-path', 301),
            ],
            'portal-home-with-prefix' => [
                '/Very-prefix',
                'GET',
                new RedirectResponse('/very-prefix', 301),
            ],
            'portal-home-with-prefix-and-trailing-slash' => [
                '/Very-prefix/',
                'GET',
                new RedirectResponse('/very-prefix/', 301),
            ],
            'post-with-prefix' => [
                '/Very-prefix/wow',
                'POST',
                new RedirectResponse('/very-prefix/wow', 307),
            ],
            'get-with-parameters' => [
                '/Very-prefix/so-path?param=value&another-param=another-value',
                'GET',
                new RedirectResponse('/very-prefix/so-path?param=value&another-param=another-value', 301),
            ],
            'post-with-parameters' => [
                '/Very-prefix/so-path?param=value&another-param=another-value',
                'POST',
                new RedirectResponse('/very-prefix/so-path?param=value&another-param=another-value', 307),
            ],
        ];
    }

    private function givenRequest(string $currentPath, string $requestMethod, ?string $content = null): void
    {
        $this->request = $this->prophesize(Request::class);
        $this->request->getHost()->willReturn('such-host');
        $this->request->getPathInfo()->willReturn($currentPath);
        $this->request->getMethod()->willReturn($requestMethod);
        $this->request->getContent()->willReturn($content);
    }

    private function givenCaseInsensitivePortalExceptionListener(): void
    {
        $this->cmsPortalDomainsDataAccessMock = $this->prophesize(CmsPortalDomainsDataAccessInterface::class);
        $this->cmsPortalDomainsDataAccessMock->getPortalPrefixListForDomain(Argument::any())->willReturn([
            'very-prefix',
            'many-prefix',
        ]);
        $this->subject = new CaseInsensitivePortalExceptionListener($this->cmsPortalDomainsDataAccessMock->reveal());
    }

    private function whenOnKernelExceptionIsCalled(\Exception $exception): void
    {
        $kernel = $this->prophesize(KernelInterface::class);
        $this->event = new ExceptionEvent($kernel->reveal(), $this->request->reveal(), 1, $exception);
        $this->subject->onKernelException($this->event);
    }

    private function thenTheExpectedResponseShouldBeSet(?RedirectResponse $expectedResponse): void
    {
        $actualResponse = $this->event->getResponse();
        if (null === $expectedResponse) {
            static::assertNull($actualResponse);

            return;
        }

        static::assertInstanceOf(RedirectResponse::class, $actualResponse);
        static::assertEquals($expectedResponse->getStatusCode(), $actualResponse->getStatusCode());
        static::assertEquals($expectedResponse->getTargetUrl(), $actualResponse->getTargetUrl());
        static::assertEquals($expectedResponse->getContent(), $actualResponse->getContent());
    }

    public function testOnKernelExceptionIgnoresOtherExceptionClasses(): void
    {
        $this->givenRequest('/', 'GET');
        $this->givenCaseInsensitivePortalExceptionListener();
        $this->whenOnKernelExceptionIsCalled(new \Exception());
        $this->thenTheExpectedResponseShouldBeSet(null);

        $this->whenOnKernelExceptionIsCalled(new AccessDeniedHttpException());
        $this->thenTheExpectedResponseShouldBeSet(null);
    }
}
