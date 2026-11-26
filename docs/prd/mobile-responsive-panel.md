# PRD: Mobile-responsive panel admin dan owner

- Produk: e_billing
- Tanggal: 2026-11-26
- Status: Disetujui pengguna untuk implementasi pada 2026-11-26; verifikasi penerimaan belum selesai
- Branch kerja: `feat/ui-mobile`

## 1. Masalah

Panel e_billing harus dapat digunakan untuk operasional sehari-hari dari HP, bukan hanya desktop. Scope meliputi seluruh panel admin dan owner, termasuk daftar data, CRUD, detail, filter, pagination, laporan, dan pengaturan profil.

Temuan awal dari source:

- `resources/views/layouts/admin.blade.php` dan `resources/views/owner/layout.blade.php` selalu menampilkan sidebar selebar 250px, tanpa mode navigasi mobile.
- Header, profil, dan padding konten pada kedua layout masih berorientasi desktop.
- Header daftar pelanggan dan payment menyusun penjelasan serta tombol aksi secara horizontal.
- Tabel pelanggan dan payment mengandung banyak kolom dan aksi; wrapper horizontal-scroll saja tidak menjamin pengguna dapat menjalankan tugas dengan nyaman.
- Dashboard owner memiliki filter dropdown berukuran tetap 360px dan deretan statistik dengan kolom otomatis.
- Halaman unpaid juga menggunakan filter dropdown berukuran tetap dan tabel padat.
- Markup layout, header, tombol aksi, dan form berulang pada banyak view. Komponen Blade existing belum menjadi sistem UI bersama untuk panel.

Temuan ini merupakan hasil inspeksi source, bukan klaim bahwa seluruh halaman telah diaudit secara visual. Audit browser dengan data representatif menjadi langkah awal implementasi.

## 2. Tujuan

1. Admin dan owner dapat menyelesaikan tugas operasional existing dari HP tanpa bergantung pada tampilan desktop.
2. Seluruh halaman aktif memiliki layout mobile, tablet, dan desktop yang sengaja dirancang, bukan sekadar diperkecil.
3. UI konsisten dan lebih mudah dirawat melalui komponen Blade yang digunakan kembali.
4. Tidak mengubah aturan bisnis, data historis, hak akses, kontrak request, atau hasil perhitungan.
5. Desktop tetap nyaman dan tidak mengalami regresi fungsional maupun visual.

## 3. Pengguna dan tugas utama

### Admin

- Melihat ringkasan billing.
- Mencari, membuat, melihat detail, dan memperbarui pelanggan serta paket.
- Membuat tagihan, melihat riwayat, dan mencatat pembayaran melalui flow existing.
- Melihat tagihan unpaid, menggunakan filter, dan menjalankan aksi per tagihan.
- Mengelola profil dan password sendiri.

### Owner

- Melakukan operasi bersama sesuai izin existing.
- Melihat dashboard owner, monitoring, dan laporan.
- Menggunakan filter untuk memahami data billing.
- Mengelola akses admin.

UI wajib mengikuti role existing. Menyembunyikan menu bukan pengganti otorisasi backend. Perubahan responsive tidak boleh memperluas izin role mana pun.

## 4. Scope halaman

### Penegasan scope dari pengguna

Scope adalah **seluruh web e_billing yang aktif**, bukan hanya halaman dashboard atau perubahan sidebar. Semua menu pada screenshot pengguna masuk scope: **Dashboard, User, Paket, Tagihan Unpaid, Payment, Monitoring, dan Akses Admin**, untuk tampilan serta hak akses masing-masing role.

Label **User** pada sidebar merujuk ke modul pelanggan (`customers`), berbeda dari **Akses Admin**. Setiap menu mencakup seluruh halaman turunannya dan semua interaksi existing: daftar, pencarian, filter, pagination, create, detail, edit, delete/konfirmasi, serta flow khusus yang memang tersedia. Halaman yang diakses dari tombol, dropdown profil, atau direct route aktif juga masuk scope meskipun tidak tercantum di sidebar, termasuk laporan, profil, dan autentikasi aktif. Tidak perlu menambahkan operasi yang belum tersedia hanya untuk melengkapi istilah CRUD.

