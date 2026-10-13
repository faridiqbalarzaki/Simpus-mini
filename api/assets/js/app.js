// ===== Tema terang / gelap =====
// Default mengikuti pengaturan sistem. Pilihan pengguna disimpan di
// localStorage dan dipasang sebagai atribut data-theme pada <html>.
// Dipanggil langsung (bukan menunggu DOMContentLoaded) supaya tidak berkedip.
const THEME_KEY = "simpus-theme";

function ambilTemaTersimpan() {
  try {
    return localStorage.getItem(THEME_KEY);
  } catch (err) {
    return null;
  }
}

function pasangTema(tema) {
  const root = document.documentElement;
  if (tema === "light" || tema === "dark") {
    root.setAttribute("data-theme", tema);
  } else {
    root.removeAttribute("data-theme");
  }
}

function temaAktif() {
  const manual = document.documentElement.getAttribute("data-theme");
  if (manual) return manual;
  return window.matchMedia("(prefers-color-scheme: dark)").matches
    ? "dark"
    : "light";
}

pasangTema(ambilTemaTersimpan());

function initThemeToggle() {
  const header = document.querySelector("header");
  if (!header) return;

  const btn = document.createElement("button");
  btn.type = "button";
  btn.className = "theme-toggle";

  const ikon = document.createElement("span");
  ikon.className = "theme-icon";
  ikon.setAttribute("aria-hidden", "true");
  const label = document.createElement("span");
  label.className = "theme-label";
  btn.append(ikon, label);

  function perbarui() {
    const gelap = temaAktif() === "dark";
    ikon.textContent = gelap ? "\u2600" : "\u263E";
    label.textContent = gelap ? "Mode terang" : "Mode gelap";
    btn.setAttribute(
      "aria-label",
      gelap ? "Beralih ke mode terang" : "Beralih ke mode gelap",
    );
  }

  btn.addEventListener("click", function () {
    const berikut = temaAktif() === "dark" ? "light" : "dark";
    pasangTema(berikut);
    try {
      localStorage.setItem(THEME_KEY, berikut);
    } catch (err) {
      // penyimpanan diblokir: tema tetap berlaku untuk halaman ini saja
    }
    perbarui();
  });

  perbarui();

  const status = header.querySelector(".auth-status");
  if (status) {
    header.insertBefore(btn, status);
  } else {
    header.appendChild(btn);
  }
}

// ===== Hamburger menu (JS-driven, menggantikan checkbox hack) =====
function initNavToggle() {
  const toggleBtn = document.getElementById("nav-toggle-btn");
  const nav = document.querySelector("header nav");
  if (!toggleBtn || !nav) return;

  toggleBtn.setAttribute("aria-expanded", "false");

  function atur(buka) {
    nav.classList.toggle("nav-open", buka);
    toggleBtn.setAttribute("aria-expanded", String(buka));
  }

  toggleBtn.addEventListener("click", function () {
    atur(!nav.classList.contains("nav-open"));
  });

  // Tutup menu setelah memilih halaman atau menekan Escape
  nav.addEventListener("click", function (e) {
    if (e.target.closest("a")) atur(false);
  });
  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") atur(false);
  });
  // Kembali ke tata letak desktop: pastikan menu tidak tertinggal terbuka
  window
    .matchMedia("(min-width: 961px)")
    .addEventListener("change", function (e) {
      if (e.matches) atur(false);
    });
}

// ===== Konfirmasi hapus =====
// Tombol Hapus kini berada di dalam <form class="form-hapus" method="post">
// yang benar-benar mengirim request DELETE ke server (buku/hapus.php,
// anggota/hapus.php). Konfirmasi dilakukan pada event "submit" agar bisa
// dibatalkan (preventDefault) sebelum request terkirim.
function initHapusConfirm() {
  document.addEventListener("submit", function (e) {
    const form = e.target;
    if (!form.classList.contains("form-hapus")) return;

    const row = form.closest("tr");
    const nama = row ? row.querySelector("td")?.textContent : "data ini";
    const yakin = confirm('Yakin ingin menghapus "' + nama + '"?');
    if (!yakin) {
      e.preventDefault();
    }
  });
}

// ===== Filter/pencarian tabel real-time =====
function initTableFilter() {
  const input = document.getElementById("search-input");
  const table = document.querySelector(".table-responsive table");
  if (!input || !table) return;

  input.addEventListener("input", function () {
    const keyword = input.value.toLowerCase();
    const rows = table.querySelectorAll("tbody tr");
    rows.forEach(function (row) {
      const teks = row.textContent.toLowerCase();
      row.style.display = teks.includes(keyword) ? "" : "none";
    });
  });
}

// ===== Validasi form (client-side) =====
function tampilkanError(input, pesan) {
  hapusError(input);
  const span = document.createElement("span");
  span.className = "error";
  span.textContent = pesan;
  input.insertAdjacentElement("afterend", span);
}

function hapusError(input) {
  const next = input.nextElementSibling;
  if (next && next.classList.contains("error")) {
    next.remove();
  }
}

function initValidasiForm() {
  const form = document.getElementById("form-tambah");
  if (!form) return;

  const tahunIni = new Date().getFullYear();

  form.addEventListener("submit", function (e) {
    let valid = true;

    const judul = form.querySelector("[name='judul'], [name='nama']");
    if (judul && judul.value.trim() === "") {
      tampilkanError(judul, "Field ini wajib diisi.");
      valid = false;
    } else if (judul) {
      hapusError(judul);
    }

    const pengarang = form.querySelector("[name='pengarang']");
    if (pengarang && pengarang.value.trim() === "") {
      tampilkanError(pengarang, "Pengarang wajib diisi.");
      valid = false;
    } else if (pengarang) {
      hapusError(pengarang);
    }

    const tahun = form.querySelector("[name='tahun']");
    if (tahun) {
      const nilai = parseInt(tahun.value, 10);
      if (isNaN(nilai) || nilai < 1900 || nilai > tahunIni) {
        tampilkanError(tahun, "Tahun harus di antara 1900-" + tahunIni + ".");
        valid = false;
      } else {
        hapusError(tahun);
      }
    }

    const stok = form.querySelector("[name='stok']");
    if (stok) {
      const nilai = parseInt(stok.value, 10);
      if (isNaN(nilai) || nilai < 0) {
        tampilkanError(stok, "Stok tidak boleh negatif.");
        valid = false;
      } else {
        hapusError(stok);
      }
    }

    if (!valid) {
      e.preventDefault();
    }
  });
}

document.addEventListener("DOMContentLoaded", function () {
  initThemeToggle();
  initNavToggle();
  initHapusConfirm();
  initTableFilter();
  initValidasiForm();
});
