<?php

namespace App\Notifications;

use App\Models\DailyAgenda;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DailyAgendaSubmittedNotification extends Notification
{
    use Queueable;

    protected DailyAgenda $dailyAgenda;
    protected User $student;

    public function __construct(DailyAgenda $dailyAgenda, User $student)
    {
        $this->dailyAgenda = $dailyAgenda;
        $this->student = $student;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'New Daily Agenda Submitted',
            'message' => "{$this->student->name} submitted a daily agenda for {$this->dailyAgenda->agenda_date?->format('M d, Y')}.",
            'agenda_id' => $this->dailyAgenda->id,
            'date' => $this->dailyAgenda->agenda_date?->format('M d, Y'),
            'agenda_date' => $this->dailyAgenda->agenda_date?->format('M d, Y'),
            'submitted_at' => $this->dailyAgenda->submitted_at?->format('M d, Y H:i') ?? $this->dailyAgenda->created_at?->format('M d, Y H:i'),
            'time_in' => $this->dailyAgenda->time_in,
            'time_out' => $this->dailyAgenda->time_out,
            'student_name' => $this->student->name,
            'student_id' => $this->student->id,
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject("New Daily Agenda Submitted: {$this->student->name}")
            ->greeting("Hello {$notifiable->name},")
            ->line("{$this->student->name} submitted a daily agenda for {$this->dailyAgenda->agenda_date?->format('M d, Y')}.")
            ->action('View Agenda', route('daily-agenda.show', $this->dailyAgenda->id))
            ->line('Thank you for using the system!');
    }
}
