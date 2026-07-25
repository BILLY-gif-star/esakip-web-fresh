<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Pesan;
use App\Models\Percakapan;
use App\Models\User;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $pesan;
    public $percakapan;
    public $targetUser;

    /**
     * Create a new event instance.
     */
    public function __construct(Pesan $pesan, Percakapan $percakapan, User $targetUser)
    {
        $this->pesan = $pesan;
        $this->percakapan = $percakapan;
        $this->targetUser = $targetUser;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat.' . $this->targetUser->id),
            new PrivateChannel('chat.percakapan.' . $this->percakapan->id),
        ];
    }

    /**
     * Data yang akan di-broadcast
     */
    public function broadcastWith(): array
    {
        return [
            'pesan_id' => $this->pesan->id,
            'isi' => $this->pesan->isi,
            'pengirim_id' => $this->pesan->pengirim_id,
            'pengirim_nama' => $this->pesan->pengirim->nama,
            'pengirim_role' => $this->pesan->pengirim->role,
            'waktu' => $this->pesan->created_at->format('H:i'),
            'percakapan_id' => $this->percakapan->id,
        ];
    }
}