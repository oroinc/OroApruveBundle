<?php

namespace Oro\Bundle\ApruveBundle\Controller;

use Oro\Bundle\ApruveBundle\Connection\Validator\ApruveConnectionValidatorInterface;
use Oro\Bundle\ApruveBundle\Connection\Validator\Result\ApruveConnectionValidatorResultInterface;
use Oro\Bundle\ApruveBundle\Connection\Validator\Result\Factory\Merchant;
use Oro\Bundle\ApruveBundle\Entity\ApruveSettings;
use Oro\Bundle\IntegrationBundle\Entity\Channel;
use Oro\Bundle\IntegrationBundle\Form\Type\ChannelType;
use Oro\Bundle\SecurityBundle\Annotation\CsrfProtection;
use Oro\Bundle\SecurityBundle\Generator\RandomTokenGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Apruve Settings Controller
 */
class ApruveSettingsController extends AbstractController
{
    /**
     * @Route("/generate-token", name="oro_apruve_generate_token", options={"expose"=true}, methods={"POST"})
     * @CsrfProtection()
     *
     * @return JsonResponse
     */
    public function generateTokenAction()
    {
        $tokenGenerator = $this->get(RandomTokenGeneratorInterface::class);

        return new JsonResponse([
            'success' => true,
            'token' => $tokenGenerator->generateToken(),
        ]);
    }

    /**
     * @Route("/validate-connection/{channelId}/", name="oro_apruve_validate_connection", methods={"POST"})
     * @ParamConverter("channel", class="OroIntegrationBundle:Channel", options={"id" = "channelId"})
     * @CsrfProtection()
     *
     * @param Request      $request
     * @param Channel|null $channel
     *
     * @return JsonResponse
     */
    public function validateConnectionAction(Request $request, Channel $channel = null)
    {
        if (!$channel) {
            $channel = new Channel();
        }

        $form = $this->createForm(
            ChannelType::class,
            $channel
        );
        $form->handleRequest($request);

        /** @var ApruveSettings $transport */
        $transport = $channel->getTransport();
        $result = $this->get(ApruveConnectionValidatorInterface::class)->validateConnectionByApruveSettings($transport);

        if (!$result->getStatus()) {
            return new JsonResponse([
                'success' => false,
                'message' => $this->getErrorMessageByValidatorResult($result),
            ]);
        }

        return new JsonResponse([
            'success' => true,
            'message' => $this->get(TranslatorInterface::class)
                ->trans('oro.apruve.check_connection.result.success.message'),
        ]);
    }

    /**
     * @param ApruveConnectionValidatorResultInterface $result
     *
     * @return string
     */
    private function getErrorMessageByValidatorResult(ApruveConnectionValidatorResultInterface $result)
    {
        $message = 'oro.apruve.check_connection.result.server_error.message';
        $parameters = [
            '%error_message%' => trim($result->getErrorMessage(), '.')
        ];
        switch ($result->getErrorSeverity()) {
            case Merchant\GetMerchantRequestApruveConnectionValidatorResultFactory::INVALID_API_KEY_SEVERITY:
                $message = 'oro.apruve.check_connection.result.invalid_api_key.message';
                break;
            case Merchant\GetMerchantRequestApruveConnectionValidatorResultFactory::MERCHANT_NOT_FOUND_SEVERITY:
                $message = 'oro.apruve.check_connection.result.merchant_not_found.message';
                break;
        }
        return $this->get(TranslatorInterface::class)->trans($message, $parameters);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedServices(): array
    {
        return array_merge(
            parent::getSubscribedServices(),
            [
                TranslatorInterface::class,
                RandomTokenGeneratorInterface::class,
                ApruveConnectionValidatorInterface::class,

            ]
        );
    }
}
