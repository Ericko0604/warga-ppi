# PROMPT PENGEMBANGAN WEBSITE DOKUMENTASI KEGIATAN WARGA

## 1. ROLE

Anda adalah **Senior Full-Stack Developer, System Analyst, UI/UX Designer, Database Designer, dan Software Architect**.

Bangun sebuah aplikasi web production-ready untuk dokumentasi kegiatan warga perumahan.

Aplikasi ditujukan untuk dua jenis pengguna:

1. **Warga**
2. **Admin**

Prioritas utama aplikasi:

* sangat mudah digunakan oleh warga yang gaptek
* warga TIDAK perlu login
* proses upload foto harus sesederhana mungkin
* foto otomatis dikompres
* seluruh foto menggunakan orientasi landscape
* admin memiliki kontrol penuh terhadap acara, kegiatan, warga, dan foto
* sistem aman walaupun warga tidak menggunakan akun
* responsive untuk smartphone karena mayoritas warga akan menggunakan HP

Jangan membuat sistem yang terlalu kompleks dari sisi UI.

---

# 2. TUJUAN SISTEM

Website digunakan sebagai pusat dokumentasi kegiatan dan acara perumahan.

Warga dapat melihat:

* acara yang telah berlangsung
* kegiatan yang telah berlangsung
* foto dokumentasi
* foto dari rumah masing-masing

Admin dapat:

* membuat acara
* membuat kegiatan
* menentukan apakah warga perlu upload foto
* mengupload foto dokumentasi
* menentukan thumbnail
* mengedit/crop thumbnail
* mengelola data warga
* menambahkan warga baru
* mengganti/menghapus foto
* melihat statistik upload

---

# 3. DATA WARGA

Struktur awal perumahan:

### Blok A1

Nomor rumah:
01 sampai 25

### Blok A2

Nomor rumah:
01 sampai 30

### Blok A3

Nomor rumah:
01 sampai 25

### Blok A4

Nomor rumah:
01 sampai 30

### Blok Kavling

Tidak menggunakan nomor rumah.

Identitas menggunakan:

**Nama Kepala Keluarga**

Contoh:

* Bapak Ahmad
* Bapak Budi
* Bapak Candra

Sistem harus memungkinkan admin menambahkan, mengedit, menonaktifkan, dan menghapus data warga.

Jangan hardcode data warga di source code.

Data harus berasal dari database.

---

# 4. JENIS KONTEN

Sistem memiliki dua jenis konten:

## A. ACARA

Contoh:

* 17 Agustus
* Halal Bihalal
* Tahun Baru
* Buka Bersama
* Jalan Sehat
* Perlombaan warga

Ketika admin membuat acara:

* semua rumah aktif mendapatkan slot upload
* setiap rumah hanya boleh memiliki maksimal 1 foto
* foto tersebut mewakili orang serumah
* admin dapat mengupload maksimal 10 foto
* admin dapat menentukan thumbnail acara
* admin dapat mengedit/crop thumbnail

Contoh:

```text
17 Agustus 2026

A1 No.01 → 1 foto
A1 No.02 → 1 foto
A1 No.03 → 1 foto
...
A4 No.30 → 1 foto
Kavling Bapak Ahmad → 1 foto
```

---

# 5. KEGIATAN

Contoh:

* Kerja Bakti
* Rapat Warga
* Posyandu
* Senam
* Pengajian
* Bersih Lingkungan

Saat membuat kegiatan, admin harus menentukan:

```text
Apakah warga perlu mengupload foto?

[ YA ]
[ TIDAK ]
```

Jika YA:

* setiap rumah aktif mendapatkan slot upload
* setiap rumah maksimal 1 foto
* admin maksimal 10 foto

Jika TIDAK:

* warga tidak mendapatkan slot upload
* hanya admin yang dapat mengupload foto
* admin maksimal 10 foto

---

# 6. SISTEM WARGA TANPA LOGIN

Warga TIDAK BOLEH diwajibkan membuat akun.

Jangan membuat:

* username warga
* password warga
* email warga
* registrasi warga
* OTP warga

Gunakan sistem **secure upload token + QR Code**.

Setiap rumah memiliki identitas/token upload yang aman.

