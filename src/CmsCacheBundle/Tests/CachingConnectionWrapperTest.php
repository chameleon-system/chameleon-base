<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CmsCacheBundle\Tests {
    use ChameleonSystem\CmsCacheBundle\Doctrine\CachingConnectionWrapper;
    use PHPUnit\Framework\TestCase;

    class CachingConnectionWrapperTest extends TestCase
    {
        /**
         * @test
         */
        public function itAllowsCachingForDateOnlyParameters(): void
        {
            $result = $this->invokeIsCacheableQuery(
                'SELECT * FROM `cms_tree` WHERE `date` = ?',
                ['2026-04-27']
            );

            $this->assertTrue($result);
        }

        /**
         * @test
         */
        public function itPreventsCachingForDateTimeStringParameters(): void
        {
            $result = $this->invokeIsCacheableQuery(
                'SELECT * FROM `cms_tree` WHERE `changed` = ?',
                ['2026-04-27 12:34:56']
            );

            $this->assertFalse($result);
        }

        /**
         * @test
         */
        public function itPreventsCachingForNestedDateTimeStringParameters(): void
        {
            $result = $this->invokeIsCacheableQuery(
                'SELECT * FROM `cms_tree` WHERE `changed` IN (?)',
                [['2026-04-27', '2026-04-27 12:34']]
            );

            $this->assertFalse($result);
        }

        /**
         * @test
         */
        public function itPreventsCachingForDateTimeObjectsWithATimeComponent(): void
        {
            $result = $this->invokeIsCacheableQuery(
                'SELECT * FROM `cms_tree` WHERE `changed` = ?',
                [new \DateTimeImmutable('2026-04-27 12:34:56')]
            );

            $this->assertFalse($result);
        }

        private function invokeIsCacheableQuery(string $sql, array $params): bool
        {
            $wrapper = (new \ReflectionClass(CachingConnectionWrapper::class))->newInstanceWithoutConstructor();
            $method = new \ReflectionMethod(CachingConnectionWrapper::class, 'isCacheableQuery');
            $method->setAccessible(true);

            return $method->invoke($wrapper, $sql, $params);
        }
    }
}
