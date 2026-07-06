<?php

namespace App\Filament\Exports\Common;

use App\Models\Common\Client;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ClientExporter extends Exporter
{
    protected static ?string $model = Client::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')
                ->label(translate('Name')),
            ExportColumn::make('account_number')
                ->label(translate('Account number')),
            ExportColumn::make('primaryContact.full_name')
                ->label(translate('Primary contact')),
            ExportColumn::make('primaryContact.email')
                ->label(translate('Email')),
            ExportColumn::make('primaryContact.first_available_phone')
                ->label(translate('Phone')),
            ExportColumn::make('currency_code')
                ->label(translate('Currency code')),
            ExportColumn::make('balance') // TODO: Potentially find an easier way to calculate this
                ->label(translate('Balance'))
                ->state(function (Client $record) {
                    return $record->invoices()
                        ->unpaid()
                        ->get()
                        ->sumMoneyInDefaultCurrency('amount_due');
                })
                ->money(),
            ExportColumn::make('overdue_amount')
                ->label(translate('Overdue amount'))
                ->state(function (Client $record) {
                    return $record->invoices()
                        ->overdue()
                        ->get()
                        ->sumMoneyInDefaultCurrency('amount_due');
                })
                ->money(),
            ExportColumn::make('billingAddress.address_string')
                ->label(translate('Billing address'))
                ->enabledByDefault(false),
            ExportColumn::make('billingAddress.address_line_1')
                ->label(translate('Billing address line 1')),
            ExportColumn::make('billingAddress.address_line_2')
                ->label(translate('Billing address line 2')),
            ExportColumn::make('billingAddress.city')
                ->label(translate('Billing city')),
            ExportColumn::make('billingAddress.state.name')
                ->label(translate('Billing state')),
            ExportColumn::make('billingAddress.postal_code')
                ->label(translate('Billing postal code')),
            ExportColumn::make('billingAddress.country.name')
                ->label(translate('Billing country')),
            ExportColumn::make('shippingAddress.recipient')
                ->label(translate('Shipping recipient'))
                ->enabledByDefault(false),
            ExportColumn::make('shippingAddress.phone')
                ->label(translate('Shipping phone'))
                ->enabledByDefault(false),
            ExportColumn::make('shippingAddress.address_string')
                ->label(translate('Shipping address'))
                ->enabledByDefault(false),
            ExportColumn::make('shippingAddress.address_line_1')
                ->label(translate('Shipping address line 1'))
                ->enabledByDefault(false),
            ExportColumn::make('shippingAddress.address_line_2')
                ->label(translate('Shipping address line 2'))
                ->enabledByDefault(false),
            ExportColumn::make('shippingAddress.city')
                ->label(translate('Shipping city'))
                ->enabledByDefault(false),
            ExportColumn::make('shippingAddress.state.name')
                ->label(translate('Shipping state'))
                ->enabledByDefault(false),
            ExportColumn::make('shippingAddress.postal_code')
                ->label(translate('Shipping postal code'))
                ->enabledByDefault(false),
            ExportColumn::make('shippingAddress.country.name')
                ->label(translate('Shipping country'))
                ->enabledByDefault(false),
            ExportColumn::make('shippingAddress.notes')
                ->label(translate('Delivery instructions'))
                ->enabledByDefault(false),
            ExportColumn::make('website')
                ->label(translate('Website'))
                ->enabledByDefault(false),
            ExportColumn::make('notes')
                ->label(translate('Notes'))
                ->enabledByDefault(false),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = translate('Your client export has completed and :count :rows exported.', [
            'count' => number_format($export->successful_rows),
            'rows' => translate(str('row')->plural($export->successful_rows)->toString()),
        ]);

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.translate(':count :rows failed to export.', [
                'count' => number_format($failedRowsCount),
                'rows' => translate(str('row')->plural($failedRowsCount)->toString()),
            ]);
        }

        return $body;
    }
}
