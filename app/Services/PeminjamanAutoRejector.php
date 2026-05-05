<?php

namespace App\Services;

use App\Models\LogStatus;
use App\Models\Peminjaman;
use Illuminate\Support\Facades\DB;

class PeminjamanAutoRejector
{
    public const STATUS_PENDING = 1;
    public const STATUS_DITOLAK = 3;

    public function rejectExpiredPending(): int
    {
        $now = now('Asia/Jakarta');
        $today = $now->toDateString();
        $currentTime = $now->format('H:i:s');
        $note = 'Pengajuan ditolak otomatis karena waktu peminjaman telah lewat.';

        return DB::transaction(function () use ($today, $currentTime, $note) {
            $expired = Peminjaman::query()
                ->select('id', 'catatan_admin')
                ->where('status_id', self::STATUS_PENDING)
                ->where(function ($query) use ($today, $currentTime) {
                    $query->whereDate('tanggal', '<', $today)
                        ->orWhere(function ($q) use ($today, $currentTime) {
                            $q->whereDate('tanggal', $today)
                                ->where('jam_mulai', '<', $currentTime);
                        });
                })
                ->lockForUpdate()
                ->get();

            foreach ($expired as $peminjaman) {
                $peminjaman->update([
                    'status_id' => self::STATUS_DITOLAK,
                    'catatan_admin' => $peminjaman->catatan_admin ?: $note,
                ]);

                LogStatus::create([
                    'peminjaman_id' => $peminjaman->id,
                    'status_id' => self::STATUS_DITOLAK,
                    'diubah_oleh' => null,
                    'catatan' => $note,
                ]);
            }

            return $expired->count();
        });
    }
}
