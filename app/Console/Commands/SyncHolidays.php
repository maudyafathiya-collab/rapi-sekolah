<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SyncHolidays extends Command
{
    protected $signature = 'holidays:sync {year?}';

    protected $description = 'Sinkronisasi libur nasional dan cuti bersama Indonesia';

    public function handle(): int
    {
        $year = (int) ($this->argument('year') ?: now()->year);

        $apiKey = config('services.api_indonesia.key');
        $baseUrl = config('services.api_indonesia.url');

        if (!$apiKey || !$baseUrl) {
            $this->error('API Key atau URL API belum dikonfigurasi.');
            return self::FAILURE;
        }

        try {
            $response = Http::acceptJson()
                ->withHeaders([
                    'x-api-key' => $apiKey,
                ])
                ->timeout(30)
                ->get(rtrim($baseUrl, '/') . '/libur', [
                    'tahun' => $year,
                ]);

            $response->throw();

            $holidays = $response->json('data');

            if (!is_array($holidays)) {
                $this->error('Format data dari API tidak sesuai.');
                return self::FAILURE;
            }

            $rows = [];

            foreach ($holidays as $holiday) {
                if (
                    empty($holiday['date']) ||
                    empty($holiday['name']) ||
                    (isset($holiday['is_active']) && !$holiday['is_active'])
                ) {
                    continue;
                }

                $isCollectiveLeave =
                    (int) ($holiday['is_joint_leave'] ?? 0) === 1;

                $rows[] = [
                    'name' => $holiday['name'],
                    'date' => $holiday['date'],
                    'type' => $isCollectiveLeave
                        ? 'collective'
                        : 'national',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (empty($rows)) {
                $this->warn("Tidak ada data libur untuk tahun {$year}.");
                return self::SUCCESS;
            }

            DB::table('holidays')->upsert(
                $rows,
                ['date'],
                ['name', 'type', 'updated_at']
            );

            $this->info(
                count($rows) . " data libur tahun {$year} berhasil disinkronkan."
            );

            return self::SUCCESS;

        } catch (\Throwable $e) {
            $this->error('Sinkronisasi gagal: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
