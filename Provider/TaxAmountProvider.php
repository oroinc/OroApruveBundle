<?php

namespace Oro\Bundle\ApruveBundle\Provider;

use Oro\Bundle\PaymentBundle\Context\PaymentContextInterface;
use Oro\Bundle\TaxBundle\Exception\TaxationDisabledException;
use Oro\Bundle\TaxBundle\Mapper\UnmappableArgumentException;
use Oro\Bundle\TaxBundle\Provider\TaxProviderInterface;
use Oro\Bundle\TaxBundle\Provider\TaxProviderRegistry;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class TaxAmountProvider implements TaxAmountProviderInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var TaxProviderRegistry
     */
    private $taxProviderRegistry;

    /**.
     * @param TaxProviderRegistry $taxProviderRegistry
     */
    public function __construct(TaxProviderRegistry $taxProviderRegistry)
    {
        $this->taxProviderRegistry = $taxProviderRegistry;
    }

    /**
     * {@inheritDoc}
     */
    public function getTaxAmount(PaymentContextInterface $paymentContext)
    {
        try {
            $result = $this->getProvider()->loadTax($paymentContext->getSourceEntity());
            $taxAmount = $result->getTotal()->getTaxAmount();
        } catch (TaxationDisabledException $e) {
            $taxAmount = 0;
        } catch (UnmappableArgumentException $e) {
            if ($this->logger) {
                $this->logger->warning($e->getMessage());
            }

            // There are no tax mapper for given source entity.
            $taxAmount = 0;
        } catch (\InvalidArgumentException $e) {
            if ($this->logger) {
                $this->logger->warning($e->getMessage());
            }

            $taxAmount = 0;
        }

        if (abs((float)$taxAmount) <= 1e-6) {
            $taxAmount = 0;
        }

        return (float) $taxAmount;
    }

    /**
     * @return TaxProviderInterface
     */
    private function getProvider()
    {
        return $this->taxProviderRegistry->getEnabledProvider();
    }
}
