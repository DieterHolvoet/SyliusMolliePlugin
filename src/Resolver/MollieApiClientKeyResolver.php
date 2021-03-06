<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusMolliePlugin\Resolver;

use BitBag\SyliusMolliePlugin\Client\MollieApiClient;
use BitBag\SyliusMolliePlugin\Factory\MollieGatewayFactory;
use BitBag\SyliusMolliePlugin\Form\Type\MollieGatewayConfigurationType;
use BitBag\SyliusMolliePlugin\Logger\MollieLoggerActionInterface;
use BitBag\SyliusMolliePlugin\Repository\PaymentMethodRepositoryInterface;
use Mollie\Api\Exceptions\ApiException;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Resource\Exception\UpdateHandlingException;

final class MollieApiClientKeyResolver implements MollieApiClientKeyResolverInterface
{
    /** @var MollieApiClient */
    private $mollieApiClient;

    /** @var MollieLoggerActionInterface */
    private $loggerAction;

    /** @var PaymentMethodRepositoryInterface */
    private $paymentMethodRepository;

    /** @var ChannelContextInterface */
    private $channelContext;

    public function __construct(
        MollieApiClient $mollieApiClient,
        MollieLoggerActionInterface $loggerAction,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        ChannelContextInterface $channelContext
    ) {
        $this->mollieApiClient = $mollieApiClient;
        $this->loggerAction = $loggerAction;
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->channelContext = $channelContext;
    }

    public function getClientWithKey(): MollieApiClient
    {
        $paymentMethod = $this->paymentMethodRepository->findOneByChannelAndGatewayFactoryName(
            $this->channelContext->getChannel(),
            MollieGatewayFactory::FACTORY_NAME,
        );

        if (null === $paymentMethod) {
            throw new UpdateHandlingException(sprintf('No payment method found'));
        }

        $gateway = $paymentMethod->getGatewayConfig();

        $config = $gateway->getConfig();

        $environment = true === $config['environment'] ?
            MollieGatewayConfigurationType::API_KEY_LIVE :
            MollieGatewayConfigurationType::API_KEY_TEST;

        try {
            return $this->mollieApiClient->setApiKey($config[$environment]);
        } catch (ApiException $e) {
            $this->loggerAction->addNegativeLog(sprintf('API call failed: %s', $e->getMessage()));

            throw new UpdateHandlingException(sprintf('API call failed: %s', htmlspecialchars($e->getMessage())));
        }
    }
}
