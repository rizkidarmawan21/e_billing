# PRD — Halaman Tagihan Belum Dibayar

**Status:** Scope dikunci untuk implementasi
**Produk:** e_billing
**Jenis perubahan:** Halaman baru untuk owner/admin melihat daftar tagihan belum dibayar

## 1. Latar belakang

Owner ingin melihat semua tagihan yang belum dibayar tanpa harus lebih dulu mengatur filter pada halaman tagihan yang ada. Fitur filter periode memang tersedia, tetapi alur saat ini tidak membuat daftar tunggakan langsung terlihat.

## 2. Tujuan

Sediakan satu halaman khusus yang saat dibuka langsung menampilkan semua tagihan berstatus belum dibayar. Owner dapat mempersempit daftar berdasarkan periode tagihan, melihat ringkasan jumlah dan nominal, lalu membuka detail tagihan.

## 3. Pengguna dan akses

- Pengguna: owner dan admin yang berwenang mengelola tagihan.
- Halaman mengikuti autentikasi dan otorisasi e_billing yang sudah berlaku.
- Pengguna tanpa akses tidak boleh melihat data tagihan melalui URL langsung.

## 4. Perilaku halaman

- Sediakan menu/tautan **Tagihan Belum Dibayar** yang mudah ditemukan.
- Saat dibuka tanpa filter, tampilkan semua tagihan berstatus `belum_bayar` dari seluruh periode dan tahun.
- Jangan membatasi default ke bulan berjalan atau menyembunyikan tunggakan lama.
- Filter periode menggunakan bulan dan tahun tagihan (`periode`, `tahun`), bukan tanggal dibuat atau tanggal bayar.
- Sediakan preset **Semua periode**, **Bulan ini**, pilihan **Bulan + tahun**, serta **Rentang bulan** (bulan/tahun awal sampai bulan/tahun akhir, inklusif).
- Pencarian pelanggan yang sudah ada dapat digunakan kembali bila sesuai dengan pola halaman tagihan saat ini.
- Filter dan pencarian berjalan di server, dapat digabung, tervalidasi, serta dipertahankan saat navigasi pagination.
- Urutkan hasil dari periode tagihan paling lama ke paling baru; nama pelanggan menjadi urutan sekunder.

## 5. Ringkasan

Di atas tabel, tampilkan:

- Jumlah tagihan belum dibayar pada hasil filter.
- Total nominal tagihan belum dibayar pada hasil filter.

Ringkasan harus memakai cakupan filter yang sama dengan tabel. Nilai dihitung dari data tagihan unik, bukan jumlah pelanggan.

## 6. Tabel

Kolom minimum:

1. Nama pelanggan.
2. Nomor/identitas tagihan yang sudah digunakan aplikasi; jika tidak ada, jangan menambahkan identifier baru untuk kebutuhan PRD ini.
3. Periode tagihan (bulan dan tahun).
4. Tanggal jatuh tempo, jika field dan aturan jatuh tempo memang tersedia. Jika tidak tersedia, jangan mengarang tanggal; tampilkan penanda tunggakan berdasarkan aturan bisnis yang sudah ada atau hilangkan kolom tersebut.
5. Nominal tagihan.
6. Status: **Belum dibayar**; beri penanda **Lewat jatuh tempo** hanya jika aturan/field jatuh tempo resmi tersedia dan tanggalnya terlewati.
7. Kolom **Aksi pada setiap baris tagihan**: **Detail**, **Edit**, dan **Hapus**, mengikuti halaman tagihan existing. Semua aksi menargetkan tagihan pada baris tersebut, bukan seluruh tagihan pelanggan. Detail dan Edit memakai halaman existing; Hapus wajib konfirmasi, CSRF, dan otorisasi backend. Setelah edit/hapus berhasil, kembali ke daftar dengan filter tetap aktif; tabel dan ringkasan diperbarui. Tagihan yang berubah menjadi lunas tidak lagi muncul dalam daftar unpaid.

Gunakan pagination yang mengikuti pola halaman existing. State kosong menjelaskan bahwa tidak ada tagihan belum dibayar untuk cakupan terpilih dan menyediakan cara menghapus filter.

## 7. Definisi status dan tanggal

- Keanggotaan daftar ditentukan hanya oleh `payments.status = belum_bayar`.
- **Belum dibayar** tidak otomatis berarti **menunggak/lewat jatuh tempo**.
- Jangan menambah atau mengasumsikan tanggal jatuh tempo. Periksa field dan aturan bisnis existing saat implementasi; bila belum ada, cukup tampilkan status belum dibayar.
- Periode bulan/tahun yang menjadi acuan adalah `payments.periode` dan `payments.tahun`.

## 8. Di luar cakupan

- Mengubah alur pencatatan/pembayaran tagihan.
- Membuat atau mengubah aturan jatuh tempo.
- Mengirim pengingat, notifikasi, atau WhatsApp.
- Export, bulk action, dan pelunasan massal.
- Mengubah data historis atau skema database, kecuali implementasi membuktikan ada kebutuhan teknis wajib dan perubahan itu disetujui terpisah.
- Mengganti halaman daftar tagihan existing; halaman baru melengkapi halaman tersebut.

## 9. Kriteria penerimaan

1. Pengguna berwenang bisa membuka halaman melalui navigasi e_billing; akses tetap terlindungi.
2. Tanpa filter, semua tagihan berstatus `belum_bayar` lintas periode/tahun tampil, dan tagihan lunas tidak tampil.
3. Filter bulan+tahun menghasilkan tagihan sesuai periode tagihan.
4. Filter rentang bulan inklusif, termasuk rentang lintas tahun, menghasilkan data yang benar.
5. Preset bulan ini memakai bulan dan tahun berjalan berdasarkan timezone aplikasi.
6. Pencarian (bila digunakan) dapat dikombinasikan dengan filter periode; pagination mempertahankan parameter filter.
7. Jumlah dan total nominal selalu sesuai dengan hasil filter, bukan keseluruhan data di luar filter.
8. Daftar diurutkan dari periode tertua ke terbaru; hasil kosong memiliki pesan dan opsi reset filter.
9. Status belum dibayar tidak dilabeli terlambat kecuali aturan jatuh tempo resmi tersedia dan terpenuhi.
10. Tidak ada perubahan status/data pembayaran hanya karena halaman atau filter dibuka.

## 10. Catatan implementasi

- Reuse model, relasi, query pattern, komponen tabel, pencarian, dan pagination e_billing yang tersedia; hindari duplikasi logika tidak perlu.
- Controller existing `PaymentController@index` saat ini berorientasi daftar pelanggan dan memilih satu `currentPayment`, sementara dashboard owner punya filter bulan. Halaman unpaid perlu mengembalikan baris tagihan (`Payment`) per tagihan, bukan satu baris per pelanggan.
- Sebelum implementasi, cek middleware/role aktual dan apakah field nomor tagihan serta jatuh tempo memang tersedia. Jangan menambah skema hanya untuk mengisi kolom opsional.
- Tambahkan feature tests untuk default lintas periode, status, filter bulan, rentang lintas tahun, ringkasan, akses, dan pagination.
