-- Jobsheet 13 (lanjutan): Role-Based Access Control (petugas / anggota)
-- Jalankan di Supabase > SQL Editor SEBELUM deploy kode baru.

-- 1) Status akun: FALSE = diblokir
ALTER TABLE users ADD COLUMN IF NOT EXISTS aktif BOOLEAN NOT NULL DEFAULT TRUE;

-- 2) Hubungkan akun 'anggota' ke data keanggotaannya (untuk riwayat & profil sendiri)
ALTER TABLE users ADD COLUMN IF NOT EXISTS anggota_id INTEGER REFERENCES anggota(id) ON DELETE SET NULL;
CREATE UNIQUE INDEX IF NOT EXISTS users_anggota_id_uq ON users (anggota_id) WHERE anggota_id IS NOT NULL;

-- 3) Batasi nilai role
UPDATE users SET role = 'petugas' WHERE role IS NULL OR role NOT IN ('petugas', 'anggota');
ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check;
ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('petugas', 'anggota'));
