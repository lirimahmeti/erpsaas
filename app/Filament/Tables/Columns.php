<?php

namespace App\Filament\Tables;

use Filament\Tables\Columns\TextColumn;

class Columns
{
    public static function id(): TextColumn
    {
        return TextColumn::make('id')
            ->label(translate('ID'))
            ->sortable()
            ->toggleable(isToggledHiddenByDefault: true)
            ->searchable();
    }
}
