<?php

namespace App\Notifications\Users;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UsersExportCompleted extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @param bool $success Whether the export was successful
     * @param string|null $downloadUrl Temporary download URL
     * @param string|null $filename Export filename
     * @param string|null $errorMessage Error message if failed
     */
    public function __construct(
        public bool $success,
        public ?string $downloadUrl = null,
        public ?string $filename = null,
        public ?string $errorMessage = null
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // Only send database notifications if mail is not configured
        $channels = ['database'];

        // Only add mail if it's properly configured
        if (config('mail.mailers.smtp.host')) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('Exportación de usuarios completada');

        if ($this->success) {
            $message->line('La exportación de usuarios ha finalizado correctamente.')
                ->line("Archivo: {$this->filename}")
                ->line('El enlace de descarga es válido por 1 hora.')
                ->action('Descargar archivo', $this->downloadUrl);
        } else {
            $message->error()
                ->line('La exportación de usuarios ha fallado.')
                ->line($this->errorMessage ?? 'Por favor contacta al administrador del sistema.');
        }

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'users_export',
            'success' => $this->success,
            'download_url' => $this->downloadUrl,
            'filename' => $this->filename,
            'error_message' => $this->errorMessage,
            'message' => $this->success
                ? "Exportación completada: {$this->filename}"
                : 'La exportación ha fallado',
            'expires_at' => $this->success ? now()->addHour()->toIso8601String() : null,
        ];
    }
}
