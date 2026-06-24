<?php

namespace App\Mail;

use App\Models\Kalibrasi;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class PengingatKalibrasi extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Collection $alatJatuhTempo,    // kalibrasi dalam 30 hari
        public readonly Collection $alatTerlambat,     // sudah melewati jadwal
        public readonly string     $recipientName,
    ) {}

    public function envelope(): Envelope
    {
        $totalAlert = $this->alatJatuhTempo->count() + $this->alatTerlambat->count();

        return new Envelope(
            subject: "⚠️ [{$totalAlert} Alat] Pengingat Jadwal Kalibrasi — " . now()->format('F Y'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.pengingat-kalibrasi',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
