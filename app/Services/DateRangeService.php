<?php

namespace App\Services;

use App\Facades\Accounting;
use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;

class DateRangeService
{
    protected string $fiscalYearStartDate = '';

    protected string $fiscalYearEndDate = '';

    public function __construct()
    {
        $company = auth()->user()->currentCompany;
        $this->fiscalYearStartDate = $company->locale->fiscalYearStartDate();
        $this->fiscalYearEndDate = $company->locale->fiscalYearEndDate();
    }

    public function getDateRangeOptions(): array
    {
        return once(function () {
            return $this->generateDateRangeOptions();
        });
    }

    private function generateDateRangeOptions(): array
    {
        $earliestDate = Carbon::parse(Accounting::getEarliestTransactionDate());
        $currentDate = company_today();
        $currentYear = $currentDate->year;
        $fiscalYearStartCurrent = Carbon::parse($this->fiscalYearStartDate);

        $fiscalYear = translate('Fiscal Year');
        $fiscalQuarter = translate('Fiscal Quarter');
        $calendarYear = translate('Calendar Year');
        $calendarQuarter = translate('Calendar Quarter');
        $month = translate('Month');
        $custom = translate('Custom');

        $options = [
            $fiscalYear => [],
            $fiscalQuarter => [],
            $calendarYear => [],
            $calendarQuarter => [],
            $month => [],
            $custom => [],
        ];

        $period = CarbonPeriod::create($earliestDate, '1 month', $currentDate->endOfMonth());

        foreach ($period as $date) {
            $options[$fiscalYear]['FY-' . $date->year] = $date->year;

            $fiscalYearStart = $fiscalYearStartCurrent->copy()->subYears($currentYear - $date->year);

            for ($i = 0; $i < 4; $i++) {
                $quarterNumber = $i + 1;
                $quarterStart = $fiscalYearStart->copy()->addMonths(($quarterNumber - 1) * 3);
                $quarterEnd = $quarterStart->copy()->addMonths(3)->subDay();

                if ($quarterStart->lessThanOrEqualTo($currentDate) && $quarterEnd->greaterThanOrEqualTo($earliestDate)) {
                    $options[$fiscalQuarter]['FQ-' . $quarterNumber . '-' . $date->year] = 'Q' . $quarterNumber . ' ' . $date->year;
                }
            }

            $options[$calendarYear]['Y-' . $date->year] = $date->year;
            $quarterKey = 'Q-' . $date->quarter . '-' . $date->year;
            $options[$calendarQuarter][$quarterKey] = 'Q' . $date->quarter . ' ' . $date->year;
            $options[$month]['M-' . $date->format('Y-m')] = $date->format('F Y');
            $options[$custom]['Custom'] = translate('Custom');
        }

        $options[$fiscalYear] = array_reverse($options[$fiscalYear], true);
        $options[$fiscalQuarter] = array_reverse($options[$fiscalQuarter], true);
        $options[$calendarYear] = array_reverse($options[$calendarYear], true);
        $options[$calendarQuarter] = array_reverse($options[$calendarQuarter], true);
        $options[$month] = array_reverse($options[$month], true);

        return $options;
    }

    public function getMatchingDateRangeOption(Carbon $startDate, Carbon $endDate): string
    {
        $options = $this->getDateRangeOptions();

        foreach ($options as $type => $ranges) {
            foreach ($ranges as $key => $label) {
                [$expectedStart, $expectedEnd] = $this->getExpectedDateRange($type, $key);

                if ($expectedStart === null || $expectedEnd === null) {
                    continue;
                }

                $expectedEnd = $expectedEnd->isFuture() ? company_today() : $expectedEnd;

                if ($startDate->isSameDay($expectedStart) && $endDate->isSameDay($expectedEnd)) {
                    return $key; // Return the matching range key (e.g., "FY-2024")
                }
            }
        }

        return 'Custom'; // Return the stored custom range key.
    }

    private function getExpectedDateRange(string $type, string $key): array
    {
        $currentYear = company_today()->year;

        switch ($type) {
            case translate('Fiscal Year'):
                $year = (int) substr($key, 3);
                $start = Carbon::parse($this->fiscalYearStartDate)->subYears($currentYear - $year)->startOfDay();
                $end = Carbon::parse($this->fiscalYearEndDate)->subYears($currentYear - $year)->startOfDay();

                break;

            case translate('Fiscal Quarter'):
                [$quarter, $year] = explode('-', substr($key, 3));
                $start = Carbon::parse($this->fiscalYearStartDate)->subYears($currentYear - $year)->addMonths(($quarter - 1) * 3)->startOfDay();
                $end = $start->copy()->addMonths(3)->subDay()->startOfDay();

                break;

            case translate('Calendar Year'):
                $year = (int) substr($key, 2);
                $start = Carbon::createFromDate($year)->startOfYear()->startOfDay();
                $end = Carbon::createFromDate($year)->endOfYear()->startOfDay();

                break;

            case translate('Calendar Quarter'):
                [$quarter, $year] = explode('-', substr($key, 2));
                $month = ($quarter - 1) * 3 + 1;
                $start = Carbon::createFromDate($year, $month, 1)->startOfDay();
                $end = $start->copy()->endOfQuarter()->startOfDay();

                break;

            case translate('Month'):
                $yearMonth = substr($key, 2);
                $start = Carbon::parse($yearMonth)->startOfMonth()->startOfDay();
                $end = Carbon::parse($yearMonth)->endOfMonth()->startOfDay();

                break;

            default:
                return [null, null];
        }

        return [$start, $end];
    }
}
