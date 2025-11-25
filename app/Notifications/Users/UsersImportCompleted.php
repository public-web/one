<?php

namespace App\Notifications\Users;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UsersImportCompleted extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @param bool $success Whether the import was successful
     * @param int $successCount Number of users imported successfully
     * @param int $errorCount Number of failures
     * @param array $failures Details of failed rows
     */
    public function __construct(
        public bool $success,
        public int $successCount,
        public int $errorCount,
        public array $failures = []
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('Importación de usuarios completada');

        if ($this->success) {
            $message->line("La importación de usuarios ha finalizado correctamente.")
                ->line("Usuarios importados: {$this->successCount}")
                ->line("Errores: {$this->errorCount}");

            if ($this->errorCount > 0) {
                $message->line('Por favor revisa los detalles de los errores en el panel de administración.');
            }
        } else {
            $message->error()
                ->line('La importación de usuarios ha fallado.')
                ->line('Por favor contacta al administrador del sistema.');
        }

        return $message->action('Ver detalles', url('/users'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'users_import',
            'success' => $this->success,
            'success_count' => $this->successCount,
            'error_count' => $this->errorCount,
            'failures' => array_slice($this->failures, 0, 10), // Limit to first 10 failures
            'message' => $this->success
                ? "Importación completada: {$this->successCount} usuarios importados, {$this->errorCount} errores"
                : 'La importación ha fallado',
        ];
    }
}
