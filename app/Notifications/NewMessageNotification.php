<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $message;

    /**
     * Create a new notification instance.
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        // Get user preferences
        $preferences = $notifiable->preferences;
        
        if (!$preferences || !$preferences->message_notifications) {
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
        $priority = ucfirst($this->message->priority);
        $url = route('communication.view-message', $this->message->id);
        
        return (new MailMessage)
            ->subject("[{$priority}] New Message: {$this->message->subject}")
            ->greeting("Hello {$notifiable->name}!")
            ->line("You have received a new message from {$this->message->sender->name}.")
            ->line("Subject: {$this->message->subject}")
            ->line("Priority: {$priority}")
            ->action('View Message', $url)
            ->line('Thank you for using our communication system!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable)
    {
        return [
            'message_id' => $this->message->id,
            'sender_id' => $this->message->sender_id,
            'sender_name' => $this->message->sender->name,
            'subject' => $this->message->subject,
            'priority' => $this->message->priority,
            'type' => 'message',
            'preview' => substr($this->message->body, 0, 100) . '...',
            'created_at' => $this->message->created_at->toISOString()
        ];
    }

    /**
     * Get the SMS representation of the notification.
     */
    public function toSms($notifiable)
    {
        $priority = ucfirst($this->message->priority);
        return "New {$priority} message from {$this->message->sender->name}: {$this->message->subject}";
    }

    /**
     * Get the push notification representation.
     */
    public function toPush($notifiable)
    {
        return [
            'title' => "New Message from {$this->message->sender->name}",
            'body' => $this->message->subject,
            'badge' => 1,
            'sound' => $this->message->priority === 'urgent' ? 'urgent.wav' : 'default',
            'data' => [
                'message_id' => $this->message->id,
                'type' => 'message'
            ]
        ];
    }
}