<?php

namespace App\Notifications;

use App\Models\CrisisReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AssignmentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected CrisisReport $report;
    protected string $unitNames;

    /**
     * Create a new notification instance.
     */
    public function __construct(CrisisReport $report, string $unitNames)
    {
        $this->report = $report;
        $this->unitNames = $unitNames;
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
            ->subject("Update Penugasan: Laporan #{$this->report->id}")
            ->line("Laporan krisis Anda telah ditugaskan ke tim unit: {$this->unitNames}.")
            ->line("Detail Laporan: {$this->report->description}")
            ->action('Lihat Progress', $url)
            ->line('Tim akan segera menuju lokasi.');
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
            'title' => 'Tim Ditugaskan',
            'message' => "Unit {$this->unitNames} telah dikerahkan untuk laporan #{$this->report->id}.",
            'url' => route('reports.show', $this->report->id),
            'type' => 'assignment',
        ];
    }
}