Checklist penerimaan harus diturunkan dari inventaris route dan tautan UI aktif; screenshot sidebar bukan batas jumlah halaman. Satu modul belum selesai bila hanya halaman index-nya sudah responsive sementara form, detail, dialog, atau subhalamannya belum.

### Matriks halaman dan flow

| Area | Halaman/flow yang masuk scope | Pengguna |
| --- | --- | --- |
| Shared shell | Header, sidebar/navigasi mobile, profil menu, logout, judul halaman | Admin dan owner |
| Dashboard admin | Ringkasan, filter, statistik, grafik | Admin |
| Dashboard owner | Ringkasan, filter bulan, statistik, grafik | Owner |
| Pelanggan | Daftar, pencarian, pagination, create, detail, edit, konfirmasi delete | Sesuai izin existing |
| Paket | Daftar, create, detail, edit, konfirmasi delete | Sesuai izin existing |
| Payment/tagihan | Daftar, pencarian, pagination, create, detail/riwayat, edit, delete, generate bulanan existing | Sesuai izin existing |
| Unpaid | Daftar tagihan, ringkasan, filter, pencarian, pagination, detail/edit/delete dan return state | Admin dan owner |
| Laporan | Filter, ringkasan, tabel, hasil kosong, seluruh aksi existing | Owner |
| Monitoring | Seluruh konten dan interaksi existing | Owner |
| Akses admin | Daftar, create, edit, konfirmasi delete | Owner |
| Profil | Update informasi, update password, konfirmasi hapus akun | Admin dan owner |
| Autentikasi aktif | Login, lupa/reset password, konfirmasi password dan verifikasi email bila digunakan | Sesuai flow existing |

Scope mengikuti route aktif. View legacy yang tidak dipakai tidak perlu direfactor. Registrasi publik yang sudah dinonaktifkan tidak boleh diaktifkan kembali. Operasi delete tidak harus memiliki halaman terpisah; konfirmasi harus nyaman digunakan pada HP.

## 5. Prinsip desain

- Target: aplikasi operasional yang jelas, praktis, dan mudah disentuh.
- Pertahankan identitas biru, Bootstrap existing, dan istilah bisnis yang sudah digunakan.
- Mobile adalah layout tersendiri: susunan, kepadatan, dan prioritas informasi disesuaikan.
- Tidak melakukan rebranding, penggantian framework, atau menambahkan library UI baru tanpa kebutuhan yang terbukti.
- Tidak menambah animasi dekoratif, elemen pemasaran, statistik palsu, atau ornamen yang mengganggu pekerjaan.
- Sebelum coding, tentukan contoh visual mobile dan desktop untuk shared shell, daftar tagihan, serta form. Jadikan contoh tersebut acuan seluruh halaman.

## 6. Kebutuhan fungsional UI

### 6.1 Navigasi dan shared shell

- Pada HP dan tablet sempit, sidebar tidak memakan ruang permanen pada konten.
- Gunakan drawer/offcanvas dari Bootstrap, dibuka melalui tombol berlabel `Menu`, bukan ikon tanpa konteks.
- Drawer menampilkan navigasi sesuai role dan menandai halaman aktif.
- Drawer dapat ditutup melalui tombol tutup, backdrop, dan Escape; fokus kembali ke pemicu dan background tidak ikut menerima navigasi keyboard saat drawer terbuka.
- Pada layar lebar, sidebar tetap terlihat dan konten memanfaatkan lebar yang tersedia.
- Saat viewport berubah, tidak boleh ada backdrop tertinggal, scroll-lock macet, atau fokus pada elemen yang tersembunyi.
- Header mobile ringkas. Nama/email panjang tidak mendorong judul atau menu keluar layar; informasi lengkap tetap dapat diakses.
- Logout tetap POST dengan CSRF. Link profil dan nama route dipertahankan.

### 6.2 Header halaman dan aksi

