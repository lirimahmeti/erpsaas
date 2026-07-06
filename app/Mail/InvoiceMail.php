<?php

namespace App\Mail;

use App\DTO\DocumentDTO;
use App\Models\Accounting\Invoice;
use App\Services\CompanySettingsService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public Invoice $invoice,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: translate('Invoice #:number from :company', [
                'number' => $this->invoice->documentNumber(),
                'company' => $this->invoice->company->name,
            ]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.invoices.send',
            with: [
                'invoice' => $this->invoice,
                'document' => $this->document(),
            ],
        );
    }

    protected function document(): DocumentDTO
    {
        $settings = CompanySettingsService::getSettings($this->invoice->company_id);

        app()->setLocale($settings['default_language']);
        locale_set_default($settings['default_language']);

        return DocumentDTO::fromModel($this->invoice);
    }
}
