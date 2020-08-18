<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
 */

declare(strict_types=1);

namespace BitBag\SyliusMolliePlugin\Documentation;

use Symfony\Contracts\Translation\TranslatorInterface;

final class DocumentationLinks implements DocumentationLinksInterface
{
    public const DOCUMENTATION_LINKS = [
        'single_click' => 'https://help.mollie.com/hc/en-us/articles/115000671249-What-are-single-click-payments-and-how-does-it-work-',
        'mollie_components' => 'https://www.mollie.com/en/news/post/better-checkout-flows-with-mollie-components',
        'payment_methods' => 'https://docs.mollie.com/orders/why-use-orders',
    ];

    /** @var TranslatorInterface */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getSingleClickDoc(): string
    {
        return $this->translator->trans('bitbag_sylius_mollie_plugin.ui.read_more_single_click_enabled') .
            '<a target="_blank" href="' . self::DOCUMENTATION_LINKS['single_click'] . '"> ' .
            $this->translator->trans('bitbag_sylius_mollie_plugin.ui.mollie_single_click') .
            '</a>';
    }

    public function getMollieComponentsDoc(): string
    {
        return $this->translator->trans('bitbag_sylius_mollie_plugin.ui.read_more_enable_components') .
            '<a target="_blank" href="' . self::DOCUMENTATION_LINKS['mollie_components'] . '"> ' .
            $this->translator->trans('bitbag_sylius_mollie_plugin.ui.mollie_components') .
            '</a>';
    }

    public function getPaymentMethodDoc(): string
    {
        return $this->translator->trans('bitbag_sylius_mollie_plugin.ui.click') .
            ' <a target="_blank" href="' . self::DOCUMENTATION_LINKS['payment_methods'] . '">' .
            $this->translator->trans('bitbag_sylius_mollie_plugin.ui.here') .
            '</a> ' . $this->translator->trans('bitbag_sylius_mollie_plugin.ui.payment_methods_doc');
    }
}
