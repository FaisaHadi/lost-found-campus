<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Claim;
use App\Models\Notification;
use App\Models\Report;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Elektronik', 'slug' => 'electronics', 'description' => 'Ponsel, laptop, pengisi daya, dan barang elektronik lainnya.'],
            ['name' => 'Dokumen', 'slug' => 'documents', 'description' => 'Kartu mahasiswa, sertifikat, buku, dan berkas.'],
            ['name' => 'Tas', 'slug' => 'bags', 'description' => 'Ransel, tote bag, dompet, dan kotak penyimpanan.'],
            ['name' => 'Kunci', 'slug' => 'keys', 'description' => 'Kunci kendaraan, ruangan, loker, dan gantungan kunci.'],
            ['name' => 'Pakaian', 'slug' => 'clothing', 'description' => 'Jaket, seragam, topi, dan aksesori.'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['slug' => $category['slug']],
                [...$category, 'status' => 'active']
            );
        }

        $admin = User::firstOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'Pengguna Admin',
            'password' => Hash::make('password123'),
            'role' => UserRole::Admin->value,
        ]);

        $student = User::firstOrCreate([
            'email' => 'test@example.com',
        ], [
            'name' => 'Pengguna Uji',
            'password' => Hash::make('password123'),
            'role' => UserRole::User->value,
        ]);

        $claimant = User::firstOrCreate([
            'email' => 'claimant@example.com',
        ], [
            'name' => 'Pengaju Klaim',
            'password' => Hash::make('password123'),
            'role' => UserRole::User->value,
        ]);

        $electronics = Category::where('slug', 'electronics')->first();
        $documents = Category::where('slug', 'documents')->first();
        $bags = Category::where('slug', 'bags')->first();

        $approvedReport = Report::firstOrCreate([
            'title' => 'Ditemukan ransel hitam dekat perpustakaan',
        ], [
            'user_id' => $student->id,
            'category_id' => $bags?->id,
            'description' => 'Ransel hitam ditemukan dekat pintu masuk perpustakaan utama setelah kelas pagi.',
            'report_type' => 'found',
            'location_text' => 'Pintu masuk Perpustakaan Utama',
            'latitude' => -6.2000000,
            'longitude' => 106.8166660,
            'status' => 'approved',
            'moderation_status' => 'approved',
        ]);

        Report::firstOrCreate([
            'title' => 'Kartu mahasiswa hilang di kantin',
        ], [
            'user_id' => $claimant->id,
            'category_id' => $documents?->id,
            'description' => 'Kartu mahasiswa terakhir terlihat di dekat kasir kantin.',
            'report_type' => 'lost',
            'location_text' => 'Kantin kampus',
            'latitude' => -6.2013000,
            'longitude' => 106.8172000,
            'status' => 'pending',
            'moderation_status' => 'pending',
        ]);

        Report::firstOrCreate([
            'title' => 'Ditemukan charger ponsel di lab',
        ], [
            'user_id' => $student->id,
            'category_id' => $electronics?->id,
            'description' => 'Charger USB-C putih ditemukan di lab komputer setelah sesi siang.',
            'report_type' => 'found',
            'location_text' => 'Lab Komputer 2',
            'latitude' => -6.1997000,
            'longitude' => 106.8159000,
            'status' => 'approved',
            'moderation_status' => 'approved',
        ]);

        Claim::firstOrCreate([
            'report_id' => $approvedReport->id,
            'claimant_id' => $claimant->id,
        ], [
            'proof_text' => 'Di dalam kompartemen depan ransel ada buku catatan kecil berwarna biru dan kantong kartu mahasiswa.',
            'status' => 'pending',
        ]);

        Notification::firstOrCreate([
            'user_id' => $student->id,
            'report_id' => $approvedReport->id,
            'title' => 'Laporan disetujui',
        ], [
            'message' => 'Laporan Anda "Ditemukan ransel hitam dekat perpustakaan" telah disetujui.',
            'status' => 'unread',
        ]);

        Notification::firstOrCreate([
            'user_id' => $admin->id,
            'report_id' => $approvedReport->id,
            'title' => 'Pengingat demo moderasi',
        ], [
            'message' => 'Gunakan data seed ini untuk mendemonstrasikan tinjauan laporan, pelacakan klaim, dan penanganan notifikasi.',
            'status' => 'unread',
        ]);
    }
}