- Judul, deskripsi, dan aksi utama memiliki susunan yang konsisten.
- Pada HP, header dan aksi dapat tersusun vertikal atau wrap tanpa tabrakan.
- Aksi destruktif dibedakan dari aksi utama dan tidak diletakkan terlalu dekat tanpa jarak.
- Tombol penting tidak terpotong, bergantung pada hover, atau tersembunyi akibat overflow.
- Tidak menambahkan fixed action bar bila tidak diperlukan. Jika digunakan, ruang konten dan safe-area wajib diperhitungkan.

### 6.3 Daftar dan tabel

- Pada desktop, pertahankan tabel untuk pemindaian dan perbandingan banyak baris.
- Pada HP, daftar operasional berubah menjadi record/card berlabel yang menyajikan identitas, informasi utama, status, serta aksi yang jelas.
- Semua field existing tetap dapat dibaca atau diakses. Jangan menghilangkan informasi hanya agar muat.
- Jangan menduplikasi form dan kontrol interaktif untuk mobile/desktop. Gunakan satu sumber markup/data bila memungkinkan.
- Tabel analitis yang memang memerlukan perbandingan antarkolom boleh memakai scroll horizontal lokal dengan petunjuk yang jelas; bukan solusi default untuk seluruh daftar CRUD.
- Identitas pelanggan, periode, nominal, dan status pembayaran diprioritaskan pada daftar tagihan.
- Aksi Detail/Edit/Hapus harus menargetkan record yang sama pada semua ukuran layar, termasuk riwayat tagihan.
- Nama, alamat, kode, email, dan nominal panjang harus wrap secara aman tanpa memotong akses ke aksi.
- Pembacaan tabel/list oleh screen reader tetap memiliki hubungan label dan nilai yang jelas.

### 6.4 Pencarian, filter, dan pagination

- Pencarian dan filter dapat digunakan tanpa zoom atau geser seluruh halaman.
- Dropdown/popover filter tidak boleh lebih lebar dari ruang viewport yang tersedia. Konten panjang tetap dapat di-scroll dan tombol Terapkan/Reset dapat dijangkau.
- Pilihan bulan/switch/checkbox memiliki label yang dapat disentuh dan state terlihat.
- Perilaku parameter filter, pencarian, sorting, dan cakupan tahun tidak berubah dalam pekerjaan ini.
- Pagination mempertahankan filter dan pencarian, serta menyediakan kontrol sebelumnya/berikutnya yang mudah disentuh pada HP.
- Setelah edit/delete dari unpaid, return state tetap mengikuti flow existing; perubahan status menjadi lunas menghilangkan record dari daftar unpaid sesuai perilaku backend.
- Perbedaan kebutuhan filter tahun/rentang pada PRD unpaid lama ditangani sebagai scope terpisah, bukan diam-diam ditambahkan dalam responsive enhancement.

### 6.5 Form create/edit dan detail

- Form satu kolom pada HP; lebih banyak kolom hanya ketika ruang mendukung keterbacaan label/input.
- Label, helper text, required marker, error, dan nilai input tetap berhubungan dengan field yang benar.
- Kontrol teks pada HP memakai ukuran font minimal 16px untuk menghindari auto-zoom input pada browser mobile yang relevan.
- Input/select/textarea tidak melebihi container. Textarea dan konten alamat dapat tumbuh sesuai kebutuhan.
- Simpan, Batal, Kembali, dan aksi lain mudah dijangkau; urutan konsisten dan tidak terpotong keyboard/layout.
- Validasi backend existing dipertahankan; error tampil jelas dan nilai input tidak hilang.
- Date, nominal, customer selection/autocomplete, package selection, serta conditional payment-date fields tetap bekerja.
- Form submit/disabled/loading state mencegah interaksi membingungkan tanpa menjanjikan proteksi duplikasi backend yang belum ada.
- Konfirmasi delete/cancel dan hapus akun dapat dijalankan/dibatalkan melalui touch dan keyboard, tidak membuka aksi destruktif saat hanya membuka halaman.
- Password pada form aktif tersembunyi secara default dan memiliki toggle tampil/sembunyikan yang aksesibel, termasuk field konfirmasi. Toggle tidak melakukan submit atau mereset nilai/autocomplete.

