<?php

namespace Oro\Bundle\ApruveBundle\Tests\Unit\Provider;

use Oro\Bundle\ApruveBundle\Provider\TaxAmountProvider;
use Oro\Bundle\PaymentBundle\Context\PaymentContextInterface;
use Oro\Bundle\TaxBundle\Exception\TaxationDisabledException;
use Oro\Bundle\TaxBundle\Mapper\UnmappableArgumentException;
use Oro\Bundle\TaxBundle\Provider\TaxAmountProvider as BaseTaxAmountProvider;
use Oro\Bundle\TestFrameworkBundle\Test\Logger\LoggerAwareTraitTestTrait;

class TaxAmountProviderTest extends \PHPUnit\Framework\TestCase
{
    use LoggerAwareTraitTestTrait;

    /** @var \stdClass|\PHPUnit\Framework\MockObject\MockObject */
    private $sourceEntity;

    /** @var PaymentContextInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $paymentContext;

    /** @var TaxAmountProvider */
    private $taxAmountProvider;

    /** @var BaseTaxAmountProvider|\PHPUnit\Framework\MockObject\MockObject */
    private $baseTaxAmountProvider;

    #[\Override]
    protected function setUp(): void
    {
        $this->sourceEntity = $this->createMock(\stdClass::class);
        $this->paymentContext = $this->createMock(PaymentContextInterface::class);
        $this->baseTaxAmountProvider = $this->createMock(BaseTaxAmountProvider::class);

        $this->paymentContext->expects(self::any())
            ->method('getSourceEntity')
            ->willReturn($this->sourceEntity);

        $this->taxAmountProvider = new TaxAmountProvider($this->baseTaxAmountProvider);

        $this->setUpLoggerMock($this->taxAmountProvider);
    }

    public function testGetTaxAmount(): void
    {
        $this->baseTaxAmountProvider->expects(self::once())
            ->method('getTaxAmount')
            ->with($this->sourceEntity)
            ->willReturn(5.0);

        $this->assertSame(5.0, $this->taxAmountProvider->getTaxAmount($this->paymentContext));
    }

    public function testGetTaxAmountWithTaxationDisabledException(): void
    {
        $this->baseTaxAmountProvider->expects(self::once())
            ->method('getTaxAmount')
            ->with($this->sourceEntity)
            ->willThrowException(new TaxationDisabledException());

        $this->assertLoggerNotCalled();

        $actual = $this->taxAmountProvider->getTaxAmount($this->paymentContext);

        $this->assertNull($actual);
    }

    /**
     * @dataProvider getTaxAmountWithHandledExceptionDataProvider
     */
    public function testGetTaxAmountWithHandledException(string $exceptionClass): void
    {
        $this->baseTaxAmountProvider->expects(self::once())
            ->method('getTaxAmount')
            ->with($this->sourceEntity)
            ->willThrowException(new $exceptionClass());

        $this->assertLoggerErrorMethodCalled();

        $actual = $this->taxAmountProvider->getTaxAmount($this->paymentContext);

        $this->assertNull($actual);
    }

    public function getTaxAmountWithHandledExceptionDataProvider(): array
    {
        return [
            [UnmappableArgumentException::class],
            [\InvalidArgumentException::class],
        ];
    }

    public function testGetTaxAmountWithUnhandledException(): void
    {
        $this->expectException(\Throwable::class);
        $this->baseTaxAmountProvider->expects(self::once())
            ->method('getTaxAmount')
            ->with($this->sourceEntity)
            ->willThrowException(new \Exception());

        $this->taxAmountProvider->getTaxAmount($this->paymentContext);
    }
}
