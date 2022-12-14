<?php

declare(strict_types=1);

namespace Milex\LeadBundle\Tests\Segment\Query\Filter;

use Doctrine\DBAL\Connection;
use Milex\LeadBundle\Segment\ContactSegmentFilter;
use Milex\LeadBundle\Segment\DoNotContact\DoNotContactParts;
use Milex\LeadBundle\Segment\Query\Filter\DoNotContactFilterQueryBuilder;
use Milex\LeadBundle\Segment\Query\QueryBuilder;
use Milex\LeadBundle\Segment\RandomParameterName;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;

class DoNotContactFilterQueryBuilderTest extends TestCase
{
    public function testGetServiceId(): void
    {
        Assert::assertSame('milex.lead.query.builder.special.dnc', DoNotContactFilterQueryBuilder::getServiceId());
    }

    /**
     * @dataProvider dataApplyQuery
     */
    public function testApplyQuery(string $operator, string $parameterValue, string $expectedQuery): void
    {
        $queryBuilder = new QueryBuilder($this->createConnection());
        $queryBuilder->select('1');
        $queryBuilder->from(MILEX_TABLE_PREFIX.'leads', 'l');

        $filter             = $this->createFilter($operator, $parameterValue);
        $filterQueryBuilder = new DoNotContactFilterQueryBuilder(new RandomParameterName(), new EventDispatcher());

        $expectedQuery = str_replace('__MILEX_TABLE_PREFIX__', MILEX_TABLE_PREFIX, $expectedQuery);
        Assert::assertSame($queryBuilder, $filterQueryBuilder->applyQuery($queryBuilder, $filter));
        Assert::assertSame($expectedQuery, $queryBuilder->getDebugOutput());
    }

    /**
     * @return iterable<array<string>>
     */
    public function dataApplyQuery(): iterable
    {
        yield ['eq', '1', 'SELECT 1 FROM __MILEX_TABLE_PREFIX__leads l WHERE l.id IN (SELECT par0.lead_id FROM __MILEX_TABLE_PREFIX__lead_donotcontact par0 WHERE (par0.reason = 1) AND (par0.channel = \'email\'))'];
        yield ['eq', '0', 'SELECT 1 FROM __MILEX_TABLE_PREFIX__leads l WHERE l.id NOT IN (SELECT par0.lead_id FROM __MILEX_TABLE_PREFIX__lead_donotcontact par0 WHERE (par0.reason = 1) AND (par0.channel = \'email\'))'];
        yield ['neq', '1', 'SELECT 1 FROM __MILEX_TABLE_PREFIX__leads l WHERE l.id NOT IN (SELECT par0.lead_id FROM __MILEX_TABLE_PREFIX__lead_donotcontact par0 WHERE (par0.reason = 1) AND (par0.channel = \'email\'))'];
        yield ['neq', '0', 'SELECT 1 FROM __MILEX_TABLE_PREFIX__leads l WHERE l.id IN (SELECT par0.lead_id FROM __MILEX_TABLE_PREFIX__lead_donotcontact par0 WHERE (par0.reason = 1) AND (par0.channel = \'email\'))'];
    }

    private function createConnection(): Connection
    {
        return new class() extends Connection {
            /** @noinspection PhpMissingParentConstructorInspection */
            public function __construct()
            {
            }
        };
    }

    private function createFilter(string $operator, string $parameterValue): ContactSegmentFilter
    {
        return new class($operator, $parameterValue) extends ContactSegmentFilter {
            /**
             * @var string
             */
            private $operator;

            /**
             * @var string
             */
            private $parameterValue;

            /** @noinspection PhpMissingParentConstructorInspection */
            public function __construct(string $operator, string $parameterValue)
            {
                $this->operator       = $operator;
                $this->parameterValue = $parameterValue;
            }

            public function getDoNotContactParts()
            {
                return new DoNotContactParts('dnc_unsubscribed');
            }

            public function getOperator()
            {
                return $this->operator;
            }

            public function getParameterValue()
            {
                return $this->parameterValue;
            }

            public function getGlue()
            {
                return 'and';
            }
        };
    }
}
