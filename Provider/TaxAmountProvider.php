<?php

namespace Oro\Bundle\ApruveBundle\Provider;

use Oro\Bundle\PaymentBundle\Context\PaymentContextInterface;
use Oro\Bundle\TaxBundle\Exception\TaxationDisabledException;
use Oro\Bundle\TaxBundle\Mapper\UnmappableArgumentException;
use Oro\Bundle\TaxBundle\Provider\TaxAmountProvider as BaseTaxAmountProvider;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

/**
 * Provides tax amount for entity.
 */
class TaxAmountProvider implements TaxAmountProviderInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var BaseTaxAmountProvider
     */
    private $taxAmountProvider;

    /**
     * @param BaseTaxAmountProvider $taxAmountProvider
     */
    public function __construct(BaseTaxAmountProvider $taxAmountProvider)
    {
        $this->taxAmountProvider = $taxAmountProvider;
    }

    /**
     * {@inheritDoc}
     */
    public function getTaxAmount(PaymentContextInterface $paymentContext): ?float
    {
        try {
            $taxAmount = $this->taxAmountProvider->getTaxAmount($paymentContext->getSourceEntity());
        } catch (TaxationDisabledException $e) {
            // We can not return any tax information in this case
            // because even 0 means that taxes calculated with 0 value
            return null;
        } catch (\InvalidArgumentException|UnmappableArgumentException $e) {
            if ($this->logger) {
                $this->logger->error('Can not get tax amount for the required payment context', ['exception' => $e]);
            }

            // We can not return any tax information in this case
            // because even 0 means that taxes calculated with 0 value
            return null;
        }

        return $taxAmount;
    }
}
