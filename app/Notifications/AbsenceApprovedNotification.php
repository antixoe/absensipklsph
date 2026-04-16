<?php

namespace App\Notifications;

use App\Models\Absence;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AbsenceApprovedNotification extends Notification
{
    use Queueable;

    protected $absence;
    protected $action; // 'approved' or 'rejected'
    protected $adminNotes;

    public function __construct(Absence $absence, string $action, $adminNotes = null)
    {
        $this->absence = $absence;
        $this->action = $action;
        $this->adminNotes = $adminNotes;
    }

    public function via($notifiable)
    {
        return ['database']; // Store in database for in-app display
    }

    public function toDatabase($notifiable)
    {
        $statusLabel = $this->action === 'approved' ? 'Approved' : 'Rejected';
        $statusEmoji = $this->action === 'approved' ? '✅' : '❌';

        return [
            'title' => "{$statusEmoji} Absence {$statusLabel}",
            'message' => "Your absence on {$this->absence->absence_date->format('M d, Y H:i')} has been {$this->action}.",
            'admin_notes' => $this->adminNotes,
            'absence_id' => $this->absence->id,
            'action' => $this->action,
            'date' => $this->absence->absence_date->format('M d, Y H:i'),
        ];
    }

    public function toMail($notifiable)
    {
        $statusLabel = $this->action === 'approved' ? 'Approved' : 'Rejected';
        $statusColor = $this->action === 'approved' ? '#10b981' : '#ef4444';

        return (new MailMessage)
            ->subject("Your Absence Has Been {$statusLabel}")
            ->greeting("Hello {$notifiable->name},")
            ->line("Your absence on **{$this->absence->absence_date->format('M d, Y H:i')}** has been **{$statusLabel}**.")
            ->when($this->adminNotes, function ($message) {
                return $message->line("**Admin Notes:** {$this->adminNotes}");
            })
            ->action('View Details', route('dashboard'))
            ->line('Thank you for using our system!');
    }
}