### 6.6 Dashboard, grafik, laporan, monitoring

- Statistik menggunakan susunan yang menyesuaikan ruang; tidak dipaksa menjadi deretan sempit di HP.
- Nominal dan angka panjang tidak overflow atau terpotong.
- Grafik mengikuti lebar container dengan tinggi terbaca, bukan ukuran desktop yang dipaksakan.
- Legend, label, tooltip, dan filter tetap dapat digunakan pada touch. Data penting tidak hanya tersedia lewat hover.
- State tanpa data tidak menampilkan grafik seolah terdapat transaksi; beri penjelasan kontekstual.
- Data, formula statistik, query, dan definisi status tidak diubah.
- Monitoring tidak boleh memperoleh tombol atau klaim live-data baru yang belum didukung implementasi existing.

### 6.7 Empty, error, feedback

- Hasil kosong membedakan belum ada data dan pencarian/filter tanpa hasil, dengan reset atau aksi existing yang relevan.
- Flash message dan error tidak mendorong layout keluar layar; dismiss button memiliki label aksesibel.
- Error tidak hanya ditandai warna. Loading state ditambahkan hanya pada interaksi yang memang asynchronous/menunggu, bukan skeleton buatan pada setiap render Blade.

## 7. Komponen Blade dan maintainability

### Tujuan

Kurangi markup berulang dan sentralisasikan perilaku responsive. Bukan membangun design system generik yang menambah abstraksi tanpa manfaat.

### Kandidat komponen minimum

Nama berikut merupakan usulan, belum komponen existing. Kontrak akhir ditetapkan setelah audit pola pemakaian.

| Komponen | Tanggung jawab |
| --- | --- |
| Shared panel layout | Struktur header, navigasi responsif, konten; entry layout admin/owner mempertahankan pemilihan role existing |
| Navigation item | Link, ikon existing, active state, `aria-current`, tanpa mengambil alih otorisasi |
| Page header | Judul, deskripsi opsional, slot aksi dengan reflow mobile |
| Form field | Label, helper/error, required state, attribute forwarding, slot input/select/textarea |
| Form actions | Susunan tombol submit/batal/kembali yang konsisten dan responsive |
| Data list/table | Wrapper/pola desktop-table dan mobile-record; isi record/kolom tetap dimiliki view domain |
| Empty state / feedback | Pesan dan aksi kontekstual, struktur aksesibel yang konsisten |
| Password field/toggle | Visibility control yang konsisten untuk form password aktif |

### Aturan komponen

- Reuse komponen existing yang cocok sebelum membuat baru.
- Ekstrak pola yang benar-benar berulang. Jangan membuat komponen untuk setiap div, badge, atau judul.
- Gunakan anonymous Blade components, props, slots, dan attribute bag bila cukup; jangan menambah service/repository untuk urusan presentasi.
- Component harus mempertahankan `id`, `name`, `value`, `old()`, `autocomplete`, error bag, CSRF, HTTP method, dan query string existing.
- ID kontrol unik; tidak ada form bersarang atau input tersembunyi duplikat akibat rendering dua versi UI.
- Role dan keputusan bisnis tidak disebar ke komponen visual generik. Backend tetap sumber izin dan validasi.
- Bootstrap utilities dan CSS bersama diprioritaskan; hindari style inline berulang atau selector global yang merusak halaman di luar panel.
- Refactor dilakukan bertahap dengan perubahan yang dapat direview, bukan rewrite seluruh view sekaligus.

## 8. Responsiveness dan accessibility

