<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewUserCreated extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public string $temporaryPassword
    ) {}

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
        $loginUrl = route('login');

        return (new MailMessage)
            ->subject('Bienvenido a '.config('app.name'))
            ->greeting('¡Hola '.$notifiable->name.'!')
            ->line('Tu cuenta ha sido creada exitosamente.')
            ->line('**Contraseña temporal:** `'.$this->temporaryPassword.'`')
            ->line('Por razones de seguridad, deberás cambiar esta contraseña en tu primer inicio de sesión.')
            ->action('Iniciar Sesión', $loginUrl)
            ->line('Si no solicitaste esta cuenta, por favor ignora este mensaje.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'temporary_password_sent' => true,
        ];
    }
}
