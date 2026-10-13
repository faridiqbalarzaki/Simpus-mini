-- Jobsheet 12 & 13: tabel peminjaman, menghubungkan buku, anggota, dan users
-- Jalankan: psql -d simpus_mini -f sql/03_peminjaman.sql

CREATE TABLE IF NOT EXISTS peminjaman (
    id SERIAL PRIMARY KEY,
    buku_id INTEGER NOT NULL REFERENCES buku(id),
    anggota_id INTEGER NOT NULL REFERENCES anggota(id),
    tanggal_pinjam DATE NOT NULL DEFAULT CURRENT_DATE,
    tanggal_jatuh_tempo DATE NOT NULL DEFAULT (CURRENT_DATE + 14),
    tanggal_kembali DATE,
    status VARCHAR(20) NOT NULL DEFAULT 'dipinjam'
);

-- Pastikan kolom tanggal_jatuh_tempo ada jika tabel sudah dibuat sebelumnya
ALTER TABLE peminjaman ADD COLUMN IF NOT EXISTS tanggal_jatuh_tempo DATE DEFAULT (CURRENT_DATE + 14);
