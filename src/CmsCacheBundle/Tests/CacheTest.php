<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace {
    if (!class_exists('\TdbCmsLanguage')) {
        class TdbCmsLanguage
        {
        }
    }
}

namespace esono\pkgcmscache\tests {
    use ChameleonSystem\CoreBundle\RequestState\Interfaces\RequestStateHashProviderInterface;
    use ChameleonSystem\CoreBundle\Util\HashInterface;
    use esono\pkgCmsCache\Cache;
    use PHPUnit\Framework\TestCase;
    use Prophecy\Argument;
    use Symfony\Component\HttpFoundation\RequestStack;
    use esono\pkgCmsCache\StorageInterface;
    use Doctrine\DBAL\Connection;
    use Prophecy\PhpUnit\ProphecyTrait;

    class CacheTest extends TestCase
    {
        use ProphecyTrait;

        /**
         * @var Cache
         */
        private $cache;

        protected function setUp(): void
        {
            parent::setUp();

            $connection = $this->getMockBuilder(Connection::class)->disableOriginalConstructor()->getMock();
            $storage = $this->prophesize(StorageInterface::class);
            $requestStack = new RequestStack();
            $hash = $this->prophesize(HashInterface::class);
            $hash->hash32(Argument::any())->will(function ($args) {
                return \md5(\json_encode($args[0]));
            });
            $requestStateHashProvider = $this->prophesize(RequestStateHashProviderInterface::class);
            $this->cache = new Cache($requestStack, $connection, $storage->reveal(), null, true, $hash->reveal(), $requestStateHashProvider->reveal());
        }

        /**
         * @test
         */
        public function it_creates_cache_key()
        {
            $this->cache->disable();
            $params = [
                'foo' => 'bar',
                'foo2' => 'bar2',
            ];
            $expectedParams = $params;
            $expectedParams['__uniqueIdentity'] = null;
            $expected = \md5(\json_encode($expectedParams));
            $result = $this->cache->getKey($params, false);
            $this->assertEquals($expected, $result);
        }

        /**
         * @test
         */
        public function it_works_with_special_chars()
        {
            $this->cache->disable();
            $params = [
                'foo2' => '中国 农业',
                'foo' => 'bar',
            ];
            $expectedParams = $params;
            $expectedParams['__uniqueIdentity'] = null;
            $expected = \md5(\json_encode($expectedParams));
            $result = $this->cache->getKey($params, false);
            $this->assertEquals($expected, $result);
        }
    }
}
