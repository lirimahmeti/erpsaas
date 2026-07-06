<?php

namespace App\Filament\Exports\Accounting;

use App\Models\Accounting\Invoice;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class InvoiceExporter extends Exporter
{
    protected static ?string $model = Invoice::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('invoice_number')
                ->label(translate('Invoice number')),
            ExportColumn::make('date')
                ->label(translate('Date'))
                ->date(),
            ExportColumn::make('due_date')
                ->label(translate('Due date'))
                ->date(),
            ExportColumn::make('client.name')
                ->label(translate('Client')),
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
            ExportColumn::make('approved_at')
                ->label(translate('Approved at'))
                ->dateTime(),
            ExportColumn::make('paid_at')
                ->label(translate('Paid at'))
                ->dateTime(),
            ExportColumn::make('last_sent_at')
                ->label(translate('Last sent at'))
                ->dateTime(),
            ExportColumn::make('estimate.estimate_number')
                ->label(translate('Estimate number'))
                ->enabledByDefault(false),
            ExportColumn::make('recurringInvoice.order_number')
                ->label(translate('Recurring invoice number'))
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
        $body = translate('Your invoice export has completed and :count :rows exported.', [
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
