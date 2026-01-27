<?php

namespace App\Notifications;

use App\Models\HandlingAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AssignmentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected HandlingAssignment $assignment;

    /**
     * Create a new notification instance.
     */
    public function __construct(HandlingAssignment $assignment)
    {
        $this->assignment = $assignment;
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
        $report = $this->assignment->report;
        $url = route('reports.assignments.index', $report);

        return (new MailMessage)
            ->subject("Penugasan Baru: Laporan #{$report->id}")
            ->line("Anda telah ditugaskan untuk menangani laporan krisis.")
            ->line("Detail Laporan: {$report->description}")
            ->line("Catatan Tugas: {$this->assignment->note}")
            ->action('Lihat Penugasan', $url)
            ->line('Mohon segera tindak lanjuti.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $report = $this->assignment->report;
        
        return [
            'assignment_id' => $this->assignment->id,
            'report_id' => $report->id,
            'title' => 'Penugasan Baru',
            'message' => "Anda ditugaskan menangani laporan #{$report->id}.",
            'url' => route('reports.assignments.index', $report),
            'type' => 'assignment',
        ];
    }
}
