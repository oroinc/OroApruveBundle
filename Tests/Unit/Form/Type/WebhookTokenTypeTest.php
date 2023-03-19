<?php

namespace Oro\Bundle\ApruveBundle\Tests\Unit\Form\Type;

use Oro\Bundle\ApruveBundle\Form\Type\WebhookTokenType;
use Oro\Bundle\SecurityBundle\Generator\RandomTokenGeneratorInterface;
use Oro\Component\Testing\ReflectionUtil;
use Oro\Component\Testing\Unit\PreloadedExtension;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Test\FormIntegrationTestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WebhookTokenTypeTest extends FormIntegrationTestCase
{
    /** @var RandomTokenGeneratorInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $tokenGenerator;

    /** @var WebhookTokenType */
    private $formType;

    protected function setUp(): void
    {
        $this->tokenGenerator = $this->createMock(RandomTokenGeneratorInterface::class);
        $this->tokenGenerator->expects(self::any())
            ->method('generateToken')
            ->with(256)
            ->willReturn('webhookTokenSample');

        $this->formType = new WebhookTokenType($this->tokenGenerator);

        parent::setUp();
    }

    public function testConstructor()
    {
        $formType = new WebhookTokenType($this->tokenGenerator);
        self::assertSame($this->tokenGenerator, ReflectionUtil::getPropertyValue($formType, 'generator'));
    }

    /**
     * {@inheritDoc}
     */
    protected function getExtensions(): array
    {
        return [
            new PreloadedExtension([$this->formType], [])
        ];
    }

    /**
     * @dataProvider submitProvider
     */
    public function testSubmit(?string $defaultData, string $submittedData, string $expectedData)
    {
        $form = $this->factory->create(WebhookTokenType::class, $defaultData);

        $form->submit($submittedData);
        $this->assertTrue($form->isValid());
        $this->assertTrue($form->isSynchronized());

        $actualData = $form->getData();
        $this->assertEquals($expectedData, $actualData);
    }

    public function submitProvider(): array
    {
        return [
            'submit with empty data' => [
                'defaultData' => null,
                'submittedData' => '',
                'expectedData' => 'webhookTokenSample',
            ],
            'submit with existing data' => [
                'defaultData' => 'existingWebhookTokenSample',
                'submittedData' => 'existingWebhookTokenSample',
                'expectedData' => 'existingWebhookTokenSample',
            ],
        ];
    }

    public function testConfigureOptions()
    {
        $resolver = new OptionsResolver();
        $this->formType->configureOptions($resolver);

        $defaultOptions = $resolver->getDefinedOptions();
        $this->assertContains('empty_data', $defaultOptions);
    }

    public function testGetBlockPrefix()
    {
        self::assertEquals(WebhookTokenType::BLOCK_PREFIX, $this->formType->getBlockPrefix());
    }

    public function testGetParent()
    {
        self::assertEquals(HiddenType::class, $this->formType->getParent());
    }
}