Contoh:

```text
A1 No.07
```

memiliki token random:

```text
A8KX92M7Q
```

QR Code mengarah ke halaman upload.

Contoh alur:

```text
Warga scan QR
        ↓
Halaman upload
        ↓
Sistem mengetahui:
Event + Rumah
        ↓
Warga memilih foto
        ↓
Validasi foto
        ↓
Compress
        ↓
Upload
        ↓
Selesai
```

Jangan memperbolehkan warga memilih rumah secara bebas setelah membuka link token.

Token harus menentukan identitas rumah.

---

# 7. KEAMANAN TOKEN

Token harus:

* random
* sulit ditebak
* tidak menggunakan ID database secara langsung
* tidak menampilkan informasi sensitif
* dapat dinonaktifkan admin
* dapat diregenerasi jika diperlukan

Jangan menggunakan:

```text
/event/1/house/7
```

Gunakan token random seperti:

```text
/upload/7f9a2c81d1e84b7c
```

Tetapi jangan menggunakan contoh token tersebut secara literal.

Gunakan cryptographically secure random token.

Tambahkan:

* rate limiting
* validasi token
* validasi event
* validasi resident
* file validation
* MIME validation
* ukuran file maksimum
* image dimension validation
* proteksi upload file berbahaya
* CSRF protection
* authorization untuk admin

---

# 8. ADMIN AUTHENTICATION

Admin WAJIB login.

Admin memiliki:

* email/username
* password
* logout
* session management

Password harus disimpan menggunakan hashing yang aman.

Jangan pernah menyimpan password plaintext.

Admin dapat:

### Dashboard

Menampilkan:

* total warga
* total rumah aktif
* total acara
* total kegiatan
* total foto
* jumlah upload terbaru

---

# 9. ADMIN — DATA WARGA

Buat halaman:

```text
Data Warga
```

Admin dapat:

* melihat warga
* mencari warga
* filter berdasarkan blok
* tambah warga
* edit warga
* nonaktifkan warga
* aktifkan kembali warga
* regenerasi token
* melihat QR Code

Data:

```text
id
block
house_number
family_head_name
upload_token
status
created_at
updated_at
```

Untuk Kavling:

```text
block = KAVLING
house_number = NULL
family_head_name = Nama Kepala Keluarga
```

Jangan menggunakan nomor rumah untuk Kavling.

---

# 10. QR CODE

Admin dapat membuka data warga kemudian:

```text
[ Lihat QR ]
[ Download QR ]
```

QR harus mengarah ke URL upload/token warga.

Buat desain QR yang sederhana sehingga dapat dicetak.

Admin juga dapat mencetak daftar QR warga.

---

# 11. ADMIN — MEMBUAT ACARA

Form:

```text
Jenis
ACARA

Nama acara
Tanggal
Deskripsi
Thumbnail
Foto dokumentasi
```

Ketika acara dibuat:

* generate slot untuk semua warga aktif
* warga dapat upload maksimal 1 foto
* admin dapat upload maksimal 10 foto

---

# 12. ADMIN — MEMBUAT KEGIATAN

Form:

```text
Jenis
KEGIATAN

Nama kegiatan
Tanggal
Deskripsi

Apakah warga perlu upload foto?

( ) Ya
( ) Tidak

Thumbnail
Foto dokumentasi
```

Jika:

```text
Ya
```

generate slot warga.

Jika:

```text
Tidak
```

jangan membuat slot upload warga.

---

# 13. ADMIN PHOTO LIMIT

Admin dapat upload maksimal:

**10 foto per event/kegiatan.**

Jika sudah 10:

```text
Batas foto admin sudah mencapai 10 foto.
```

Frontend dan backend harus sama-sama melakukan validasi.

Jangan hanya membatasi melalui frontend.

---

# 14. RESIDENT PHOTO LIMIT

Setiap rumah:

**maksimal 1 foto per event/kegiatan.**

Database harus mencegah duplicate upload.

Gunakan constraint unik yang sesuai, misalnya:

```text
UNIQUE(event_id, resident_id)
```

Jika warga upload foto kedua:

Jangan membuat record baru.

Berikan opsi:

