<?php

namespace Mautic\LeadBundle\Tests\Segment\Decorator\Date\Year;

use Mautic\CoreBundle\Helper\DateTimeHelper;
use Mautic\LeadBundle\Segment\ContactSegmentFilterCrate;
use Mautic\LeadBundle\Segment\Decorator\Date\DateOptionAbstract;
use Mautic\LeadBundle\Segment\Decorator\Date\DateOptionParameters;
use Mautic\LeadBundle\Segment\Decorator\Date\TimezoneResolver;
use Mautic\LeadBundle\Segment\Decorator\Date\Year\DateYearThis;
use Mautic\LeadBundle\Segment\Decorator\DateDecorator;

class DateYearThisTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers \Mautic\LeadBundle\Segment\Decorator\Date\Year\DateYearThis::getOperator
     */
    public function testGetOperatorBetween()
    {
        $dateDecorator    = $this->createMock(DateDecorator::class);
        $timezoneResolver = $this->createMock(TimezoneResolver::class);

        $filter        = [
            'operator' => '=',
        ];
        $contactSegmentFilterCrate = new ContactSegmentFilterCrate($filter);
        $dateOptionParameters      = new DateOptionParameters($contactSegmentFilterCrate, [], $timezoneResolver);

        $filterDecorator = new DateYearThis($dateDecorator, $dateOptionParameters);

        $this->assertEquals('like', $filterDecorator->getOperator($contactSegmentFilterCrate));
    }

    /**
     * @covers \Mautic\LeadBundle\Segment\Decorator\Date\Year\DateYearThis::getOperator
     */
    public function testGetOperatorLessOrEqual()
    {
        $dateDecorator    = $this->createMock(DateDecorator::class);
        $timezoneResolver = $this->createMock(TimezoneResolver::class);

        $dateDecorator->method('getOperator')
            ->with()
            ->willReturn('==<<');

        $filter        = [
            'operator' => 'lte',
        ];
        $contactSegmentFilterCrate = new ContactSegmentFilterCrate($filter);
        $dateOptionParameters      = new DateOptionParameters($contactSegmentFilterCrate, [], $timezoneResolver);

        $filterDecorator = new DateYearThis($dateDecorator, $dateOptionParameters);

        $this->assertEquals('==<<', $filterDecorator->getOperator($contactSegmentFilterCrate));
    }

    /**
     * @covers \Mautic\LeadBundle\Segment\Decorator\Date\Year\DateYearThis::getParameterValue
     */
    public function testGetParameterValueBetween()
    {
        $dateDecorator    = $this->createMock(DateDecorator::class);
        $timezoneResolver = $this->createMock(TimezoneResolver::class);

        $date = new DateTimeHelper('', null, 'local');

        $timezoneResolver->method('getDefaultDate')
            ->with()
            ->willReturn($date);

        $filter        = [
            'operator' => '!=',
        ];
        $contactSegmentFilterCrate = new ContactSegmentFilterCrate($filter);
        $dateOptionParameters      = new DateOptionParameters($contactSegmentFilterCrate, [], $timezoneResolver);

        $filterDecorator = new DateYearThis($dateDecorator, $dateOptionParameters);

        $expectedDate = new \DateTime('first day of january this year');

        $this->assertEquals($expectedDate->format('Y-%'), $filterDecorator->getParameterValue($contactSegmentFilterCrate));
    }

    /**
     * @covers \Mautic\LeadBundle\Segment\Decorator\Date\Year\DateYearThis::getParameterValue
     */
    public function testGetParameterValueSingle()
    {
        $dateDecorator    = $this->createMock(DateDecorator::class);
        $timezoneResolver = $this->createMock(TimezoneResolver::class);

        $date = new DateTimeHelper('', null, 'local');

        $timezoneResolver->method('getDefaultDate')
            ->with()
            ->willReturn($date);

        $filter        = [
            'operator' => 'lt',
        ];
        $contactSegmentFilterCrate = new ContactSegmentFilterCrate($filter);
        $dateOptionParameters      = new DateOptionParameters($contactSegmentFilterCrate, [], $timezoneResolver);

        $filterDecorator = new DateYearThis($dateDecorator, $dateOptionParameters);

        $expectedDate = new \DateTime('first day of january this year');

        $this->assertEquals($expectedDate->format('Y-m-d'), $filterDecorator->getParameterValue($contactSegmentFilterCrate));
    }

    /**
     * @covers \Mautic\LeadBundle\Segment\Decorator\Date\Month\DateMonthThis::getParameterValue
     */
    public function testGetParameterValueBetweenDateTimeTimezone()
    {
        $dateDecorator    = $this->createMock(DateDecorator::class);
        $timezoneResolver = $this->createMock(TimezoneResolver::class);

        $date = new DateTimeHelper(DateYearThis::MIDNIGHT_FIRST_DAY_OF_JANUARY_THIS_YEAR, null, 'Europe/Paris');

        $timezoneResolver->method('getDefaultDate')
            ->with()
            ->willReturn($date);

        $timezoneResolver->method('getDefaultTimezone')
            ->with()
            ->willReturn('Europe/Paris');

        $filter        = [
            'operator' => '!=',
            'type'     => 'datetime',
        ];
        $contactSegmentFilterCrate = new ContactSegmentFilterCrate($filter);
        $dateOptionParameters      = new DateOptionParameters($contactSegmentFilterCrate, [], $timezoneResolver);

        $filterDecorator = new DateYearThis($dateDecorator, $dateOptionParameters);

        $startDate = $date->toUtcString(DateOptionAbstract::Y_M_D_H_I_S);
        $date->modify('+1 year -1 second');
        $endDate = $date->toUtcString(DateOptionAbstract::Y_M_D_H_I_S);

        $this->assertEquals([$startDate, $endDate], $filterDecorator->getParameterValue($contactSegmentFilterCrate));
    }
}
