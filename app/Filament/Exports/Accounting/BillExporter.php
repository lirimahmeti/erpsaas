<?php

namespace App\Filament\Exports\Accounting;

use App\Models\Accounting\Bill;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class BillExporter extends Exporter
{
    protected static ?string $model = Bill::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('bill_number')
                ->label(translate('Bill number')),
            ExportColumn::make('date')
                ->label(translate('Date'))
                ->date(),
            ExportColumn::make('due_date')
                ->label(translate('Due date'))
                ->date(),
            ExportColumn::make('vendor.name')
                ->label(translate('Vendor')),
            ExportColumn::make('status')
                ->label(translate('Status'))
                ->enum(),
            ExportColumn::make('total')
                ->label(translate('Total'))
                ->money(),
            ExportColumn::make('amount_paid')
                ->label(translate('Amount paid'))
                ->money(),
            ExportColumn::make('amount_due')
                ->label(translate('Amount due'))
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
            ExportColumn::make('order_number')
                ->label(translate('P.O/S.O Number')),
            ExportColumn::make('paid_at')
                ->label(translate('Paid at'))
                ->dateTime(),
            ExportColumn::make('notes')
                ->label(translate('Notes'))
                ->enabledByDefault(false),
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
        $body = translate('Your bill export has completed and :count :rows exported.', [
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
