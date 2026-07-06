<?php

namespace App\Filament\Exports\Accounting;

use App\Models\Accounting\RecurringInvoice;
use Carbon\Carbon;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class RecurringInvoiceExporter extends Exporter
{
    protected static ?string $model = RecurringInvoice::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('order_number')
                ->label(translate('P.O/S.O Number')),
            ExportColumn::make('client.name')
                ->label(translate('Client')),
            ExportColumn::make('status')
                ->label(translate('Status'))
                ->enum(),
            ExportColumn::make('schedule')
                ->label(translate('Schedule'))
                ->formatStateUsing(function ($state, RecurringInvoice $record) {
                    return $record->getScheduleDescription();
                }),
            ExportColumn::make('timeline')
                ->label(translate('Timeline'))
                ->formatStateUsing(function ($state, RecurringInvoice $record) {
                    return $record->getTimelineDescription();
                }),
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
            ExportColumn::make('payment_terms')
                ->label(translate('Payment terms'))
                ->enum(),
            ExportColumn::make('start_date')
                ->label(translate('Start date'))
                ->date(),
            ExportColumn::make('end_date')
                ->label(translate('End date'))
                ->date(),
            ExportColumn::make('next_date')
                ->label(translate('Next date'))
                ->date(),
            ExportColumn::make('last_date')
                ->label(translate('Last date'))
                ->date(),
            ExportColumn::make('approved_at')
                ->label(translate('Approved at'))
                ->dateTime(),
            ExportColumn::make('ended_at')
                ->label(translate('Ended at'))
                ->dateTime(),
            ExportColumn::make('occurrences_count')
                ->label(translate('Occurrences count')),
            ExportColumn::make('max_occurrences')
                ->label(translate('Max occurrences')),
            ExportColumn::make('send_time')
                ->label(translate('Send time'))
                ->formatStateUsing(function (?Carbon $state) {
                    return $state?->format('H:i');
                }),
            ExportColumn::make('frequency')
                ->label(translate('Frequency'))
                ->enabledByDefault(false)
                ->enum(),
            ExportColumn::make('interval_type')
                ->label(translate('Interval type'))
                ->enabledByDefault(false)
                ->enum(),
            ExportColumn::make('interval_value')
                ->label(translate('Interval value'))
                ->enabledByDefault(false),
            ExportColumn::make('month')
                ->label(translate('Month'))
                ->enabledByDefault(false)
                ->enum(),
            ExportColumn::make('day_of_month')
                ->label(translate('Day of month'))
                ->enabledByDefault(false)
                ->enum(),
            ExportColumn::make('day_of_week')
                ->label(translate('Day of week'))
                ->enabledByDefault(false)
                ->enum(),
            ExportColumn::make('end_type')
                ->label(translate('End type'))
                ->enabledByDefault(false)
                ->enum(),
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
        $body = translate('Your recurring invoice export has completed and :count :rows exported.', [
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
