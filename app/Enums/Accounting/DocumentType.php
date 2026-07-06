<?php

namespace App\Enums\Accounting;

use App\DTO\DocumentLabelDTO;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum DocumentType: string implements HasIcon, HasLabel
{
    case Invoice = 'invoice';
    case Bill = 'bill';
    case Estimate = 'estimate';
    case RecurringInvoice = 'recurring_invoice';

    public const DEFAULT = self::Invoice->value;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Invoice, self::Bill, self::Estimate => $this->name,
            self::RecurringInvoice => 'Recurring Invoice',
        };
    }

    public function getPluralLabel(): ?string
    {
        return Str::plural($this->getLabel());
    }

    public function getIcon(): ?string
    {
        return match ($this->value) {
            self::Invoice->value, self::RecurringInvoice->value => 'heroicon-o-document-duplicate',
            self::Bill->value => 'heroicon-o-clipboard-document-list',
            self::Estimate->value => 'heroicon-o-document-text',
        };
    }

    public function getTaxKey(): string
    {
        return match ($this) {
            self::Invoice, self::RecurringInvoice, self::Estimate => 'salesTaxes',
            self::Bill => 'purchaseTaxes',
        };
    }

    public function getDiscountKey(): string
    {
        return match ($this) {
            self::Invoice, self::RecurringInvoice, self::Estimate => 'salesDiscounts',
            self::Bill => 'purchaseDiscounts',
        };
    }

    public function getLabels(): DocumentLabelDTO
    {
        return match ($this) {
            self::Invoice => new DocumentLabelDTO(
                title: translate(self::Invoice->getLabel()),
                number: translate('Invoice Number'),
                referenceNumber: translate('P.O/S.O Number'),
                date: translate('Invoice Date'),
                dueDate: translate('Payment Due'),
                amountDue: translate('Amount Due'),
            ),
            self::RecurringInvoice => new DocumentLabelDTO(
                title: translate(self::RecurringInvoice->getLabel()),
                number: translate('Invoice Number'),
                referenceNumber: translate('P.O/S.O Number'),
                date: translate('Invoice Date'),
                dueDate: translate('Payment Due'),
                amountDue: translate('Amount Due'),
            ),
            self::Estimate => new DocumentLabelDTO(
                title: translate(self::Estimate->getLabel()),
                number: translate('Estimate Number'),
                referenceNumber: translate('Reference Number'),
                date: translate('Estimate Date'),
                dueDate: translate('Expiration Date'),
                amountDue: null,
            ),
            self::Bill => new DocumentLabelDTO(
                title: translate(self::Bill->getLabel()),
                number: translate('Bill Number'),
                referenceNumber: translate('P.O/S.O Number'),
                date: translate('Bill Date'),
                dueDate: translate('Payment Due'),
                amountDue: translate('Amount Due'),
            ),
        };
    }

    public function getDefaultPrefix(): ?string
    {
        return match ($this) {
            self::Invoice => 'INV-',
            self::Estimate => 'EST-',
            self::Bill => 'BILL-',
            default => null,
        };
    }
}
