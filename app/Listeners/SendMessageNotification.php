<?php

namespace App\Listeners;

use App\Events\MessageSent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class SendMessageNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(MessageSent $event): void
    {
        // Buat notifikasi di database untuk user target
        DB::table('notifikasi')->insert([
            'user_id' => $event->targetUser->id,
            'judul' => 'Pesan Baru',
            'pesan' => 'Pesan baru dari ' . $event->pesan->pengirim->nama,
            'tipe' => 'chat_message',
            'ikon' => '💬',
            'warna' => 'indigo',
            'url' => route('chat.with', $event->pesan->pengirim_id),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}