<?php

namespace Oro\Bundle\ApruveBundle\Tests\Unit;

use Oro\Bundle\ApruveBundle\Provider\TaxAmountProvider;
use Oro\Bundle\PaymentBundle\Context\PaymentContextInterface;
use Oro\Bundle\TaxBundle\Exception\TaxationDisabledException;
use Oro\Bundle\TaxBundle\Mapper\UnmappableArgumentException;
use Oro\Bundle\TaxBundle\Provider\TaxProviderInterface;
use Oro\Bundle\TaxBundle\Provider\TaxProviderRegistry;
use Oro\Bundle\TestFrameworkBundle\Test\Logger\LoggerAwareTraitTestTrait;

class TaxAmountProviderTest extends \PHPUnit\Framework\TestCase
{
    use LoggerAwareTraitTestTrait;

    const AMOUNT = 10.0;
    const AMOUNT_NEGLIGIBLE = 0.000001;

    /**
     * @var \stdClass|\PHPUnit\Framework\MockObject\MockObject
     */
    private $sourceEntity;

    /**
     * @var PaymentContextInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $paymentContext;

    /**
     * @var TaxProviderInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $taxProvider;

    /**
     * @var TaxAmountProvider
     */
    private $provider;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->sourceEntity = $this->createMock(\stdClass::class);

        $this->paymentContext = $this->createMock(PaymentContextInterface::class);
        $this->paymentContext
            ->method('getSourceEntity')
            ->willReturn($this->sourceEntity);

        $this->taxProvider = $this->createMock(TaxProviderInterface::class);
        $taxProviderRegistry = $this->createMock(TaxProviderRegistry::class);
        $taxProviderRegistry->expects($this->any())
            ->method('getEnabledProvider')
            ->willReturn($this->taxProvider);

        $this->provider = new TaxAmountProvider($taxProviderRegistry);

        $this->setUpLoggerMock($this->provider);
    }

    /**
     * @dataProvider getTaxAmountDataProvider
     *
     * @param float $taxAmount
     * @param float $expectedAmount
     */
    public function testGetTaxAmount($taxAmount, $expectedAmount)
    {
        // Oro\Bundle\TaxBundle\Model\ResultElement is final and cannot be mocked.
        $taxResultElement = $this->getMockBuilder(\stdClass::class)
            ->setMethods(['getTaxAmount'])
            ->getMock();
        $taxResultElement
            ->expects(static::once())
            ->method('getTaxAmount')
            ->willReturn($taxAmount);

        // Oro\Bundle\TaxBundle\Model\Result is final and cannot be mocked.
        $taxResult = $this->getMockBuilder(\stdClass::class)
            ->setMethods(['getTotal'])
            ->getMock();

        $taxResult
            ->expects(static::once())
            ->method('getTotal')
            ->willReturn($taxResultElement);

        $this->taxProvider
            ->expects(static::once())
            ->method('loadTax')
            ->with($this->sourceEntity)
            ->willReturn($taxResult);

        $actual = $this->provider->getTaxAmount($this->paymentContext);

        static::assertSame($expectedAmount, $actual);
    }

    /**
     * @return array
     */
    public function getTaxAmountDataProvider()
    {
        return [
            [self::AMOUNT, self::AMOUNT],
            [self::AMOUNT_NEGLIGIBLE, 0.0],
        ];
    }

    public function testGetTaxAmountIfTaxationIsDisabled()
    {
        $this->taxProvider
            ->expects(static::once())
            ->method('loadTax')
            ->with($this->sourceEntity)
            ->willThrowException(new TaxationDisabledException());

        $actual = $this->provider->getTaxAmount($this->paymentContext);
        static::assertSame(0.0, $actual);
    }

    public function testGetTaxAmountIfIsNotMappable()
    {
        $this->taxProvider
            ->expects(static::once())
            ->method('loadTax')
            ->with($this->sourceEntity)
            ->willThrowException(new UnmappableArgumentException());

        $this->assertLoggerWarningMethodCalled();

        $actual = $this->provider->getTaxAmount($this->paymentContext);
        static::assertSame(0.0, $actual);
    }

    public function testGetTaxAmountIfEntityIsInvalid()
    {
        $this->taxProvider
            ->expects(static::once())
            ->method('loadTax')
            ->with($this->sourceEntity)
            ->willThrowException(new \InvalidArgumentException());

        $this->assertLoggerWarningMethodCalled();

        $actual = $this->provider->getTaxAmount($this->paymentContext);
        static::assertSame(0.0, $actual);
    }
}
