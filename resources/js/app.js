import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

/* ============================================================
 * Tur interaktif (onboarding) — driver.js
 * window.KostTour.start(steps)     -> jalankan tur langsung (tombol "Panduan")
 * window.KostTour.auto(key, steps) -> jalankan sekali untuk user baru (localStorage)
 * ============================================================ */
import { driver } from 'driver.js';
import 'driver.js/dist/driver.css';

function buildDriver() {
    return driver({
        showProgress: true,
        allowClose: true,
        overlayColor: 'rgba(6, 78, 59, 0.55)',
        nextBtnText: 'Lanjut →',
        prevBtnText: '← Kembali',
        doneBtnText: 'Selesai',
        progressText: '{{current}} / {{total}}',
        popoverClass: 'kostcloud-tour',
    });
}

window.KostTour = {
    start(steps) {
        if (!Array.isArray(steps) || steps.length === 0) return;
        // Buang step yang elemennya tidak ada di halaman agar tur tidak rusak.
        const valid = steps.filter((s) => !s.element || document.querySelector(s.element));
        if (valid.length === 0) return;
        const d = buildDriver();
        d.setSteps(valid);
        d.drive();
    },

    auto(key, steps) {
        // Halaman dibuka untuk aksi cepat dari checklist onboarding (?add=1):
        // jangan jalankan tur (biar tak bentrok dengan modal), dan JANGAN tandai seen.
        try { if (new URLSearchParams(window.location.search).get('add')) return; } catch (e) {}

        // Kunci status tur PER-USER (bukan per-browser) agar owner/admin BARU
        // selalu mendapat tur meski akun lain pernah melihatnya di browser sama.
        const meta = document.querySelector('meta[name="user-id"]');
        const uid = (meta && meta.getAttribute('content')) || '0';
        const storageKey = 'tour_done_' + uid + '_' + key;

        let done = false;
        try {
            done = !!localStorage.getItem(storageKey);
            localStorage.setItem(storageKey, '1');
        } catch (e) {
            return; // localStorage tak tersedia → lewati auto-tour
        }
        if (done) return;
        setTimeout(() => this.start(steps), 700);
    },
};

/* Helper modal pop-up sederhana (toggle invisible/opacity). */
window.openModal = (id) => {
    const m = document.getElementById(id);
    if (m) {
        m.classList.remove('invisible', 'opacity-0');
        document.body.classList.add('overflow-hidden');
    }
};
window.closeModal = (id) => {
    const m = document.getElementById(id);
    if (m) {
        m.classList.add('invisible', 'opacity-0');
        document.body.classList.remove('overflow-hidden');
    }
};

/* ============================================================
 * Auto-kompres gambar sebelum upload (klien-side).
 * Gambar yang melebihi batas otomatis diperkecil dimensi & kualitasnya
 * (canvas → JPEG) sehingga pengguna TAK perlu kompres foto manual.
 * Aktif pada <input type="file" data-auto-compress>. Non-gambar (PDF) & GIF dilewati.
 * ============================================================ */
const AUTO_COMPRESS_MAX_BYTES = 1.8 * 1024 * 1024; // target < ~1.8MB (di bawah batas server 2MB)
const AUTO_COMPRESS_MAX_DIM = 1920;                // sisi terpanjang maksimum (px)

async function compressImageFile(file) {
    if (!file || !file.type || !file.type.startsWith('image/')) return file; // lewati PDF dll
    if (file.type === 'image/gif') return file;             // GIF animasi: jangan diproses
    if (file.size <= AUTO_COMPRESS_MAX_BYTES) return file;  // sudah cukup kecil

    try {
        const dataUrl = await new Promise((res, rej) => {
            const r = new FileReader();
            r.onload = () => res(r.result);
            r.onerror = rej;
            r.readAsDataURL(file);
        });
        const img = await new Promise((res, rej) => {
            const i = new Image();
            i.onload = () => res(i);
            i.onerror = rej;
            i.src = dataUrl;
        });

        let w = img.naturalWidth || img.width;
        let h = img.naturalHeight || img.height;
        if (w > AUTO_COMPRESS_MAX_DIM || h > AUTO_COMPRESS_MAX_DIM) {
            const scale = Math.min(AUTO_COMPRESS_MAX_DIM / w, AUTO_COMPRESS_MAX_DIM / h);
            w = Math.round(w * scale);
            h = Math.round(h * scale);
        }

        const draw = (dw, dh) => {
            const c = document.createElement('canvas');
            c.width = dw;
            c.height = dh;
            c.getContext('2d').drawImage(img, 0, 0, dw, dh);
            return c;
        };
        const toBlob = (c, q) => new Promise((r) => c.toBlob(r, 'image/jpeg', q));

        let canvas = draw(w, h);
        let quality = 0.9;
        let blob = await toBlob(canvas, quality);
        while (blob && blob.size > AUTO_COMPRESS_MAX_BYTES && quality > 0.4) {
            quality -= 0.1;
            blob = await toBlob(canvas, quality);
        }
        if (blob && blob.size > AUTO_COMPRESS_MAX_BYTES) {
            // Masih besar → perkecil dimensi sekali lagi.
            canvas = draw(Math.round(w * 0.7), Math.round(h * 0.7));
            blob = await toBlob(canvas, 0.7);
        }
        if (!blob || blob.size >= file.size) return file; // gagal / tak menguntungkan → pakai asli

        const name = file.name.replace(/\.[^.]+$/, '') + '.jpg';
        return new File([blob], name, { type: 'image/jpeg', lastModified: Date.now() });
    } catch (e) {
        console.warn('Auto-kompres gagal, memakai file asli:', e);
        return file;
    }
}

function attachAutoCompress(input) {
    if (input.dataset.autoCompressBound) return;
    input.dataset.autoCompressBound = '1';
    input.addEventListener('change', async () => {
        if (!input.files || input.files.length === 0) return;
        const form = input.closest('form');
        const submitBtns = form ? form.querySelectorAll('[type="submit"]') : [];
        submitBtns.forEach((b) => (b.disabled = true)); // cegah submit saat kompres berjalan
        try {
            const dt = new DataTransfer();
            for (const f of Array.from(input.files)) {
                dt.items.add(await compressImageFile(f));
            }
            input.files = dt.files;
        } finally {
            submitBtns.forEach((b) => (b.disabled = false));
        }
    });
}

window.initAutoCompress = (root = document) => {
    root.querySelectorAll('input[type="file"][data-auto-compress]').forEach(attachAutoCompress);
};
document.addEventListener('DOMContentLoaded', () => window.initAutoCompress());
