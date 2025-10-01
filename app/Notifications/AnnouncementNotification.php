<?php

namespace App\Notifications;

use App\Models\Announcement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AnnouncementNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $announcement;

    /**
     * Create a new notification instance.
     */
    public function __construct(Announcement $announcement)
    {
        $this->announcement = $announcement;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        // Get user preferences
        $preferences = $notifiable->preferences;
        
        if (!$preferences || !$preferences->announcement_notifications) {
            return ['database'];
        }
        
        // Check if user is in quiet hours
        if ($preferences->isInQuietHours()) {
            return ['database'];
        }
        
        return $preferences->getEnabledChannels();
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $type = ucfirst($this->announcement->type);
        $url = route('communication.view-announcement', $this->announcement->id);
        
        return (new MailMessage)
            ->subject("[{$type}] {$this->announcement->title}")
            ->greeting("Hello {$notifiable->name}!")
            ->line("A new announcement has been posted.")
            ->line("Title: {$this->announcement->title}")
            ->line("Type: {$type}")
            ->line(substr($this->announcement->content, 0, 200) . '...')
            ->action('View Announcement', $url)
            ->line('Thank you for staying informed!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable)
    {
        return [
            'announcement_id' => $this->announcement->id,
            'title' => $this->announcement->title,
            'type' => $this->announcement->type,
            'target_audience' => $this->announcement->target_audience,
            'preview' => substr($this->announcement->content, 0, 100) . '...',
            'created_by' => $this->announcement->creator->name,
            'created_at' => $this->announcement->created_at->toISOString()
        ];
    }

    /**
     * Get the SMS representation of the notification.
     */
    public function toSms($notifiable)
    {
        $type = ucfirst($this->announcement->type);
        return "[{$type}] {$this->announcement->title} - Check the app for details.";
    }

    /**
     * Get the push notification representation.
     */
    public function toPush($notifiable)
    {
        $typeEmoji = [
            'info' => 'ℹ️',
            'warning' => '⚠️',
            'alert' => '🚨',
            'success' => '✅'
        ];
        
        $emoji = $typeEmoji[$this->announcement->type] ?? '📢';
        
        return [
            'title' => "{$emoji} {$this->announcement->title}",
            'body' => substr($this->announcement->content, 0, 150) . '...',
            'badge' => 1,
            'sound' => $this->announcement->type === 'alert' ? 'alert.wav' : 'default',
            'data' => [
                'announcement_id' => $this->announcement->id,
                'type' => 'announcement'
            ]
        ];
    }
}