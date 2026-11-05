<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    public function handle(Request $request)
    {
        if (!auth()->check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ]);
        }

        $id_user = auth()->user()->id_user;

        if ($request->isMethod('get')) {
            $unreadCount = Notifikasi::where('id_user', $id_user)
                ->where('is_read', 0)
                ->count();

            $listNotif = Notifikasi::where('id_user', $id_user)
                ->orderBy('tgl_dibuat', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($n) {
                    $link = $n->link;
                    // Normalize legacy links if any
                    if (preg_match('/forum_post_detail\.php\?id=(\d+)/', (string) $link, $m)) {
                        $link = route('forum.show', ['id' => $m[1]]);
                    } elseif (preg_match('/forum_detail\.php\?id=(\d+)/', (string) $link, $m)) {
                        $link = route('diskusi.show', ['id' => $m[1]]);
                    }
                    return [
                        'id_notifikasi' => $n->id_notifikasi,
                        'id_user' => $n->id_user,
                        'judul' => $n->judul,
                        'pesan' => $n->pesan,
                        'link' => $link,
                        'is_read' => $n->is_read ? 1 : 0,
                        'tgl_dibuat' => $n->tgl_dibuat ? $n->tgl_dibuat->format('Y-m-d H:i:s') : '',
                    ];
                });

            return response()->json([
                'status' => 'success',
                'unread_count' => $unreadCount,
                'data' => $listNotif,
            ]);
        }

        if ($request->isMethod('post') && $request->has('action')) {
            $action = $request->input('action');
            $id_notif = $request->input('id_notifikasi', 'all');

            if ($action === 'mark_read') {
                if ($id_notif === 'all') {
                    Notifikasi::where('id_user', $id_user)->update(['is_read' => 1]);
                } else {
                    Notifikasi::where('id_notifikasi', $id_notif)
                        ->where('id_user', $id_user)
                        ->update(['is_read' => 1]);
                }
                return response()->json(['status' => 'success']);
            } elseif ($action === 'delete') {
                if ($id_notif === 'all') {
                    Notifikasi::where('id_user', $id_user)->delete();
                } else {
                    Notifikasi::where('id_notifikasi', $id_notif)
                        ->where('id_user', $id_user)
                        ->delete();
                }
                return response()->json(['status' => 'success']);
            }
        }

        return response()->json(['status' => 'error']);
    }
}