```text
Ganti Foto
```

Foto lama diganti dengan foto baru.

---

# 15. FOTO LANDSCAPE

Semua foto harus landscape.

Minimal:

```text
width > height
```

Jika portrait:

```text
Foto harus berformat landscape.
Silakan pilih foto lain.
```

Jangan mengubah foto portrait secara otomatis jika hasil crop dapat memotong orang secara tidak wajar.

Jika memungkinkan berikan preview sebelum upload.

---

# 16. AUTO COMPRESS

Implementasikan image processing.

Ketika user mengupload:

```text
Original
↓
Validate
↓
Resize
↓
Compress
↓
Convert
↓
Generate thumbnail
↓
Save storage
```

Target:

* maksimal width sekitar 1920 px
* kualitas sekitar 75–85%
* format WebP/JPEG
* hapus EXIF jika memungkinkan
* ukuran file hasil jauh lebih kecil daripada original

Jangan menyimpan foto original berukuran besar jika tidak diperlukan.

Buat minimal:

```text
display image
thumbnail image
```

Thumbnail digunakan untuk beranda.

---

# 17. THUMBNAIL

Setiap acara/kegiatan memiliki satu thumbnail.

Admin dapat:

1. upload thumbnail sendiri
2. memilih foto admin sebagai thumbnail
3. memilih foto warga sebagai thumbnail
4. crop/reposition thumbnail

Thumbnail menggunakan rasio:

```text
16:9
```

Buat image cropper yang sederhana.

Admin dapat:

* zoom
* reposition
* crop
* save

Tidak perlu membuat editor foto kompleks.

---

# 18. BERANDA

Beranda harus sangat sederhana.

Tampilkan:

### Hero / featured event

Event terbaru atau event yang dipilih admin.

Kemudian:

### Riwayat Acara dan Kegiatan

Urutkan berdasarkan tanggal terbaru.

Card:

```text
[ THUMBNAIL ]

17 Agustus 2026

16 Agustus 2026

Acara
```

atau:

```text
[ THUMBNAIL ]

Kerja Bakti

10 Agustus 2026

Kegiatan
```

Card dapat diklik untuk melihat dokumentasi.

Tambahkan filter sederhana:

```text
Semua
Acara
Kegiatan
```

---

# 19. HALAMAN DETAIL EVENT

Tampilkan:

```text
Thumbnail

Nama event
Tanggal
Deskripsi
Jenis event
```

Kemudian:

```text
Foto Admin
```

Maksimal 10 foto.

Kemudian:

```text
Dokumentasi Warga
```

Kelompokkan berdasarkan blok:

```text
A1
A2
A3
A4
Kavling
```

Contoh:

```text
A1

No.01 [foto]
No.02 [foto]
No.03 [foto]
No.04 [foto]

...
```

Jika belum upload:

```text
A1 No.07

Belum ada foto
```

Jangan menampilkan token upload kepada publik.

---

# 20. HALAMAN UPLOAD WARGA

Halaman ini harus menjadi halaman paling sederhana di seluruh aplikasi.

Contoh:

```text
📷 Dokumentasi Warga

17 Agustus 2026

Rumah:
A1 No.07

Foto maksimal 1

[ PILIH FOTO ]

Preview

[ KIRIM FOTO ]
```

Setelah berhasil:

```text
✅ Foto berhasil dikirim!

Terima kasih telah ikut mendokumentasikan
kegiatan warga.
```

Jika sudah pernah upload:

```text
Foto rumah Anda sudah tersedia.

[ GANTI FOTO ]
```

Jangan menampilkan dashboard atau menu yang tidak diperlukan.

---

# 21. MOBILE FIRST

Prioritas perangkat:

1. Smartphone
2. Tablet
3. Desktop

Semua halaman harus responsive.

Upload harus mudah dari:

* Android
* iPhone

Gunakan:

```html
<input type="file" accept="image/*">
```

Jika memungkinkan gunakan:

```html
capture="environment"
```

tetapi jangan memaksa kamera jika user ingin memilih foto dari gallery.

---

# 22. UX UNTUK WARGA GAPTEK

Gunakan:

