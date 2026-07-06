<?php

namespace App\Filament\Exports\Accounting;

use App\Models\Accounting\Estimate;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class EstimateExporter extends Exporter
{
    protected static ?string $model = Estimate::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('estimate_number')
                ->label(translate('Estimate number')),
            ExportColumn::make('date')
                ->label(translate('Date'))
                ->date(),
            ExportColumn::make('expiration_date')
                ->label(translate('Expiration date'))
                ->date(),
            ExportColumn::make('client.name')
                ->label(translate('Client')),
            ExportColumn::make('status')
                ->label(translate('Status'))
                ->enum(),
            ExportColumn::make('total')
                ->label(translate('Total'))
                ->money(),
            ExportColumn::make('subtotal')
                ->label(translate('Subtotal'))
                ->money(),
            ExportColumn::make('tax_total')
                ->label(translate('Tax total'))
                ->money(),
            ExportColumn::make('discount_total')
                ->label(translate('Discount total'))
                ->money(),
            ExportColumn::make('discount_rate')
                ->label(translate('Discount rate')),
            ExportColumn::make('currency_code')
                ->label(translate('Currency code')),
            ExportColumn::make('reference_number')
                ->label(translate('Reference number')),
            ExportColumn::make('approved_at')
                ->label(translate('Approved at'))
                ->dateTime(),
            ExportColumn::make('accepted_at')
                ->label(translate('Accepted at'))
                ->dateTime(),
            ExportColumn::make('declined_at')
                ->label(translate('Declined at'))
                ->dateTime(),
            ExportColumn::make('converted_at')
                ->label(translate('Converted at'))
                ->dateTime(),
            ExportColumn::make('last_sent_at')
                ->label(translate('Last sent at'))
                ->dateTime(),
            ExportColumn::make('discount_method')
                ->label(translate('Discount method'))
                ->enabledByDefault(false)
                ->enum(),
            ExportColumn::make('discount_computation')
                ->label(translate('Discount computation'))
                ->enabledByDefault(false)
                ->enum(),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = translate('Your estimate export has completed and :count :rows exported.', [
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
