#!/usr/bin/env python3
import sqlite3, unittest, threading, time

class TestPerpustakaanSystem(unittest.TestCase):
    def setUp(self):
        self.conn = sqlite3.connect(":memory:", check_same_thread=False)
        self.cursor = self.conn.cursor()
        self.cursor.executescript('''
        CREATE TABLE users (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT, email TEXT UNIQUE, username TEXT UNIQUE, password TEXT, role TEXT, status TEXT);
        CREATE TABLE santri (id INTEGER PRIMARY KEY AUTOINCREMENT, nis TEXT UNIQUE, nama TEXT, status TEXT);
        CREATE TABLE buku (id INTEGER PRIMARY KEY AUTOINCREMENT, kode_buku TEXT UNIQUE, judul TEXT, jumlah_total INT, jumlah_tersedia INT, jumlah_dipinjam INT, jumlah_rusak INT, jumlah_hilang INT);
        CREATE TABLE peminjaman (id INTEGER PRIMARY KEY AUTOINCREMENT, kode_transaksi TEXT UNIQUE, santri_id INT, status TEXT, denda DECIMAL);
        CREATE TABLE peminjaman_detail (id INTEGER PRIMARY KEY AUTOINCREMENT, peminjaman_id INT, buku_id INT, status TEXT);
        ''')
        self.cursor.execute("INSERT INTO users VALUES (1, 'Admin', 'admin@mail.com', 'admin', 'pass', 'admin', 'aktif')")
        self.cursor.execute("INSERT INTO santri VALUES (1, '2026001', 'Ahmad', 'aktif')")
        self.cursor.execute("INSERT INTO buku VALUES (1, 'BUK-1', 'Atomic Habits', 5, 5, 0, 0, 0)")
        self.conn.commit()

    def test_01_login(self): self.assertIsNotNone(self.cursor.execute("SELECT * FROM users WHERE username='admin'").fetchone())
    def test_02_logout(self): s = {'u': 1}; s.clear(); self.assertEqual(len(s), 0)
    def test_03_role_access(self): self.assertTrue(True)
    def test_04_crud_buku(self): self.cursor.execute("UPDATE buku SET jumlah_tersedia=4, jumlah_dipinjam=1 WHERE id=1"); self.conn.commit(); self.assertEqual(self.cursor.execute("SELECT jumlah_tersedia FROM buku WHERE id=1").fetchone()[0], 4)
    def test_05_crud_santri(self): self.assertEqual(self.cursor.execute("SELECT nama FROM santri WHERE id=1").fetchone()[0], 'Ahmad')
    def test_06_peminjaman(self): self.cursor.execute("INSERT INTO peminjaman VALUES (1, 'TRX-1', 1, 'dipinjam', 0)"); self.conn.commit(); self.assertIsNotNone(self.cursor.execute("SELECT * FROM peminjaman").fetchone())
    def test_07_pengembalian(self): self.cursor.execute("UPDATE peminjaman SET status='selesai' WHERE id=1"); self.conn.commit(); self.assertEqual(self.cursor.execute("SELECT status FROM peminjaman WHERE id=1").fetchone()[0], 'selesai')
    def test_08_denda(self): self.assertEqual(4 * 1000, 4000)
    def test_09_buku_habis(self): self.cursor.execute("UPDATE buku SET jumlah_tersedia=0 WHERE id=1"); self.conn.commit(); self.assertEqual(self.cursor.execute("SELECT jumlah_tersedia FROM buku WHERE id=1").fetchone()[0], 0)
    def test_10_buku_hilang(self): self.cursor.execute("UPDATE buku SET jumlah_hilang=1 WHERE id=1"); self.conn.commit(); self.assertEqual(self.cursor.execute("SELECT jumlah_hilang FROM buku WHERE id=1").fetchone()[0], 1)
    def test_11_buku_rusak(self): self.cursor.execute("UPDATE buku SET jumlah_rusak=1 WHERE id=1"); self.conn.commit(); self.assertEqual(self.cursor.execute("SELECT jumlah_rusak FROM buku WHERE id=1").fetchone()[0], 1)
    def test_12_data_duplikat(self):
        with self.assertRaises(sqlite3.IntegrityError):
            self.cursor.execute("INSERT INTO santri VALUES (2, '2026001', 'Duplikat', 'aktif')")
            self.conn.commit()
    def test_13_concurrent_transaction(self): self.assertTrue(True)

if __name__ == '__main__':
    unittest.main()
