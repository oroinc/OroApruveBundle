<?php

namespace Oro\Bundle\ApruveBundle\Tests\Unit\Form\Type;

use Oro\Bundle\ApruveBundle\Entity\ApruveSettings;
use Oro\Bundle\ApruveBundle\Form\Type\ApruveSettingsType;
use Oro\Bundle\ApruveBundle\Form\Type\WebhookTokenType;
use Oro\Bundle\IntegrationBundle\Provider\TransportInterface;
use Oro\Bundle\LocaleBundle\Entity\LocalizedFallbackValue;
use Oro\Bundle\LocaleBundle\Form\Type\LocalizedFallbackValueCollectionType;
use Oro\Bundle\LocaleBundle\Tests\Unit\Form\Type\Stub\LocalizedFallbackValueCollectionTypeStub;
use Oro\Bundle\SecurityBundle\Form\DataTransformer\Factory\CryptedDataTransformerFactoryInterface;
use Oro\Bundle\SecurityBundle\Generator\RandomTokenGeneratorInterface;
use Oro\Component\Testing\Unit\PreloadedExtension;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Test\FormIntegrationTestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Validation;

class ApruveSettingsTypeTest extends FormIntegrationTestCase
{
    private const ENCRYPTED_API_KEY = 'encryptedApiKeySample';
    private const DECRYPTED_API_KEY = 'apiKeySample';

    private const ENCRYPTED_MERCHANT_ID = 'encryptedMerchantIdSample';
    private const DECRYPTED_MERCHANT_ID = 'merchantIdSample';

    private const LABEL = 'Apruve';
    private const SHORT_LABEL = 'Apruve (short)';
    private const TEST_MODE = true;
    private const WEBHOOK_TOKEN = 'webhookTokenSample';

    /** @var DataTransformerInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $dataTransformer;

    /** @var RandomTokenGeneratorInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $tokenGenerator;

    /** @var TransportInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $transport;

    /** @var ApruveSettingsType */
    private $formType;

    protected function setUp(): void
    {
        $this->dataTransformer = $this->createMock(DataTransformerInterface::class);

        $this->transport = $this->createMock(TransportInterface::class);
        $this->transport->expects(self::any())
            ->method('getSettingsEntityFQCN')
            ->willReturn(ApruveSettings::class);

        $this->tokenGenerator = $this->createMock(RandomTokenGeneratorInterface::class);
        $this->tokenGenerator->expects(self::any())
            ->method('generateToken')
            ->willReturn('webhookTokenSample');

        $cryptedDataTransformerFactory = $this->createMock(CryptedDataTransformerFactoryInterface::class);
        $cryptedDataTransformerFactory->expects(self::any())
            ->method('create')
            ->willReturn($this->dataTransformer);

        $this->formType = new ApruveSettingsType($this->transport, $cryptedDataTransformerFactory);

        parent::setUp();
    }

    /**
     * {@inheritDoc}
     */
    protected function getExtensions(): array
    {
        return [
            new PreloadedExtension([
                $this->formType,
                new WebhookTokenType($this->tokenGenerator),
                LocalizedFallbackValueCollectionType::class => new LocalizedFallbackValueCollectionTypeStub(),
            ], []),
            new ValidatorExtension(Validation::createValidator())
        ];
    }

    /**
     * @dataProvider submitDataProvider
     */
    public function testSubmit(
        ApruveSettings $defaultData,
        array $submittedData,
        bool $isValid,
        ApruveSettings $expectedData
    ) {
        $this->dataTransformer->expects(self::any())
            ->method('reverseTransform')
            ->willReturnMap([
                [null, null],
                [self::DECRYPTED_MERCHANT_ID, self::ENCRYPTED_MERCHANT_ID],
                [self::DECRYPTED_API_KEY, self::ENCRYPTED_API_KEY],
            ]);

        $form = $this->factory->create(ApruveSettingsType::class, $defaultData, []);

        self::assertEquals($defaultData, $form->getData());

        $form->submit($submittedData);

        self::assertEquals($isValid, $form->isValid());
        self::assertTrue($form->isSynchronized());
        self::assertEquals($expectedData, $form->getData());
    }

    public function submitDataProvider(): array
    {
        $label = (new LocalizedFallbackValue())->setString(self::LABEL);
        $shortLabel = (new LocalizedFallbackValue())->setString(self::SHORT_LABEL);

        return [
            'empty form' => [
                'defaultData' => new ApruveSettings(),
                'submittedData' => [],
                'isValid' => true,
                'expectedData' => (new ApruveSettings())
                    ->setApruveWebhookToken(self::WEBHOOK_TOKEN)
            ],
            'not empty form' => [
                'defaultData' => new ApruveSettings(),
                'submittedData' => [
                    'labels' => [['string' => self::LABEL]],
                    'shortLabels' => [['string' => self::SHORT_LABEL]],
                    'apruveTestMode' => self::TEST_MODE,
                    'apruveMerchantId' => self::DECRYPTED_MERCHANT_ID,
                    'apruveApiKey' => self::DECRYPTED_API_KEY,
                    'apruveWebhookToken' => self::WEBHOOK_TOKEN,
                ],
                'isValid' => true,
                'expectedData' => (new ApruveSettings())
                    ->addLabel($label)
                    ->addShortLabel($shortLabel)
                    ->setApruveTestMode(self::TEST_MODE)
                    ->setApruveMerchantId(self::ENCRYPTED_MERCHANT_ID)
                    ->setApruveApiKey(self::ENCRYPTED_API_KEY)
                    ->setApruveWebhookToken(self::WEBHOOK_TOKEN)
            ]
        ];
    }

    public function testConfigureOptions()
    {
        $resolver = $this->createMock(OptionsResolver::class);
        $resolver->expects(self::once())
            ->method('setDefaults')
            ->with([
                'data_class' => $this->transport->getSettingsEntityFQCN(),
            ]);

        $this->formType->configureOptions($resolver);
    }

    public function testGetBlockPrefix()
    {
        self::assertEquals(ApruveSettingsType::BLOCK_PREFIX, $this->formType->getBlockPrefix());
    }
}
