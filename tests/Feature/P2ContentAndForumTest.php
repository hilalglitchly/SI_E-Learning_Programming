<?php

namespace Tests\Feature;

use App\Models\BalasanDiskusi;
use App\Models\Diskusi;
use App\Models\ForumJawaban;
use App\Models\ForumPost;
use App\Models\ForumSimpan;
use App\Models\Kelas;
use App\Models\Kuis;
use App\Models\Materi;
use App\Models\Murid;
use App\Models\Pengajar;
use App\Models\PesertaKelas;
use App\Models\SoalKuis;
use App\Models\Tugas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class P2ContentAndForumTest extends TestCase
{
    use RefreshDatabase;

    public function test_pengajar_can_edit_and_delete_materi_tugas_kuis_soal_and_other_pengajar_is_blocked(): void
    {
        $u1 = User::create(['username' => 'dsn_p2a', 'password' => Hash::make('StrongPass123!'), 'role' => 'Pengajar']);
        $u2 = User::create(['username' => 'dsn_p2b', 'password' => Hash::make('StrongPass123!'), 'role' => 'Pengajar']);
        Pengajar::create(['nidn' => '4001', 'nama_pengajar' => 'Dosen 1', 'email' => 'p2a@test.local', 'id_user' => $u1->id_user]);
        Pengajar::create(['nidn' => '4002', 'nama_pengajar' => 'Dosen 2', 'email' => 'p2b@test.local', 'id_user' => $u2->id_user]);

        $kelas = Kelas::create(['nama_kelas' => 'Web Dev', 'deskripsi' => 'Desc', 'nidn' => '4001']);
        $materi = Materi::create(['id_kelas' => $kelas->id_kelas, 'judul_materi' => 'M1', 'deskripsi' => '<p>Awal</p>', 'file_materi' => '']);
        $tugas = Tugas::create(['id_kelas' => $kelas->id_kelas, 'judul_tugas' => 'T1', 'deskripsi' => '<p>Tugas</p>', 'deadline' => now()->addDays(3)]);
        $kuis = Kuis::create(['id_kelas' => $kelas->id_kelas, 'judul_kuis' => 'K1', 'deskripsi' => 'Desc', 'durasi_menit' => 20]);
        $soal = SoalKuis::create([
            'id_kuis' => $kuis->id_kuis, 'pertanyaan' => 'Q1?', 'opsi_a' => 'A', 'opsi_b' => 'B', 'opsi_c' => 'C', 'opsi_d' => 'D', 'jawaban_benar' => 'A',
        ]);

        // Another pengajar cannot edit or delete
        $this->actingAs($u2)->post(route('kelas.materi.update', [$kelas->id_kelas, $materi->id_materi]), ['judul_materi' => 'Hack'])->assertStatus(403);
        $this->actingAs($u2)->delete(route('kelas.materi.delete', [$kelas->id_kelas, $materi->id_materi]))->assertStatus(403);
        $this->actingAs($u2)->post(route('kuis.soal.update', [$kuis->id_kuis, $soal->id_soal]), [
            'pertanyaan' => 'H', 'opsi_a' => '1', 'opsi_b' => '2', 'opsi_c' => '3', 'opsi_d' => '4', 'jawaban_benar' => 'B',
        ])->assertStatus(403);

        // Owning pengajar can edit & delete
        $this->actingAs($u1)->post(route('kelas.materi.update', [$kelas->id_kelas, $materi->id_materi]), [
            'judul_materi' => 'M1 Edited',
            'deskripsi' => '<p>Baru</p><script>alert(1)</script>',
        ])->assertRedirect(route('kelas.show', ['id' => $kelas->id_kelas]));
        $this->assertSame('M1 Edited', $materi->fresh()->judul_materi);
        $this->assertStringNotContainsString('<script>', $materi->fresh()->deskripsi);

        $this->actingAs($u1)->post(route('kelas.tugas.update', [$kelas->id_kelas, $tugas->id_tugas]), [
            'judul_tugas' => 'T1 Edited',
            'deskripsi' => '<p>Baru</p>',
            'deadline_tugas' => now()->addDays(5)->format('Y-m-d\TH:i'),
        ])->assertRedirect(route('kelas.show', ['id' => $kelas->id_kelas]));
        $this->assertSame('T1 Edited', $tugas->fresh()->judul_tugas);

        $this->actingAs($u1)->post(route('kelas.kuis.update', [$kelas->id_kelas, $kuis->id_kuis]), [
            'judul_kuis' => 'K1 Edited',
            'deskripsi_kuis' => 'Updated',
            'durasi_menit' => 45,
        ])->assertRedirect(route('kelas.show', ['id' => $kelas->id_kelas]));
        $this->assertSame(45, (int) $kuis->fresh()->durasi_menit);

        $this->actingAs($u1)->post(route('kuis.soal.update', [$kuis->id_kuis, $soal->id_soal]), [
            'pertanyaan' => 'Q1 Edited?', 'opsi_a' => '1', 'opsi_b' => '2', 'opsi_c' => '3', 'opsi_d' => '4', 'jawaban_benar' => 'C',
        ])->assertRedirect(route('kuis.manage', ['id_kuis' => $kuis->id_kuis]));
        $this->assertSame('C', $soal->fresh()->jawaban_benar);

        $this->actingAs($u1)->delete(route('kelas.materi.delete', [$kelas->id_kelas, $materi->id_materi]))->assertRedirect();
        $this->actingAs($u1)->delete(route('kelas.tugas.delete', [$kelas->id_kelas, $tugas->id_tugas]))->assertRedirect();
        $this->actingAs($u1)->delete(route('kelas.kuis.delete', [$kelas->id_kelas, $kuis->id_kuis]))->assertRedirect();

        $this->assertDatabaseCount('tb_materi', 0);
        $this->assertDatabaseCount('tb_tugas', 0);
        $this->assertDatabaseCount('tb_kuis', 0);
    }

    public function test_diskusi_and_forum_edit_delete_saved_filter_and_no_n_plus_one(): void
    {
        $admin = User::create(['username' => 'adm_p2', 'password' => Hash::make('StrongPass123!'), 'role' => 'Admin']);
        $u1 = User::create(['username' => 'mrd_p2a', 'password' => Hash::make('StrongPass123!'), 'role' => 'Murid']);
        $u2 = User::create(['username' => 'mrd_p2b', 'password' => Hash::make('StrongPass123!'), 'role' => 'Murid']);
        Murid::create(['nim' => '5001', 'nama_murid' => 'M1', 'email' => 'm5001@test.local', 'id_user' => $u1->id_user]);
        Murid::create(['nim' => '5002', 'nama_murid' => 'M2', 'email' => 'm5002@test.local', 'id_user' => $u2->id_user]);

        $kelas = Kelas::create(['nama_kelas' => 'PHP', 'deskripsi' => 'Desc']);
        PesertaKelas::create(['id_kelas' => $kelas->id_kelas, 'nim' => '5001']);
        PesertaKelas::create(['id_kelas' => $kelas->id_kelas, 'nim' => '5002']);

        $disk = Diskusi::create(['id_kelas' => $kelas->id_kelas, 'id_user' => $u1->id_user, 'judul_diskusi' => 'Topik 1', 'isi_diskusi' => '<p>Isi</p>']);
        $balas = BalasanDiskusi::create(['id_diskusi' => $disk->id_diskusi, 'id_user' => $u2->id_user, 'isi_balasan' => '<p>Balas</p>']);

        // u2 cannot edit u1's discussion; u1 can edit own discussion; u2 can edit/delete own reply
        $this->actingAs($u2)->post(route('diskusi.update', $disk->id_diskusi), ['judul_diskusi' => 'Hack', 'isi_diskusi' => 'x'])->assertStatus(403);
        $this->actingAs($u1)->post(route('diskusi.update', $disk->id_diskusi), ['judul_diskusi' => 'Topik 1 Updated', 'isi_diskusi' => '<p>Isi baru</p>'])->assertRedirect();
        $this->assertSame('Topik 1 Updated', $disk->fresh()->judul_diskusi);

        $this->actingAs($u2)->post(route('diskusi.balasan.update', [$disk->id_diskusi, $balas->id_balasan]), ['isi_balasan' => '<p>Balas baru</p>'])->assertRedirect();
        $this->assertStringContainsString('Balas baru', $balas->fresh()->isi_balasan);
        $this->actingAs($u2)->delete(route('diskusi.balasan.delete', [$disk->id_diskusi, $balas->id_balasan]))->assertRedirect();
        $this->assertDatabaseCount('tb_balasan_diskusi', 0);

        // Forum: create 10 posts, bookmark 1, test ?sort=saved and query count (no N+1)
        $posts = [];
        for ($i = 1; $i <= 10; $i++) {
            $posts[] = ForumPost::create([
                'id_user' => $u1->id_user,
                'judul' => "Postingan Forum {$i}",
                'konten' => "<p>Konten {$i}</p>",
                'tipe' => 'pertanyaan',
                'status' => 'open',
            ]);
        }
        ForumSimpan::create(['id_user' => $u2->id_user, 'id_post' => $posts[0]->id_post]);

        DB::flushQueryLog();
        DB::enableQueryLog();
        $this->actingAs($u2)->get(route('forum.index'))->assertStatus(200);
        $queryCount = count(DB::getQueryLog());
        DB::disableQueryLog();
        $this->assertLessThan(15, $queryCount, "Forum index executed {$queryCount} queries (expected batched queries without N+1)");

        $this->actingAs($u2)->get(route('forum.index', ['sort' => 'saved']))
            ->assertStatus(200)
            ->assertSee('Postingan Forum 1')
            ->assertDontSee('Postingan Forum 2');

        // Forum edit & admin moderation delete (and status reset when solution answer deleted)
        $targetPost = $posts[0];
        $ans = ForumJawaban::create(['id_post' => $targetPost->id_post, 'id_user' => $u2->id_user, 'konten' => '<p>Solusi</p>', 'is_solusi' => true]);
        $targetPost->update(['status' => 'solved']);

        $this->actingAs($u2)->post(route('forum.update', $targetPost->id_post), ['judul' => 'Hack', 'konten' => 'x'])->assertStatus(403);
        $this->actingAs($u1)->post(route('forum.update', $targetPost->id_post), ['judul' => 'Post 1 Edited', 'tipe' => 'pertanyaan', 'konten' => '<p>Updated</p>'])->assertRedirect();
        $this->assertSame('Post 1 Edited', $targetPost->fresh()->judul);

        $this->actingAs($admin)->delete(route('forum.jawaban.delete', [$targetPost->id_post, $ans->id_jawaban]))->assertRedirect();
        $this->assertSame('open', $targetPost->fresh()->status);

        $this->actingAs($admin)->delete(route('forum.delete', $targetPost->id_post))->assertRedirect(route('forum.index'));
        $this->assertNull(ForumPost::find($targetPost->id_post));
    }
}