* tombol besar
* teks sederhana
* ikon yang mudah dipahami
* warna kontras
* font mudah dibaca
* sedikit menu
* tidak menggunakan istilah teknis
* tidak menggunakan popup berlebihan

Hindari:

* sidebar kompleks
* dropdown bertingkat
* terlalu banyak tombol
* halaman penuh statistik
* istilah developer
* konfigurasi rumit

Contoh:

Gunakan:

```text
📷 Upload Foto
```

bukan:

```text
Manage Media Asset
```

Gunakan:

```text
Ganti Foto
```

bukan:

```text
Update Media Resource
```

---

# 23. DATABASE

Gunakan database relational.

Minimal tabel:

```text
users
residents
events
event_resident_uploads
photos
upload_tokens
```

Anda boleh mengubah struktur jika memiliki desain database yang lebih baik.

Tetapi database harus mendukung:

* warga
* blok
* rumah
* kepala keluarga
* acara
* kegiatan
* foto admin
* foto warga
* token upload
* status upload
* thumbnail
* timestamps

Gunakan foreign key dan index yang sesuai.

---

# 24. RECOMMENDED STACK

Gunakan:

### Backend

Laravel

### Frontend

Blade + Livewire atau Vue jika benar-benar diperlukan.

Prioritaskan kesederhanaan.

### CSS

Tailwind CSS

### Database

MySQL atau PostgreSQL

### Storage

S3-compatible object storage.

### Image Processing

Gunakan library image processing yang sesuai dengan Laravel.

### QR Code

Gunakan library QR Code yang mature dan aman.

---

# 25. STORAGE ARCHITECTURE

Jangan menyimpan file image binary di database.

Database hanya menyimpan metadata:

```text
photo_id
event_id
resident_id
file_path
thumbnail_path
mime_type
file_size
width
height
created_at
updated_at
```

File disimpan di object storage.

Contoh struktur:

```text
events/
    2026/
        event-{id}/
            thumbnail.webp
            admin/
                photo-01.webp
                photo-02.webp
            residents/
                resident-{id}.webp
```

Jangan gunakan nama file asli user sebagai nama file storage.

Generate nama file random/UUID.

---

# 26. ADMIN EVENT MANAGEMENT

Admin dapat:

* create event
* edit event
* publish event
* unpublish event
* delete event
* upload photo
* delete photo
* set thumbnail
* edit thumbnail
* melihat jumlah warga yang sudah upload

Contoh:

```text
17 Agustus 2026

Warga:
87 / 110 sudah upload

Admin:
7 / 10 foto
```

---

# 27. STATUS EVENT

Gunakan status:

```text
DRAFT
PUBLISHED
ARCHIVED
```

DRAFT:

Tidak terlihat publik.

PUBLISHED:

Terlihat di beranda dan warga dapat upload.

ARCHIVED:

Dokumentasi masih dapat dilihat tetapi upload dapat dinonaktifkan.

---

# 28. ADMIN EVENT DETAIL

Admin harus dapat melihat:

```text
17 Agustus 2026

Upload Warga

A1
23 / 25

A2
28 / 30

A3
21 / 25

A4
27 / 30

Kavling
8 / 10
```

Berikan progress bar.

Admin dapat mengetahui rumah mana yang belum upload.

---

# 29. SEARCH DAN FILTER

Admin membutuhkan:

### Data warga

Search:

```text
A1 No.07
```

atau:

```text
Budi Santoso
```

Filter:

```text
A1
A2
A3
A4
Kavling
```

### Event

Filter:

```text
Semua
Acara
Kegiatan
```

dan berdasarkan tahun.

---

# 30. VALIDASI UPLOAD

Validasi minimal:

* hanya image
* MIME type valid
* file extension valid
* ukuran file maksimal
* width/height valid
* landscape
* jumlah upload sesuai limit
* token valid
* event aktif
* resident aktif

Jangan percaya extension file saja.

---

# 31. ERROR HANDLING

Semua error harus menggunakan bahasa sederhana.

Contoh:

Jangan:

```text
SQLSTATE[23000]
```

kepada warga.

Gunakan:

```text
Maaf, foto tidak dapat dikirim.
Silakan coba lagi.
```

