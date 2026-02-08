<?php

namespace App\Notifications;

use App\Models\CrisisReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CrisisHighPriorityNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected CrisisReport $report;

    /**
     * Create a new notification instance.
     */
    public function __construct(CrisisReport $report)
    {
        $this->report = $report;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];
        if (config('mail.enable_notifications', false)) {
            $channels[] = 'mail';
        }
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = route('reports.show', $this->report->id);

        return (new MailMessage)
            ->error()
            ->subject("URGENT: Laporan Krisis Baru High Priority #{$this->report->id}")
            ->line("PERHATIAN: Laporan krisis dengan urgensi TINGGI baru saja masuk.")
            ->line("Deskripsi: {$this->report->description}")
            ->line("Kategori: {$this->report->crisisType->name}")
            ->line("Lokasi: {$this->report->region->name}")
            ->action('Segera Tinjau', $url)
            ->line('Mohon segera lakukan verifikasi dan penanganan!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'report_id' => $this->report->id,
            'title' => 'ALERT: HIGH PRIORITY CRISIS',
            'message' => "Laporan #{$this->report->id} (Urgency: {$this->report->urgencyLevel->name}) membutuhkan respons segera.",
            'url' => route('reports.show', $this->report->id),
            'type' => 'high_priority',
        ];
    }
}
