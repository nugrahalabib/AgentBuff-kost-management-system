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
