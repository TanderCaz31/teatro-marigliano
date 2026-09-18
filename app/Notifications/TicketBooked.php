<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketBooked extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Ticket $ticket)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $performance = $this->ticket->performance;

        return (new MailMessage)
            ->subject('Prenotazione confermata: '.$performance->show->title)
            ->greeting("Salve $notifiable->name,")
            ->line("La tua prenotazione per \"{$performance->show->title}\" è confermata.")
            ->line('Data: '.$performance->starts_at->translatedFormat('d F Y, H:i'))
            ->line('Sala: '.$performance->venue->name)
            ->line('Posto: '.$this->ticket->seat_code)
            ->line('Non ci sarà bisogno di portare una conferma cartacea della prenotazione il giorno dell\'esibizione.')
            ->action('I miei biglietti', route('tickets.index'))
            ->salutation('Grazie per la prenotazione, Teatro Marigliano');
    }
}
