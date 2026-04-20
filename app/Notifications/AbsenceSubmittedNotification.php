<?php

namespace App\Notifications;

use App\Models\Absence;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AbsenceSubmittedNotification extends Notification
{
    use Queueable;

    protected $absence;
    protected $student;
    protected $submissionMethod;

    public function __construct(Absence $absence, User $student, string $submissionMethod = 'qr')
    {
        $this->absence = $absence;
        $this->student = $student;
        $this->submissionMethod = $submissionMethod;
    }

    public function via($notifiable)
    {
        return ['database']; // Store in database for in-app display
    }

    public function toDatabase($notifiable)
    {
        $methodLabel = $this->submissionMethod === 'qr' ? 'QR Code' : 'Selfie';
        
        return [
            'title' => 'New Absence Submitted',
            'message' => "{$this->student->name} has marked absence on {$this->absence->absence_date->format('M d, Y H:i')} via {$methodLabel}",
            'student_name' => $this->student->name,
            'student_id' => $this->student->id,
            'absence_id' => $this->absence->id,
            'submission_method' => $this->submissionMethod,
            'submission_date' => $this->absence->absence_date->format('M d, Y H:i'),
            'location' => $this->absence->location_name ?? 'Not provided',
            'ip_address' => $this->absence->ip_address ?? 'Not provided',
        ];
    }

    public function toMail($notifiable)
    {
        $methodLabel = $this->submissionMethod === 'qr' ? 'QR Code' : 'Selfie';
        $location = $this->absence->location_name ?? 'Not provided';
        $ip = $this->absence->ip_address ?? 'Not provided';
        
        return (new MailMessage)
            ->subject("New Absence Submission: {$this->student->name}")
            ->greeting("Hello {$notifiable->name},")
            ->line("{$this->student->name} has submitted an absence record via {$methodLabel}.")
            ->line("**Student Name:** {$this->student->name}")
            ->line("**Date & Time:** {$this->absence->absence_date->format('M d, Y H:i')}")
            ->line("**Method:** {$methodLabel}")
            ->line("**Location:** {$location}")
            ->line("**IP Address:** {$ip}")
            ->action('View Record', route('absence.all'))
            ->line('Thank you for using the attendance system!');
    }
}
