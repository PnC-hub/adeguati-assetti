<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TrialReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $daysLeft;
    public $type;

    /**
     * @param $user
     * @param int $daysLeft
     * @param string $type - 'day3', 'day7', 'day10', 'day13'
     */
    public function __construct($user, int $daysLeft, string $type)
    {
        $this->user = $user;
        $this->daysLeft = $daysLeft;
        $this->type = $type;
    }

    public function envelope(): Envelope
    {
        $subjects = [
            'day3' => 'Hai inserito i tuoi dati economici? Il tuo score ti aspetta',
            'day7' => 'Come le aziende italiane evitano le ispezioni giudiziarie',
            'day10' => 'Il tuo trial scade tra 4 giorni - non perdere le funzionalita Pro',
            'day13' => 'ULTIMO GIORNO: Mantieni le funzionalita Pro',
        ];

        return new Envelope(
            subject: $subjects[$this->type] ?? 'Adeguati Assetti - Promemoria'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.trial-reminder-' . $this->type,
            with: [
                'userName' => $this->user->nome ?? 'Utente',
                'daysLeft' => $this->daysLeft,
                'upgradeUrl' => 'https://adeguatiassettiimpresa.it/dashboard/account?upgrade=1',
            ],
        );
    }
}
