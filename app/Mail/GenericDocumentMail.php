<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GenericDocumentMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $subjectText;
    public string $bodyText;
    public string $filename;
    public string $pdf;

    public function __construct(
        string $subject,
        string $body,
        string $pdf,
        string $filename
    ) {
        $this->subjectText = $subject;
        $this->bodyText = $body;
        $this->pdf = $pdf;
        $this->filename = $filename;
    }

    public function build(): self
    {
        return $this
            ->subject($this->subjectText)
            ->view('emails.generic-document')
            ->attachData(
                $this->pdf,
                $this->filename,
                ['mime' => 'application/pdf']
            )
            ->with([
                'bodyText' => $this->bodyText,
            ]);
    }
}
