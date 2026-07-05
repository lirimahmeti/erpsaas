<?php

use App\Http\Controllers\DocumentPrintController;
use App\Http\Middleware\AllowSameOriginFrame;
use App\Models\Setting\Localization;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

$landingRoute = function (?string $locale = null) {
    $availableLocales = collect(File::files(resource_path('data/lang')))
        ->map(fn (SplFileInfo $file): string => pathinfo($file->getFilename(), PATHINFO_FILENAME))
        ->filter()
        ->unique()
        ->values();

    $locale = $locale ?: config('app.locale');

    abort_unless($availableLocales->contains($locale), 404);

    app()->setLocale($locale);

    $languageNames = Localization::getAllLanguages();

    return view('landing', [
        'currentLocale' => $locale,
        'languages' => $availableLocales
            ->mapWithKeys(fn (string $availableLocale): array => [
                $availableLocale => $languageNames[$availableLocale] ?? strtoupper($availableLocale),
            ])
            ->sort(),
        'loginUrl' => filament()->getLoginUrl(),
        'registrationUrl' => filament()->getRegistrationUrl(),
    ]);
};

Route::get('/', $landingRoute);

Route::get('/{locale}', $landingRoute)->whereIn('locale', collect(File::files(resource_path('data/lang')))
    ->map(fn (SplFileInfo $file): string => pathinfo($file->getFilename(), PATHINFO_FILENAME))
    ->all());

Route::middleware(['auth'])->group(function () {
    Route::get('documents/{documentType}/{id}/print', [DocumentPrintController::class, 'show'])
        ->middleware(AllowSameOriginFrame::class)
        ->name('documents.print');
});
