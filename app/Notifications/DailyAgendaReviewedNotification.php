<?php

namespace App\Notifications;

use App\Models\DailyAgenda;
use App\Models\Role;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DailyAgendaReviewedNotification extends Notification
{
    use Queueable;

    protected DailyAgenda $dailyAgenda;
    protected User $reviewer;
    protected string $reviewType;
    protected string $status;
    protected ?string $notes;

    public function __construct(
        DailyAgenda $dailyAgenda,
        User $reviewer,
        string $reviewType,
        string $status = 'approved',
        ?string $notes = null
    ) {
        $this->dailyAgenda = $dailyAgenda;
        $this->reviewer = $reviewer;
        $this->reviewType = $reviewType;
        $this->status = $status;
        $this->notes = $notes;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $statusLabel = $this->status === 'approved' ? 'approved' : 'rejected';
        $statusLabelId = $this->status === 'approved' ? 'disetujui' : 'ditolak';
        $reviewerRole = Role::displayName((string) ($this->reviewer->role?->name ?? 'N/A'));

        return [
            'title' => 'Agenda Harian ' . ucfirst($statusLabelId),
            'message' => "Agenda harian Anda untuk {$this->dailyAgenda->agenda_date?->format('d/m/Y')} {$statusLabelId} oleh {$this->reviewer->name} ({$reviewerRole}).",
            'agenda_id' => $this->dailyAgenda->id,
            'date' => $this->dailyAgenda->agenda_date?->format('d/m/Y'),
            'agenda_date' => $this->dailyAgenda->agenda_date?->format('d/m/Y'),
            'review_type' => $this->reviewType,
            'status' => $this->status,
            'reviewer_name' => $this->reviewer->name,
            'reviewer_role' => $reviewerRole,
            'reviewed_at' => now()->format('d/m/Y H:i'),
            'notes' => $this->notes,
        ];
    }

    public function toMail($notifiable)
    {
        $statusLabel = $this->status === 'approved' ? 'Disetujui' : 'Ditolak';

        return (new MailMessage)
            ->subject("Agenda Harian Anda {$statusLabel}")
            ->greeting("Halo {$notifiable->name},")
            ->line("Agenda harian Anda untuk {$this->dailyAgenda->agenda_date?->format('d/m/Y')} telah {$statusLabel} oleh {$this->reviewer->name}.")
            ->line("Jenis review: {$this->reviewType}")
            ->when($this->notes, function ($message) {
                return $message->line("Catatan: {$this->notes}");
            })
            ->action('Lihat Agenda', route('daily-agenda.show', $this->dailyAgenda->id))
            ->line('Terima kasih telah menggunakan sistem.');
    }
}
