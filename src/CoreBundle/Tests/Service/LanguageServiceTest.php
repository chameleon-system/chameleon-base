<?php

declare(strict_types=1);

namespace ChameleonSystem\CoreBundle\Tests\Service;

use ChameleonSystem\CoreBundle\DataAccess\DataAccessCmsLanguageInterface;
use ChameleonSystem\CoreBundle\Service\Initializer\LanguageServiceInitializerInterface;
use ChameleonSystem\CoreBundle\Service\LanguageService;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class LanguageServiceTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var EventDispatcherInterface|ObjectProphecy
     */
    private $eventDispatcher;

    /**
     * @var LanguageServiceInitializerInterface|ObjectProphecy
     */
    private $initializer;

    /**
     * @var DataAccessCmsLanguageInterface|ObjectProphecy
     */
    private $languageDataAccess;

    private RequestStack $requestStack;

    protected function setUp(): void
    {
        parent::setUp();

        $this->eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $this->initializer = $this->prophesize(LanguageServiceInitializerInterface::class);
        $this->languageDataAccess = $this->prophesize(DataAccessCmsLanguageInterface::class);
        $this->requestStack = new RequestStack();

        $this->eventDispatcher->dispatch(\Prophecy\Argument::cetera())->willReturnArgument(0);
    }

    public function testSetActiveLanguageStoresIsoCodeAsRequestLocale(): void
    {
        $request = Request::create('https://www.example.com/');
        $this->requestStack->push($request);

        $language = $this->createMock(\TdbCmsLanguage::class);
        $language->id = 'fr-id';
        $language->fieldIso6391 = 'fr';
        $language->sqlData = [];
        $language->expects(self::once())->method('SetLanguage')->with('fr-id');
        $language->expects(self::once())->method('LoadFromRow')->with([]);

        $this->languageDataAccess->getLanguage('fr-id', 'fr-id')->willReturn($language);

        $service = new LanguageService(
            $this->requestStack,
            $this->eventDispatcher->reveal(),
            $this->initializer->reveal(),
            $this->languageDataAccess->reveal()
        );

        $service->setActiveLanguage('fr-id');

        self::assertSame('fr', $request->attributes->get('_locale'));
        self::assertSame('fr', $service->getActiveLocale());
    }
}
