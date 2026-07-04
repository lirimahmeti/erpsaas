<?php

namespace App\Filament\Exports\Common;

use App\Enums\Accounting\BillStatus;
use App\Models\Common\Vendor;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class VendorExporter extends Exporter
{
    protected static ?string $model = Vendor::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name'),
            ExportColumn::make('type')
                ->enum(),
            ExportColumn::make('contractor_type')
                ->enum(),
            ExportColumn::make('account_number'),
            ExportColumn::make('contact.full_name')
                ->label(translate('Primary contact')),
            ExportColumn::make('contact.email')
                ->label(translate('Email')),
            ExportColumn::make('contact.first_available_phone')
                ->label(translate('Phone')),
            ExportColumn::make('currency_code'),
            ExportColumn::make('balance')
                ->state(function (Vendor $record) {
                    return $record->bills()
                        ->unpaid()
                        ->get()
                        ->sumMoneyInDefaultCurrency('amount_due');
                })
                ->money(),
            ExportColumn::make('overdue_amount')
                ->state(function (Vendor $record) {
                    return $record->bills()
                        ->where('status', BillStatus::Overdue)
                        ->get()
                        ->sumMoneyInDefaultCurrency('amount_due');
                })
                ->money(),
            ExportColumn::make('address.address_string')
                ->label(translate('Address'))
                ->enabledByDefault(false),
            ExportColumn::make('address.address_line_1')
                ->label(translate('Address line 1')),
            ExportColumn::make('address.address_line_2')
                ->label(translate('Address line 2')),
            ExportColumn::make('address.city')
                ->label(translate('City')),
            ExportColumn::make('address.state.name')
                ->label(translate('State')),
            ExportColumn::make('address.postal_code')
                ->label(translate('Postal code')),
            ExportColumn::make('address.country.name')
                ->label(translate('Country')),
            ExportColumn::make('ssn')
                ->label(translate('SSN'))
                ->enabledByDefault(false),
            ExportColumn::make('ein')
                ->label(translate('EIN'))
                ->enabledByDefault(false),
            ExportColumn::make('website')
                ->enabledByDefault(false),
            ExportColumn::make('notes')
                ->enabledByDefault(false),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your vendor export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
