namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewNotification implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $notification;

    // Kullanıcıya özel bildirim
    public $targetUserId;

    public function __construct($notification, $targetUserId = null)
    {
        $this->notification = $notification;
        $this->targetUserId = $targetUserId;
    }

    // Private kanal: sadece hedef kullanıcı dinleyebilir
    public function broadcastOn()
    {
        // Hedef kullanıcı yoksa global kanal
        if ($this->targetUserId) {
            return new PrivateChannel('user.'.$this->targetUserId);
        }
        return new PrivateChannel('notifications');
    }

    public function broadcastAs()
    {
        return 'new.notification';
    }
}
