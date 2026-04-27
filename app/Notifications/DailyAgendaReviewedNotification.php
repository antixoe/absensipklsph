<?php

namespace App\Notifications;

use App\Models\DailyAgenda;
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

        return [
            'title' => 'Daily Agenda ' . ucfirst($statusLabel),
            'message' => "Your daily agenda for {$this->dailyAgenda->agenda_date?->format('M d, Y')} was {$statusLabel} by {$this->reviewer->name} ({$this->reviewType}).",
            'agenda_id' => $this->dailyAgenda->id,
            'date' => $this->dailyAgenda->agenda_date?->format('M d, Y'),
            'agenda_date' => $this->dailyAgenda->agenda_date?->format('M d, Y'),
            'review_type' => $this->reviewType,
            'status' => $this->status,
            'reviewer_name' => $this->reviewer->name,
            'reviewer_role' => $this->reviewer->role?->name,
            'reviewed_at' => now()->format('M d, Y H:i'),
            'notes' => $this->notes,
        ];
    }

    public function toMail($notifiable)
    {
        $statusLabel = $this->status === 'approved' ? 'Approved' : 'Rejected';

        return (new MailMessage)
            ->subject("Your Daily Agenda Has Been {$statusLabel}")
            ->greeting("Hello {$notifiable->name},")
            ->line("Your daily agenda for {$this->dailyAgenda->agenda_date?->format('M d, Y')} was {$statusLabel} by {$this->reviewer->name}.")
            ->line("Review type: {$this->reviewType}")
            ->when($this->notes, function ($message) {
                return $message->line("Notes: {$this->notes}");
            })
            ->action('View Agenda', route('daily-agenda.show', $this->dailyAgenda->id))
            ->line('Thank you for using the system!');
    }
}
