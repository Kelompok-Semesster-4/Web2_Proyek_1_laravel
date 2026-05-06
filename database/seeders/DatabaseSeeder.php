<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    private array $timeSlots = [
        ['07:30:00', '09:00:00'],
        ['09:15:00', '10:45:00'],
        ['11:00:00', '12:30:00'],
        ['13:00:00', '14:30:00'],
        ['14:45:00', '16:15:00'],
        ['16:30:00', '18:00:00'],
        ['18:30:00', '20:00:00'],
    ];

    private array $kegiatan = [
        'Rapat BEM',
        'Kelas Pengganti',
        'Seminar Prodi',
        'Workshop UI UX',
        'Diskusi Skripsi',
        'Pelatihan Laravel',
        'Kajian Riset',
        'Briefing Panitia',
        'Presentasi Proyek',
        'Mentoring Akademik',
        'Latihan Debat',
        'Klinik Proposal',
        'Rapat Himpunan',
        'Sharing Alumni',
        'Bootcamp Data',
        'Sosialisasi PKM',
        'Kelas Bahasa',
        'Uji Coba Sistem',
    ];

    public function run(): void
    {
        mt_srand(20260506);

        $this->seedMasterData();
        $roomIds = $this->seedGedungLantaiRuangan();
        $adminIds = $this->seedUsers();
        $this->seedPeminjaman($roomIds, $adminIds);
    }

    private function seedMasterData(): void
    {
        DB::table('status_peminjaman')->insert([
            ['id' => 1, 'nama_status' => 'Menunggu'],
            ['id' => 2, 'nama_status' => 'Disetujui'],
            ['id' => 3, 'nama_status' => 'Ditolak'],
            ['id' => 4, 'nama_status' => 'Selesai'],
            ['id' => 5, 'nama_status' => 'Dibatalkan'],
        ]);

        DB::table('fasilitas')->insert([
            ['id' => 1, 'nama_fasilitas' => 'Proyektor'],
            ['id' => 2, 'nama_fasilitas' => 'AC'],
            ['id' => 3, 'nama_fasilitas' => 'WiFi'],
            ['id' => 4, 'nama_fasilitas' => 'Sound System'],
            ['id' => 5, 'nama_fasilitas' => 'Papan Tulis'],
            ['id' => 6, 'nama_fasilitas' => 'Mikrofon'],
            ['id' => 7, 'nama_fasilitas' => 'Kursi'],
            ['id' => 8, 'nama_fasilitas' => 'Meja'],
        ]);
    }

    private function seedGedungLantaiRuangan(): array
    {
        $gedungList = [
            ['nama' => 'Gedung A', 'lantai' => 4, 'kode' => 'A'],
            ['nama' => 'Gedung B', 'lantai' => 5, 'kode' => 'B'],
            ['nama' => 'Gedung C', 'lantai' => 3, 'kode' => 'C'],
            ['nama' => 'Gedung D', 'lantai' => 6, 'kode' => 'D'],
            ['nama' => 'Gedung E', 'lantai' => 4, 'kode' => 'E'],
            ['nama' => 'Gedung F', 'lantai' => 2, 'kode' => 'F'],
        ];

        $fotoRuangan = [
            'ruangan_1_1777277943_afabf9be99c0.jpg',
            'ruangan_2_1777278058_36ceea00a9c4.jpg',
            'ruangan_2_1777278058_437ed3595f63.jpg',
            'ruangan_2_1777278058_99473a102fc8.jpg',
            'ruangan_2_1777278058_ba57546c6b25.jpg',
        ];

        $roomIds = [];
        $roomIndex = 0;

        foreach ($gedungList as $gedungIndex => $gedung) {
            $gedungId = DB::table('gedung')->insertGetId(['nama_gedung' => $gedung['nama']]);
            $lantaiIds = [];

            for ($nomor = 1; $nomor <= $gedung['lantai']; $nomor++) {
                $lantaiIds[$nomor] = DB::table('lantai')->insertGetId([
                    'gedung_id' => $gedungId,
                    'nomor' => $nomor,
                ]);
            }

            for ($i = 1; $i <= 5; $i++) {
                $lantaiNomor = (($i + $gedungIndex) % $gedung['lantai']) + 1;
                $nomorRuang = ($lantaiNomor * 100) + $i;
                $roomIndex++;

                $ruanganId = DB::table('ruangan')->insertGetId([
                    'nama_ruangan' => 'Ruang ' . $gedung['kode'] . $nomorRuang,
                    'lantai_id' => $lantaiIds[$lantaiNomor],
                    'kapasitas' => [25, 30, 35, 40, 45, 50, 60, 75][($roomIndex - 1) % 8],
                    'deskripsi' => 'Ruangan aktif untuk kelas, rapat, seminar kecil, dan kegiatan mahasiswa.',
                ]);

                $roomIds[] = $ruanganId;

                DB::table('ruangan_foto')->insert([
                    [
                        'ruangan_id' => $ruanganId,
                        'nama_file' => $fotoRuangan[($roomIndex - 1) % count($fotoRuangan)],
                        'tipe' => 'cover',
                        'created_at' => now(),
                    ],
                    [
                        'ruangan_id' => $ruanganId,
                        'nama_file' => $fotoRuangan[$roomIndex % count($fotoRuangan)],
                        'tipe' => 'detail',
                        'created_at' => now(),
                    ],
                ]);

                $facilityCount = mt_rand(4, 7);
                $facilityIds = range(1, 8);
                shuffle($facilityIds);

                foreach (array_slice($facilityIds, 0, $facilityCount) as $facilityId) {
                    DB::table('ruangan_fasilitas')->insert([
                        'ruangan_id' => $ruanganId,
                        'fasilitas_id' => $facilityId,
                    ]);
                }
            }
        }

        return $roomIds;
    }

    private function seedUsers(): array
    {
        $now = now();
        $password = Hash::make('password');
        $users = [
            ['nama' => 'Admin Utama', 'username' => 'admin01', 'email' => 'admin01@siperu.test', 'role' => 'admin', 'prodi' => null],
            ['nama' => 'Dina Admin', 'username' => 'admin02', 'email' => 'admin02@siperu.test', 'role' => 'admin', 'prodi' => null],
            ['nama' => 'Rafi Admin', 'username' => 'admin03', 'email' => 'admin03@siperu.test', 'role' => 'admin', 'prodi' => null],
            ['nama' => 'Alya Putri', 'username' => 'mhs001', 'email' => 'alya@siperu.test', 'role' => 'mahasiswa', 'prodi' => 'Sistem Informasi'],
            ['nama' => 'Bagas Pratama', 'username' => 'mhs002', 'email' => 'bagas@siperu.test', 'role' => 'mahasiswa', 'prodi' => 'Teknik Informatika'],
            ['nama' => 'Citra Lestari', 'username' => 'mhs003', 'email' => 'citra@siperu.test', 'role' => 'mahasiswa', 'prodi' => 'Bisnis Digital'],
            ['nama' => 'Dimas Saputra', 'username' => 'mhs004', 'email' => 'dimas@siperu.test', 'role' => 'mahasiswa', 'prodi' => 'Manajemen Informatika'],
            ['nama' => 'Elma Zahra', 'username' => 'mhs005', 'email' => 'elma@siperu.test', 'role' => 'mahasiswa', 'prodi' => 'Sistem Informasi'],
            ['nama' => 'Fajar Nugroho', 'username' => 'mhs006', 'email' => 'fajar@siperu.test', 'role' => 'mahasiswa', 'prodi' => 'Teknik Informatika'],
            ['nama' => 'Gita Anjani', 'username' => 'mhs007', 'email' => 'gita@siperu.test', 'role' => 'mahasiswa', 'prodi' => 'Akuntansi'],
            ['nama' => 'Hana Kirana', 'username' => 'mhs008', 'email' => 'hana@siperu.test', 'role' => 'mahasiswa', 'prodi' => 'Komunikasi'],
            ['nama' => 'Iqbal Ramadhan', 'username' => 'mhs009', 'email' => 'iqbal@siperu.test', 'role' => 'mahasiswa', 'prodi' => 'Sistem Informasi'],
            ['nama' => 'Jihan Maulida', 'username' => 'mhs010', 'email' => 'jihan@siperu.test', 'role' => 'mahasiswa', 'prodi' => 'Teknik Informatika'],
            ['nama' => 'Kevin Alfarizi', 'username' => 'mhs011', 'email' => 'kevin@siperu.test', 'role' => 'mahasiswa', 'prodi' => 'Bisnis Digital'],
            ['nama' => 'Laras Wening', 'username' => 'mhs012', 'email' => 'laras@siperu.test', 'role' => 'mahasiswa', 'prodi' => 'Manajemen Informatika'],
        ];

        foreach ($users as $user) {
            DB::table('users')->insert(array_merge($user, [
                'password' => $password,
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }

        return DB::table('users')->where('role', 'admin')->pluck('id')->all();
    }

    private function seedPeminjaman(array $roomIds, array $adminIds): void
    {
        $studentIds = DB::table('users')->where('role', 'mahasiswa')->pluck('id')->all();
        $monthlyTargets = [
            ['2026-01-01', '2026-01-31', 42],
            ['2026-02-01', '2026-02-28', 38],
            ['2026-03-01', '2026-03-31', 45],
            ['2026-04-01', '2026-04-30', 41],
            ['2026-05-01', '2026-05-05', 34],
        ];

        foreach ($monthlyTargets as [$start, $end, $count]) {
            $this->createRandomPeminjaman($start, $end, $count, $studentIds, $roomIds, $adminIds, false);
        }

        $this->createTodaySchedule($studentIds, $roomIds, $adminIds);
        $this->createRandomPeminjaman('2026-05-07', '2026-12-20', 95, $studentIds, $roomIds, $adminIds, true);
    }

    private function createRandomPeminjaman(
        string $start,
        string $end,
        int $count,
        array $studentIds,
        array $roomIds,
        array $adminIds,
        bool $allowPending
    ): void {
        $startDate = Carbon::parse($start);
        $days = $startDate->diffInDays(Carbon::parse($end));

        for ($i = 0; $i < $count; $i++) {
            $date = $startDate->copy()->addDays(mt_rand(0, $days));
            $slot = $this->timeSlots[mt_rand(0, count($this->timeSlots) - 1)];
            $statusId = $allowPending ? $this->futureStatus($date) : $this->historicalStatus();
            $createdAt = $date->copy()->subDays(mt_rand(3, 21))->setTime(mt_rand(8, 18), mt_rand(0, 59));

            $this->insertPeminjaman([
                'user_id' => $studentIds[array_rand($studentIds)],
                'ruangan_id' => $roomIds[array_rand($roomIds)],
                'nama_kegiatan' => $this->kegiatan[array_rand($this->kegiatan)],
                'tanggal' => $date->toDateString(),
                'jam_mulai' => $slot[0],
                'jam_selesai' => $slot[1],
                'jumlah_peserta' => mt_rand(12, 68),
                'surat' => 'surat_' . $date->format('Ymd') . '_' . str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT) . '.pdf',
                'status_id' => $statusId,
                'catatan_admin' => $this->catatanAdmin($statusId),
                'created_at' => $createdAt,
            ], $adminIds);
        }
    }

    private function createTodaySchedule(array $studentIds, array $roomIds, array $adminIds): void
    {
        $items = [
            ['07:30:00', '09:00:00', 2, 'Rapat Koordinasi'],
            ['08:00:00', '10:00:00', 2, 'Kelas Pengganti'],
            ['09:15:00', '10:45:00', 3, 'Workshop Media'],
            ['10:00:00', '12:00:00', 2, 'Presentasi Proyek'],
            ['11:00:00', '12:30:00', 2, 'Klinik Proposal'],
            ['13:00:00', '14:30:00', 1, 'Rapat Himpunan'],
            ['13:30:00', '15:00:00', 2, 'Seminar Mini'],
            ['14:45:00', '16:15:00', 3, 'Latihan Debat'],
            ['16:30:00', '18:00:00', 1, 'Briefing Panitia'],
            ['18:30:00', '20:00:00', 2, 'Kajian Riset'],
        ];

        foreach ($items as $index => [$mulai, $selesai, $statusId, $kegiatan]) {
            $this->insertPeminjaman([
                'user_id' => $studentIds[$index % count($studentIds)],
                'ruangan_id' => $roomIds[($index * 3) % count($roomIds)],
                'nama_kegiatan' => $kegiatan,
                'tanggal' => '2026-05-06',
                'jam_mulai' => $mulai,
                'jam_selesai' => $selesai,
                'jumlah_peserta' => [22, 35, 28, 41, 18, 30, 55, 20, 32, 26][$index],
                'surat' => 'surat_20260506_' . str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT) . '.pdf',
                'status_id' => $statusId,
                'catatan_admin' => $this->catatanAdmin($statusId),
                'created_at' => Carbon::parse('2026-05-04')->setTime(9 + ($index % 8), 15),
            ], $adminIds);
        }
    }

    private function insertPeminjaman(array $data, array $adminIds): void
    {
        $peminjamanId = DB::table('peminjaman')->insertGetId($data);

        DB::table('log_status')->insert([
            'peminjaman_id' => $peminjamanId,
            'status_id' => 1,
            'diubah_oleh' => null,
            'catatan' => 'Pengajuan dibuat oleh mahasiswa.',
            'waktu' => $data['created_at'],
        ]);

        if ((int) $data['status_id'] !== 1) {
            DB::table('log_status')->insert([
                'peminjaman_id' => $peminjamanId,
                'status_id' => $data['status_id'],
                'diubah_oleh' => $adminIds[array_rand($adminIds)],
                'catatan' => $data['catatan_admin'] ?: 'Pengajuan diproses admin.',
                'waktu' => Carbon::parse($data['created_at'])->addHours(mt_rand(4, 48)),
            ]);
        }
    }

    private function historicalStatus(): int
    {
        return mt_rand(1, 100) <= 76 ? 2 : 3;
    }

    private function futureStatus(Carbon $date): int
    {
        if ($date->isSameDay(Carbon::parse('2026-05-07')) || mt_rand(1, 100) <= 34) {
            return 1;
        }

        return mt_rand(1, 100) <= 72 ? 2 : 3;
    }

    private function catatanAdmin(int $statusId): ?string
    {
        return match ($statusId) {
            2 => ['Disetujui, gunakan ruangan sesuai jadwal.', 'Disetujui oleh admin.', 'OK, fasilitas sudah dicatat.'][mt_rand(0, 2)],
            3 => ['Ditolak karena jadwal bentrok.', 'Ditolak, kelengkapan surat belum sesuai.', 'Ditolak karena kapasitas melebihi ruangan.'][mt_rand(0, 2)],
            4 => 'Kegiatan selesai.',
            5 => 'Dibatalkan oleh peminjam.',
            default => null,
        };
    }
}
