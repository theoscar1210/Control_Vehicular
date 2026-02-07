<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DocumentoVencidoNotification extends Notification
{
    use Queueable;
    protected $alerta;

    /**
     * Crear una nueva instancia de notificación.
     */
    public function __construct($alerta)
    {
        //
        $this->alerta = $alerta;
    }


    /**
     * Obtenga los canales de entrega de la notificación.
     *
     * @return array<int, string>
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Obtener la representación de correo electrónico de la notificación.
     */
    public function toMail($notifiable)
    {

        $msg = (new MailMessage)
            ->subject("Alerta: Documento {$this->alerta->tipo_vencimiento}")
            ->line($this->alerta->mensaje ?? 'Documento con alarma.')
            ->line('Fecha de alerta: ' . ($this->alerta->fecha_alerta ? $this->alerta->fecha_alerta->format('Y-m-d') : '-'))
            ->action('Ver Documentos', url('/consultar-documentos'))
            ->line('Este mensaje fue enviado al equipo de Administración.');




        return $msg;
    }

    /**
     * Obtener la representación de la matriz de la notificación.
     *
     * @return array<string, mixed>
     */
    public function toDatabase($notifiable)
    {
        return [
            'alerta_id' => $this->alerta->id_alerta,
            'mensaje' => $this->alerta->mensaje,
            'tipo_vencimiento' => $this->alerta->tipo_vencimiento,
        ];
    }
}
