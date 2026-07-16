<?php

declare(strict_types=1);

namespace ChameleonSystem\CoreBundle\Tests\DataAccess;

use ChameleonSystem\CoreBundle\DataAccess\CmsPortalDomainsDataAccess;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CmsPortalDomainsDataAccessTest extends TestCase
{
    private CmsPortalDomainsDataAccess $subject;

    /**
     * @var Connection&MockObject
     */
    private $connection;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection = $this->createMock(Connection::class);
        $this->subject = new CmsPortalDomainsDataAccess($this->connection);
    }

    public function testGetDomainCandidatesByHostReturnsEmptyArrayForEmptyHost(): void
    {
        $this->connection
            ->expects($this->never())
            ->method('fetchAllAssociative');

        self::assertSame([], $this->subject->getDomainCandidatesByHost(''));
    }

    public function testGetDomainCandidatesByHostReturnsSingleConfiguredDomain(): void
    {
        $host = 'www.example.com';
        $expectedRows = [
            [
                'id' => 'domain-de',
                'cms_portal_id' => 'portal-a',
                'cms_language_id' => 'lang-de',
                'name' => $host,
                'sslname' => '',
                'url_suffix' => '',
                'is_master_domain' => '1',
            ],
        ];

        $this->connection
            ->expects($this->once())
            ->method('fetchAllAssociative')
            ->with($this->stringContains('(`name` = ? AND `name` != \'\')'), [$host, $host])
            ->willReturn($expectedRows);

        self::assertSame($expectedRows, $this->subject->getDomainCandidatesByHost($host));
    }

    public function testGetDomainCandidatesByHostReturnsAllCandidatesForSameHost(): void
    {
        $host = 'www.tischwelt.ch';
        $expectedRows = [
            [
                'id' => 'domain-de',
                'cms_portal_id' => 'portal-a',
                'cms_language_id' => 'lang-de',
                'name' => $host,
                'sslname' => '',
                'url_suffix' => '',
                'is_master_domain' => '1',
            ],
            [
                'id' => 'domain-fr',
                'cms_portal_id' => 'portal-a',
                'cms_language_id' => 'lang-fr',
                'name' => $host,
                'sslname' => '',
                'url_suffix' => 'fr',
                'is_master_domain' => '0',
            ],
            [
                'id' => 'domain-it',
                'cms_portal_id' => 'portal-a',
                'cms_language_id' => 'lang-it',
                'name' => $host,
                'sslname' => '',
                'url_suffix' => 'it',
                'is_master_domain' => '0',
            ],
        ];

        $this->connection
            ->expects($this->once())
            ->method('fetchAllAssociative')
            ->with($this->logicalAnd(
                $this->logicalNot($this->stringContains('GROUP BY')),
                $this->stringContains('ORDER BY `cms_portal_id` ASC, `url_suffix` ASC, `id` ASC')
            ), [$host, $host])
            ->willReturn($expectedRows);

        self::assertSame($expectedRows, $this->subject->getDomainCandidatesByHost($host));
    }

    public function testGetDomainCandidatesByHostMatchesSslHost(): void
    {
        $host = 'secure.example.com';
        $expectedRows = [
            [
                'id' => 'domain-fr',
                'cms_portal_id' => 'portal-a',
                'cms_language_id' => 'lang-fr',
                'name' => 'www.example.com',
                'sslname' => $host,
                'url_suffix' => 'fr',
                'is_master_domain' => '0',
            ],
        ];

        $this->connection
            ->expects($this->once())
            ->method('fetchAllAssociative')
            ->with($this->stringContains('(`sslname` = ? AND `sslname` != \'\')'), [$host, $host])
            ->willReturn($expectedRows);

        self::assertSame($expectedRows, $this->subject->getDomainCandidatesByHost($host));
    }

    public function testGetDomainCandidatesByHostAndPortalRestrictsPortalContext(): void
    {
        $host = 'www.example.com';
        $portalId = 'portal-a';
        $expectedRows = [
            [
                'id' => 'domain-fr',
                'cms_portal_id' => $portalId,
                'cms_language_id' => 'lang-fr',
                'name' => $host,
                'sslname' => '',
                'url_suffix' => 'fr',
                'is_master_domain' => '0',
            ],
        ];

        $this->connection
            ->expects($this->once())
            ->method('fetchAllAssociative')
            ->with($this->stringContains('AND `cms_portal_id` = ?'), [$host, $host, $portalId])
            ->willReturn($expectedRows);

        self::assertSame($expectedRows, $this->subject->getDomainCandidatesByHostAndPortal($host, $portalId));
    }

    public function testGetDomainCandidatesByHostReturnsCandidatesAcrossPortals(): void
    {
        $host = 'www.example.com';
        $expectedRows = [
            [
                'id' => 'domain-a-fr',
                'cms_portal_id' => 'portal-a',
                'cms_language_id' => 'lang-fr',
                'name' => $host,
                'sslname' => '',
                'url_suffix' => 'fr',
                'is_master_domain' => '0',
            ],
            [
                'id' => 'domain-b-fr',
                'cms_portal_id' => 'portal-b',
                'cms_language_id' => 'lang-fr',
                'name' => $host,
                'sslname' => '',
                'url_suffix' => 'fr',
                'is_master_domain' => '0',
            ],
        ];

        $this->connection
            ->expects($this->once())
            ->method('fetchAllAssociative')
            ->with($this->anything(), [$host, $host])
            ->willReturn($expectedRows);

        self::assertSame($expectedRows, $this->subject->getDomainCandidatesByHost($host));
    }

    public function testGetDomainDataByNameKeepsLegacyGroupedLookup(): void
    {
        $host = 'www.example.com';
        $expectedRows = [
            [
                'id' => 'legacy-domain',
                'cms_portal_id' => 'portal-a',
            ],
        ];

        $this->connection
            ->expects($this->once())
            ->method('fetchAllAssociative')
            ->with($this->stringContains('GROUP BY `cms_portal_domains`.`cms_portal_id`'), [$host, $host])
            ->willReturn($expectedRows);

        self::assertSame($expectedRows, $this->subject->getDomainDataByName($host));
    }
}
