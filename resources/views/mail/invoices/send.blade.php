<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ translate('Invoice #:number', ['number' => $document->number]) }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #f3f4f6;
            color: #111827;
            font-family: Arial, Helvetica, sans-serif;
            line-height: 1.5;
        }

        .wrapper {
            width: 100%;
            padding: 24px 0;
        }

        .card {
            width: 100%;
            max-width: 640px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
        }

        .header {
            padding: 24px;
            background: #111827;
            color: #ffffff;
        }

        .content {
            padding: 24px;
        }

        .details {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .line-items {
            width: 100%;
            border-collapse: collapse;
            margin: 24px 0;
        }

        .line-items th {
            padding: 10px 8px;
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
            color: #4b5563;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .line-items td {
            padding: 12px 8px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: top;
        }

        .details td {
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .label {
            color: #6b7280;
            font-size: 13px;
        }

        .value {
            text-align: right;
            font-weight: 700;
        }

        .footer {
            padding: 0 24px 24px;
            color: #6b7280;
            font-size: 13px;
        }

        .summary {
            width: 100%;
            max-width: 320px;
            margin-left: auto;
            border-collapse: collapse;
        }

        .summary td {
            padding: 7px 0;
        }

        .summary .total td {
            border-top: 2px solid #111827;
            padding-top: 10px;
            font-weight: 700;
        }

        @media only screen and (max-width: 640px) {
            .wrapper {
                padding: 0;
            }

            .card {
                border-radius: 0;
            }

            .header,
            .content,
            .footer {
                padding: 20px;
            }

            .details td {
                display: block;
                width: 100%;
                text-align: left;
                border-bottom: 0;
                padding: 4px 0;
            }

            .details tr {
                display: block;
                padding: 10px 0;
                border-bottom: 1px solid #e5e7eb;
            }

            .line-items thead {
                display: none;
            }

            .line-items tr {
                display: block;
                padding: 12px 0;
                border-bottom: 1px solid #e5e7eb;
            }

            .line-items td {
                display: block;
                width: 100%;
                border: 0;
                padding: 4px 0;
            }

            .summary {
                max-width: none;
            }
        }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="card">
        <div class="header">
            <h1 style="margin: 0; font-size: 24px;">{{ translate('Invoice #:number', ['number' => $document->number]) }}</h1>
            <p style="margin: 8px 0 0;">{{ translate('From :company', ['company' => $document->company->name]) }}</p>
        </div>

        <div class="content">
            <p style="margin-top: 0;">
                {{ translate('Hello :client,', ['client' => $document->client?->name ?? translate('there')]) }}
            </p>

            <p>
                {{ translate('Please find your invoice details below.') }}
            </p>

            <table class="details" role="presentation">
                <tr>
                    <td class="label">{{ $document->label->number }}</td>
                    <td class="value">{{ $document->number }}</td>
                </tr>
                <tr>
                    <td class="label">{{ $document->label->date }}</td>
                    <td class="value">{{ $document->date }}</td>
                </tr>
                <tr>
                    <td class="label">{{ $document->label->dueDate }}</td>
                    <td class="value">{{ $document->dueDate }}</td>
                </tr>
                <tr>
                    <td class="label">{{ translate('Total') }}</td>
                    <td class="value">{{ $document->total }}</td>
                </tr>
                @if ($document->amountDue)
                    <tr>
                        <td class="label">{{ $document->label->amountDue }} ({{ $document->currencyCode }})</td>
                        <td class="value">{{ $document->amountDue }}</td>
                    </tr>
                @endif
            </table>

            <table class="line-items" role="presentation">
                <thead>
                    <tr>
                        <th align="left">{{ $document->columnLabel->items }}</th>
                        <th align="center">{{ $document->columnLabel->units }}</th>
                        <th align="right">{{ $document->columnLabel->price }}</th>
                        <th align="right">{{ $document->columnLabel->amount }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($document->lineItems as $item)
                        <tr>
                            <td>
                                <strong>{{ $item->name }}</strong>
                                @if ($item->description)
                                    <div style="color: #6b7280; font-size: 13px;">{{ $item->description }}</div>
                                @endif
                            </td>
                            <td align="center">{{ $item->quantity }}</td>
                            <td align="right">{{ $item->unitPrice }}</td>
                            <td align="right"><strong>{{ $item->subtotal }}</strong></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <table class="summary" role="presentation">
                @if ($document->subtotal)
                    <tr>
                        <td>{{ translate('Subtotal') }}</td>
                        <td align="right">{{ $document->subtotal }}</td>
                    </tr>
                @endif
                @if ($document->discount)
                    <tr>
                        <td>{{ translate('Discount') }}</td>
                        <td align="right">({{ $document->discount }})</td>
                    </tr>
                @endif
                @if ($document->tax)
                    <tr>
                        <td>{{ translate('Tax') }}</td>
                        <td align="right">{{ $document->tax }}</td>
                    </tr>
                @endif
                <tr class="total">
                    <td>{{ translate('Total') }}</td>
                    <td align="right">{{ $document->total }}</td>
                </tr>
                @if ($document->amountDue)
                    <tr class="total">
                        <td>{{ $document->label->amountDue }} ({{ $document->currencyCode }})</td>
                        <td align="right">{{ $document->amountDue }}</td>
                    </tr>
                @endif
            </table>

            @if ($document->terms)
                <div style="margin-top: 24px;">
                    <div class="label" style="font-weight: 700; color: #111827;">{{ translate('Terms & Conditions') }}</div>
                    <p style="margin-bottom: 0;">{{ $document->terms }}</p>
                </div>
            @endif

            <p style="margin-bottom: 0;">
                {{ translate('Thank you,') }}<br>
                {{ $document->company->name }}
            </p>
        </div>

        <div class="footer">
            {{ translate('This email was sent by :company.', ['company' => $document->company->name]) }}
        </div>
    </div>
</div>
</body>
</html>