- Target minimum viewport: 320 CSS px. Pemeriksaan representatif: 320, 390, 768, 1024, dan 1440 CSS px; periksa juga transisi di sekitar breakpoint.
- Tidak ada horizontal scroll pada dokumen utama. Scroll lokal untuk tabel analitis yang disetujui bukan kegagalan selama dapat ditemukan dan digunakan.
- Breakpoint mengikuti Bootstrap existing dan kebutuhan konten; jangan mengarang daftar breakpoint per model HP.
- Target sentuh aksi utama dan kontrol ikon minimal 44 x 44 CSS px, dengan jarak yang cukup. Checkbox/radio menggunakan area label yang mudah disentuh.
- Semua aksi dapat dioperasikan dengan Tab, Shift+Tab, Enter/Space; Escape menutup drawer/dialog/dropdown sesuai pola kontrol.
- Focus indicator terlihat; tombol ikon mempunyai accessible name.
- Kontras teks dan ikon interaktif memenuhi WCAG AA yang relevan. Warna biru existing tidak boleh menjadi alasan mempertahankan pasangan warna yang tidak terbaca.
- Pengguna dapat zoom; tidak menonaktifkan pinch-to-zoom. Verifikasi reflow/akses konten pada 200% zoom.
- Safe-area dan keyboard mobile tidak menutupi kontrol penting.
- Tidak menambah dependency font/icon jarak jauh untuk responsive enhancement.

## 9. Di luar scope

- Perubahan aturan billing, definisi unpaid, jatuh tempo, harga, atau hitungan dashboard.
- Perubahan permission, aktivasi registrasi publik, endpoint baru, dan skema database.
- Perubahan sync Prism, queue delivery, integrasi pembayaran, atau deployment produksi.
- PWA, offline mode, push notification, dan aplikasi native.
- Fitur baru seperti export, bulk action, pelunasan massal, advanced filters, dan pencarian server baru.
- Rebranding, framework migration, upgrade dependency massal, dark-mode baru, atau animasi dekoratif.
- Commit/push/deploy otomatis tanpa permintaan yang sesuai.

Jika audit menemukan bug/security existing yang menghambat flow, dokumentasikan dan pisahkan perubahan. Jangan diam-diam mengubah aturan bisnis dalam refactor UI.

## 10. Urutan pengerjaan setelah approval

1. Audit browser kedua role dengan data representatif. Buat inventaris route/view aktif dan screenshot baseline mobile/desktop; catat flow dan keterbatasan existing.
2. Tetapkan acuan visual shared shell, satu daftar kompleks (tagihan), dan satu form. Review sebelum menerapkan pola ke seluruh panel.
3. Implementasikan shared layout/navigasi responsive dan komponen Blade minimum; verifikasi desktop dan mobile terlebih dahulu.
4. Terapkan pola ke pelanggan dan paket: list, create, detail, edit, delete.
5. Terapkan ke payment, riwayat, dan unpaid, dengan pengujian query/return state.
6. Terapkan dashboard kedua role, monitoring, laporan, dan akses admin.
7. Terapkan profil serta autentikasi aktif, termasuk password fields.
8. Jalankan regression/visual/accessibility checks seluruh matriks. Perbaiki overflow, target sentuh, dan kegagalan flow sebelum handoff.

Setiap tahap harus bisa divalidasi sendiri. Selesai shared layout tidak berarti seluruh scope selesai.

## 11. Acceptance criteria

1. Seluruh halaman aktif pada tabel scope dicatat dalam checklist validasi; tidak hanya dashboard/index.
2. Pada HP, konten tidak kehilangan ruang permanen untuk sidebar; menu dapat dibuka/ditutup, fokus dikelola, dan navigasi sesuai role.
3. Admin tidak memperoleh akses halaman owner melalui navigasi maupun direct URL; existing backend authorization tetap berlaku.
4. Tidak ada document-level horizontal overflow pada viewport target, termasuk saat dropdown, error, dan menu terbuka.
5. Daftar operasional dapat dibaca dan dioperasikan tanpa mencari aksi di ujung tabel lebar. Field tidak hilang dan action target benar.
6. Create/edit/detail/delete pelanggan, paket, payment, dan akses admin yang didukung existing dapat diselesaikan pada HP dengan hasil tersimpan sesuai request.
7. Pencarian, filter, pagination, validation error, dan return state unpaid tetap benar pada mobile/desktop.
8. Pengguna dapat membatalkan konfirmasi destruktif tanpa perubahan data; konfirmasi lanjut menjalankan action existing dengan CSRF/method yang benar.
9. Dashboard kedua role, grafik, laporan, monitoring, dan empty state terbaca pada mobile/tablet/desktop tanpa perubahan nilai bisnis.
10. Form profil dan password tetap bekerja. Visibility toggle tidak submit form atau menghilangkan nilai; input tersembunyi saat pertama dirender.
11. Komponen Blade benar-benar digunakan lintas halaman untuk pola berulang; bukan hanya dibuat tanpa menggantikan markup duplikat.
12. Navigasi/control penting dapat digunakan via keyboard, memiliki accessible name/focus, dan memenuhi ukuran sentuh yang ditetapkan.
13. Data panjang dan banyak record tidak merusak layout, termasuk nama/email/alamat panjang, nominal besar, dan pagination aktif.
14. `npm run build`, `php artisan test`, dan `git diff --check` lulus. Kegagalan baseline, jika ada, dicatat terpisah dengan bukti.
15. Browser tests mencakup kedua role dan CRUD happy path, validasi invalid form, filter/pagination, drawer, dropdown, delete cancel/confirm, serta password toggle.
16. Hasil handoff menyertakan checklist route/viewport/role, bukti screenshot representatif, perintah test dan hasil aktual, serta keterbatasan yang belum diverifikasi.

