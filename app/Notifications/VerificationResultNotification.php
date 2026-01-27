<?php

namespace App\Notifications;

use App\Models\CrisisReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerificationResultNotification extends Notification implements ShouldQueue
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
        $status = ucfirst($this->report->verification_status);
        $url = route('crisis-reports.show', $this->report);

        return (new MailMessage)
            ->subject("Laporan Krisis #{$this->report->id} Telah {$status}")
            ->line("Laporan Anda mengenai \"{$this->report->description}\" telah {$status}.")
            ->action('Lihat Laporan', $url)
            ->line('Terima kasih atas partisipasi Anda.');
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
            'title' => 'Hasil Verifikasi Laporan',
            'message' => "Laporan #{$this->report->id} telah {$this->report->verification_status}.",
            'url' => route('crisis-reports.show', $this->report),
            'type' => 'verification_result',
        ];
    }
}
