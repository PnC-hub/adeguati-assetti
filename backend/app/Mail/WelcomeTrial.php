<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeTrial extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $trialDays;

    public function __construct($user, $trialDays = 14)
    {
        $this->user = $user;
        $this->trialDays = $trialDays;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Benvenuto in Adeguati Assetti Impresa - Il tuo trial di '. $this->trialDays .' giorni inizia oggi'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome-trial',
            with: [
                'userName' => $this->user->nome ?? 'Utente',
                'trialDays' => $this->trialDays,
                'loginUrl' => 'https://adeguatiassettiimpresa.it/login',
                'dashboardUrl' => 'https://adeguatiassettiimpresa.it/dashboard',
            ],
        );
    }
}
