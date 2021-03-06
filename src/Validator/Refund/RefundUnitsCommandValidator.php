<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusMolliePlugin\Validator\Refund;

use BitBag\SyliusMolliePlugin\Checker\Refund\DuplicateRefundTheSameAmountCheckerInterface;
use Sylius\RefundPlugin\Checker\OrderRefundingAvailabilityCheckerInterface;
use Sylius\RefundPlugin\Command\RefundUnits;
use Sylius\RefundPlugin\Exception\InvalidRefundAmountException;
use Sylius\RefundPlugin\Exception\OrderNotAvailableForRefundingException;
use Sylius\RefundPlugin\Model\RefundType;
use Sylius\RefundPlugin\Validator\RefundAmountValidatorInterface;
use Sylius\RefundPlugin\Validator\RefundUnitsCommandValidatorInterface;

final class RefundUnitsCommandValidator implements RefundUnitsCommandValidatorInterface
{
    /** @var OrderRefundingAvailabilityCheckerInterface */
    private $orderRefundingAvailabilityChecker;

    /** @var RefundAmountValidatorInterface */
    private $refundAmountValidator;

    /** @var DuplicateRefundTheSameAmountCheckerInterface */
    private $duplicateRefundTheSameAmountChecker;

    public function __construct(
        OrderRefundingAvailabilityCheckerInterface $orderRefundingAvailabilityChecker,
        RefundAmountValidatorInterface $refundAmountValidator,
        DuplicateRefundTheSameAmountCheckerInterface $duplicateRefundTheSameAmountChecker
    ) {
        $this->orderRefundingAvailabilityChecker = $orderRefundingAvailabilityChecker;
        $this->refundAmountValidator = $refundAmountValidator;
        $this->duplicateRefundTheSameAmountChecker = $duplicateRefundTheSameAmountChecker;
    }

    public function validate(RefundUnits $command): void
    {
        if (!$this->orderRefundingAvailabilityChecker->__invoke($command->orderNumber())) {
            throw OrderNotAvailableForRefundingException::withOrderNumber($command->orderNumber());
        }

        $this->refundAmountValidator->validateUnits($command->units(), RefundType::orderItemUnit());
        $this->refundAmountValidator->validateUnits($command->shipments(), RefundType::shipment());

        if (true === $this->duplicateRefundTheSameAmountChecker->check($command)) {
            throw new InvalidRefundAmountException('A duplicate refund has been detected');
        }
    }
}