Untuk admin boleh tampilkan informasi error yang lebih detail melalui log.

---

# 32. LOGGING

Implementasikan logging untuk:

* admin login
* event dibuat
* event diubah
* event dihapus
* resident dibuat
* resident diubah
* token dibuat ulang
* photo upload
* photo delete

Jangan menyimpan password dalam log.

---

# 33. SEO DAN PERFORMANCE

Website harus:

* cepat
* image lazy loading
* thumbnail digunakan pada list
* full image hanya ketika dibuka
* pagination jika diperlukan
* caching untuk halaman publik
* database indexing
* compressed assets

Jangan load semua foto full-resolution di beranda.

---

# 34. ACCESSIBILITY

Gunakan:

* semantic HTML
* alt text
* tombol dengan ukuran cukup besar
* contrast yang baik
* keyboard navigation untuk admin
* label form yang jelas

---

# 35. PROJECT STRUCTURE

Buat struktur project yang bersih dan maintainable.

Pisahkan:

* controllers
* models
* services
* repositories jika memang diperlukan
* requests/validation
* policies
* jobs
* storage/image services
* components
* views
* routes

Jangan menaruh seluruh business logic di controller.

Contoh:

```text
EventService
PhotoService
ImageProcessingService
UploadTokenService
ResidentService
```

---

# 36. ASYNCHRONOUS IMAGE PROCESSING

Jika memungkinkan, gunakan queue untuk:

* resize
* compress
* generate thumbnail

Tetapi UX upload harus tetap memberikan feedback yang jelas.

Jika queue digunakan, tampilkan status:

```text
Foto sedang diproses...
```

Kemudian:

```text
Foto berhasil diproses.
```

---

# 37. SEEDER

Buat database seeder untuk data awal warga.

Masukkan:

A1:

01–25

A2:

01–30

A3:

01–25

A4:

01–30

Untuk Kavling buat beberapa contoh data kepala keluarga.

Gunakan data dummy yang jelas sebagai contoh.

Admin dummy juga harus dibuat melalui seeder.

Jangan hardcode password production.

---

# 38. TESTING

Buat automated test untuk business rules penting.

Minimal test:

### Resident

* tambah warga
* edit warga
* nonaktifkan warga

### Event

* create event
* create activity
* activity dengan upload warga
* activity tanpa upload warga

### Upload

* warga dapat upload 1 foto
* warga tidak dapat upload foto kedua
* warga dapat mengganti foto
* admin maksimal 10 foto
* upload portrait ditolak
* file bukan image ditolak
* token invalid ditolak
* event archived menolak upload

### Security

* warga tidak dapat mengakses admin
* token tidak dapat digunakan untuk rumah lain
* token invalid ditolak
* unauthorized request ditolak

---

# 39. API / ROUTING

Jika menggunakan server-rendered Laravel, tidak perlu membuat API untuk semua hal.

Gunakan route sederhana.

Contoh konsep:

```text
/
```

Beranda.

```text
/events
```

Daftar event.

```text
/events/{event}
```

Detail event.

```text
/upload/{token}
```

Upload warga.

Admin:

```text
/admin
/admin/events
/admin/events/create
/admin/residents
/admin/photos
```

Jangan expose database ID sensitif jika tidak diperlukan.

Gunakan UUID/ULID untuk resource publik jika sesuai.

---

# 40. ADMIN UI

Dashboard admin harus berbeda dengan UI warga.

Admin boleh memiliki:

* sidebar
* tabel
* filter
* statistik
* modal
* pagination

Tetapi tetap sederhana dan responsive.

---

# 41. PUBLIC UI

Jangan membuat warga merasa sedang menggunakan sistem administrasi.

Website publik harus terasa seperti:

**"Album Foto Perumahan."**

Bukan:

**"Enterprise Management System."**

Prioritaskan foto dan kegiatan.

---

# 42. HALAMAN YANG HARUS DIBUAT

Minimal:

### Public

1. Beranda
2. Daftar acara/kegiatan
3. Detail acara/kegiatan
4. Upload foto warga
5. Success upload
6. Error/invalid token

### Admin