## 12. Strategi verifikasi

### Automated

- Pertahankan seluruh feature tests existing, terutama `tests/Feature/UnpaidBillsTest.php` dan authentication/profile tests.
- Tambahkan feature/component tests untuk role-aware shell dan kontrak markup penting bila sesuai.
- Repo belum memiliki setup Playwright yang ditemukan pada inspeksi awal. Untuk regression UI lintas halaman, usulkan setup Playwright minimal di `tests/e2e/` sebagai dev-only tooling; instalasi/config ditetapkan pada implementation plan, bukan diasumsikan sudah tersedia.
- Browser test menggunakan database lokal/testing terisolasi dan fixture berlabel jelas. Jangan menulis transaksi uji ke produksi atau mengirim sync Prism.
- Gunakan data berisi record kosong, beberapa record, pagination, nilai panjang, serta status unpaid/lunas. Fixture bukan klaim data pelanggan nyata.

### Browser dan visual

- Setiap halaman dicek HTTP/render, responsive layout, aksi utama, dan browser console/runtime error.
- Uji kedua role pada HP dan desktop; lakukan responsive sweep di lebar perantara, termasuk filter/menu terbuka.
- Uji form dengan keyboard, kondisi invalid, nilai input dipertahankan, dan conditional fields.
- Uji resize drawer terbuka menuju desktop dan kembali ke mobile.
- Verifikasi Safari/iOS aktual bila tersedia. Chromium emulation tidak boleh dilaporkan sebagai pengujian Safari/perangkat fisik. Keterbatasan akses perangkat dilaporkan eksplisit.

## 13. Risiko dan keputusan yang perlu dijaga

- Markup duplikat untuk mobile/desktop dapat menghasilkan ID/form/action ganda. Utamakan satu sumber markup.
- Memaksa semua tabel menjadi kartu dapat merusak laporan analitis. Pilih pola berdasarkan tugas pengguna, bukan aturan CSS global.
- CSS global untuk `.d-flex`, `.table`, atau `.btn` dapat merusak desktop dan komponen Bootstrap. Scope styling ke panel/pola komponen.
- Refactor komponen dapat menghilangkan validation state, query parameter, dan conditional field logic. Lindungi dengan regression tests per flow.
- Data lokal kosong tidak cukup untuk mengklaim UX mobile selesai. Wajib fixture representatif dan skenario pagination.
- Sinkronisasi dan integrasi eksternal tetap dinonaktifkan pada pengujian lokal.

## 14. Status saat PRD dibuat

- Perubahan sebelumnya: toggle password login dan penambahan assertion authentication masih uncommitted pada branch `feat/ui-mobile`.
- Pada checkpoint sebelum penulisan PRD, git diff hanya menunjukkan dua file perubahan tersebut; belum terlihat perubahan source responsive panel.
- Implementasi responsive telah diminta berhenti untuk mendahulukan PRD. Tahap berikutnya adalah review scope dan acuan visual, bukan melanjutkan edit seluruh panel tanpa approval.
