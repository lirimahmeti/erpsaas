<?php

namespace App\Filament\Exports\Accounting;

use App\Models\Accounting\Transaction;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class TransactionExporter extends Exporter
{
    protected static ?string $model = Transaction::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('posted_at')
                ->label(translate('Posted at'))
                ->date(),
            ExportColumn::make('description')
                ->label(translate('Description')),
            ExportColumn::make('amount')
                ->label(translate('Amount'))
                ->money(),
            ExportColumn::make('account.name')
                ->label(translate('Category')),
            ExportColumn::make('bankAccount.account.name')
                ->label(translate('Account')),
            ExportColumn::make('type')
                ->label(translate('Type'))
                ->enum(),
            ExportColumn::make('payeeable.name')
                ->label(translate('Payee')),
            ExportColumn::make('payment_method')
                ->label(translate('Payment method'))
                ->enum(),
            ExportColumn::make('notes')
                ->label(translate('Notes'))
                ->enabledByDefault(false),
            ExportColumn::make('transactionable_type')
                ->label(translate('Source type'))
                ->formatStateUsing(static function ($state) {
                    return class_basename($state);
                })
                ->enabledByDefault(false),
            ExportColumn::make('payeeable_type')
                ->label(translate('Payee type'))
                ->formatStateUsing(static function ($state) {
                    return class_basename($state);
                })
                ->enabledByDefault(false),
            ExportColumn::make('is_payment')
                ->label(translate('Payment'))
                ->enabledByDefault(false),
            ExportColumn::make('reviewed')
                ->label(translate('Reviewed'))
                ->enabledByDefault(false),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = translate('Your transaction export has completed and :count :rows exported.', [
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