1. Login
2. Dashboard
3. Data warga
4. Tambah warga
5. Edit warga
6. QR warga
7. Daftar event
8. Buat event
9. Edit event
10. Detail event
11. Kelola foto
12. Kelola thumbnail
13. Profile/password admin

---

# 43. DESIGN SYSTEM

Gunakan design system sederhana.

Karakter desain:

* bersih
* ramah
* modern
* tidak terlalu corporate
* tidak terlalu colorful
* mudah dibaca lansia

Gunakan card dan tombol besar.

Pastikan ukuran touch target nyaman di smartphone.

---

# 44. IMPLEMENTATION STRATEGY

Jangan langsung membuat seluruh aplikasi sekaligus.

Kerjakan secara bertahap:

## PHASE 1

Setup project:

* Laravel
* database
* authentication admin
* Tailwind
* base layout

## PHASE 2

Resident management:

* residents
* block
* house
* family head
* token

## PHASE 3

Event management:

* event
* activity
* publish
* archive

## PHASE 4

Resident upload:

* token
* upload
* validation
* 1 photo limit
* replace photo

## PHASE 5

Admin photo:

* max 10
* upload
* delete
* gallery

## PHASE 6

Image processing:

* compression
* resize
* WebP/JPEG
* thumbnail

## PHASE 7

QR:

* generate QR
* print/download QR

## PHASE 8

Public website:

* homepage
* event history
* gallery

## PHASE 9

Testing:

* business rules
* security
* upload
* responsive

## PHASE 10

Production optimization.

---

# 45. IMPORTANT DEVELOPMENT RULES

Jangan:

* membuat fitur yang tidak diminta
* membuat login warga
* membuat registrasi warga
* membuat sistem sosial media
* membuat komentar
* membuat like
* membuat chat
* membuat profile warga publik
* membuat UI terlalu rumit
* menyimpan foto di database
* mempercayai input client
* hanya melakukan validasi di frontend

Selalu lakukan validasi di backend.

Gunakan prinsip:

```text
Simple for residents.
Powerful for administrators.
Secure by default.
```

---

# 46. OUTPUT YANG DIHARAPKAN

Bangun aplikasi secara nyata, bukan hanya mockup.

Hasil akhir harus mencakup:

1. Source code
2. Database migration
3. Seeder
4. Model
5. Controller
6. Service
7. Validation
8. Authorization
9. Authentication
10. Image processing
11. QR generation
12. Responsive UI
13. Automated tests
14. README
15. `.env.example`
16. Installation instructions
17. Deployment instructions

---

# 47. CARA BEKERJA

Sebelum coding:

1. Analisis requirement.
2. Identifikasi potensi konflik requirement.
3. Buat ERD.
4. Buat database schema.
5. Buat application architecture.
6. Buat route map.
7. Buat user flow.
8. Buat admin flow.
9. Jelaskan keputusan teknis penting.

Setelah itu implementasikan aplikasi secara bertahap.

Jangan hanya menghasilkan kode pseudo-code.

Jika terdapat requirement yang ambigu, pilih solusi yang paling sederhana dan aman berdasarkan tujuan utama aplikasi.

Prioritas keputusan:

```text
UX warga
>
Business Rules
>
Security
>
Performance
>
Maintainability
>
Additional Features
```

Jangan menambahkan fitur tambahan sebelum seluruh requirement utama selesai.

---

# 48. DEFINITION OF DONE

Project dianggap selesai jika:

* warga dapat membuka website tanpa login
* warga dapat melihat dokumentasi
* warga dapat upload melalui token/QR
* satu rumah hanya dapat memiliki satu foto per event
* warga dapat mengganti foto
* admin dapat membuat acara
* admin dapat membuat kegiatan
* admin dapat menentukan apakah warga boleh upload
* admin maksimal 10 foto
* warga maksimal 1 foto
* admin dapat mengelola warga
* admin dapat menambah warga
* admin dapat membuat QR
* foto otomatis dikompres
* foto landscape
* thumbnail dapat dibuat dan diedit
* event dapat ditampilkan di beranda
* sistem responsive
* database memiliki constraint yang benar
* backend melakukan seluruh validasi penting
* automated test untuk business rule utama tersedia
* tidak ada password atau secret yang hardcoded
