// ---------------------- SIDEBAR TOGGLE ----------------------
const sidebar = document.getElementById("sidebar");
const toggleSidebarBtn = document.getElementById("toggleSidebar");

let sidebarCollapsed = false;
toggleSidebarBtn.addEventListener("click", () => {
  sidebarCollapsed = !sidebarCollapsed;
  sidebar.classList.toggle("collapsed", sidebarCollapsed);
  sidebar.classList.toggle("expanded", !sidebarCollapsed);
  updateSidebarLogo();
});

// ---------------------- LOGO DINÁMICO -----------------------
function updateSidebarLogo() {
  const isDark = document.documentElement.getAttribute("data-bs-theme") === "dark";
  const isCollapsed = sidebar.classList.contains("collapsed");
  const logo = document.getElementById("sidebarLogo");
  if (isDark && !isCollapsed) logo.src = "img/RHEONICS WHITE LOGO.png";
  if (isDark && isCollapsed) logo.src = "img/RHEONICS WHITE LOGO ICON.png";
  if (!isDark && !isCollapsed) logo.src = "img/RHEONICS LOGO.png";
  if (!isDark && isCollapsed) logo.src = "img/RHEONICS LOGO ICON.png";
}
updateSidebarLogo();

// ------------------- CARGA DINÁMICA DE TABS -----------------
const mainTabContent = document.getElementById('mainTabContent');

function loadTab(tabName) {
  fetch(`views/${tabName}.php`)
    .then(res => res.text())
    .then(html => {
      mainTabContent.innerHTML = html;
      AOS.init();  // (Si no está inicializado al principio)
      AOS.refreshHard();
      // "See more" (en cada vista)
      document.querySelectorAll('.see-more').forEach(btn => {
        btn.addEventListener('click', function (e) {
          e.preventDefault();
          const target = this.getAttribute('data-target');
          document.querySelectorAll('#sidebarLinks .nav-link').forEach(link => {
            if (link.dataset.tab === target) link.click();
          });
        });
      });

      // Chart solo en reports
      if (tabName === 'reports') {
        setTimeout(() => {
          if (typeof renderActivityChart === 'function') renderActivityChart();
          const chartRangeSelect = document.getElementById('chartRangeSelect');
          if (chartRangeSelect) {
            chartRangeSelect.addEventListener('change', function () {
              renderActivityChart(this.value);
            });
          }
        }, 100);
      }

      // Visor 3D solo en upload
      if (tabName === 'upload') {
        load3DScripts().then(() => {
          const uploaderScript = document.createElement('script');
          uploaderScript.src = 'js/preview3d.js';
          uploaderScript.onerror = function (e) {
            console.error("Error loading uploader JS", e);
          };
          document.body.appendChild(uploaderScript);
        }).catch(err => {
          alert('Fallo cargando scripts 3D: ' + err);
          console.error('Fallo cargando scripts 3D:', err);
        });
      }

      // Reinicializa radios/settings en la vista Settings
      reSyncThemeRadio();
      reSyncLangRadio();
      // Inicializa traducción si hay elementos data-i18n
      if (document.querySelector('[data-i18n]')) updateLang(currentLang);
    });
}

// ------------ SOLO carga Three.js si hace falta --------------
function load3DScripts() {
  return new Promise((resolve, reject) => {
    if (window.THREE && window.THREE.OrbitControls) return resolve();
    const urls = [
      "https://cdn.jsdelivr.net/npm/three@0.143.0/build/three.min.js",
      "https://cdn.jsdelivr.net/npm/three@0.143.0/examples/js/controls/OrbitControls.js",
      "https://cdn.jsdelivr.net/npm/three@0.143.0/examples/js/loaders/STLLoader.js",
      "https://cdn.jsdelivr.net/npm/three@0.143.0/examples/js/loaders/OBJLoader.js",
      "https://cdn.jsdelivr.net/npm/three@0.143.0/examples/js/loaders/GLTFLoader.js"
    ];
    let loaded = 0;
    let failed = false;
    urls.forEach(src => {
      const s = document.createElement('script');
      s.src = src;
      s.onload = () => {
        loaded++;
        if (loaded === urls.length && !failed) resolve();
      };
      s.onerror = (e) => {
        if (!failed) {
          failed = true;
          reject(`Error cargando: ${src}`);
        }
      };
      document.body.appendChild(s);
    });
  });
}


// ------------------ NAVEGACIÓN DE TABS -----------------------
document.querySelectorAll("#sidebarLinks .nav-link").forEach(link => {
  link.addEventListener("click", function (e) {
    e.preventDefault();
    document.querySelectorAll("#sidebarLinks .nav-link").forEach(l => l.classList.remove("active"));
    this.classList.add("active");
    const tabName = this.getAttribute('data-tab');
    loadTab(tabName);
  });
});

// Tab inicial al cargar
window.addEventListener("DOMContentLoaded", () => {
  const firstTab = document.querySelector("#sidebarLinks .nav-link.active");
  loadTab(firstTab ? firstTab.getAttribute('data-tab') : 'dashboard_home');
});

// ------------------- CAMBIO DE TEMA --------------------------
const htmlEl = document.documentElement;
const themeBtn = document.getElementById("themeBtn");
themeBtn.addEventListener("click", () => {
  if (htmlEl.getAttribute("data-bs-theme") === "dark") {
    htmlEl.setAttribute("data-bs-theme", "light");
    themeBtn.innerHTML = '<i class="bi bi-sun"></i>';
  } else {
    htmlEl.setAttribute("data-bs-theme", "dark");
    themeBtn.innerHTML = '<i class="bi bi-moon"></i>';
  }
  updateSidebarLogo();
  reSyncThemeRadio();
});
if (htmlEl.getAttribute("data-bs-theme") === "dark") {
  themeBtn.innerHTML = '<i class="bi bi-moon"></i>';
} else {
  themeBtn.innerHTML = '<i class="bi bi-sun"></i>';
}

// ----- Radios en settings para tema (re-sync por AJAX) ------
function reSyncThemeRadio() {
  const themeDarkRadio = document.getElementById('themeDarkRadio');
  const themeLightRadio = document.getElementById('themeLightRadio');
  if (!themeDarkRadio || !themeLightRadio) return;
  if (htmlEl.getAttribute("data-bs-theme") === "dark") {
    themeDarkRadio.checked = true;
  } else {
    themeLightRadio.checked = true;
  }
  themeDarkRadio.onchange = () => {
    htmlEl.setAttribute("data-bs-theme", "dark");
    themeBtn.innerHTML = '<i class="bi bi-moon"></i>';
    updateSidebarLogo();
    reSyncThemeRadio();
  };
  themeLightRadio.onchange = () => {
    htmlEl.setAttribute("data-bs-theme", "light");
    themeBtn.innerHTML = '<i class="bi bi-sun"></i>';
    updateSidebarLogo();
    reSyncThemeRadio();
  };
}

// ------------- CAMBIO DE IDIOMA INTERNACIONALIZACIÓN ----------
let currentLang = 'en';
const LANGUAGES = { en: "English", es: "Español" };

async function updateLang(lang) {
  currentLang = lang;
  const response = await fetch(`locales/${lang}.json`);
  const translations = await response.json();
  document.querySelectorAll('[data-i18n]').forEach(el => {
    const key = el.getAttribute('data-i18n');
    if (translations[key]) {
      el.textContent = translations[key];
    }
  });
  reSyncLangRadio();
}
function reSyncLangRadio() {
  document.querySelectorAll('#langDropdownMenu input[type="radio"]').forEach(radio => {
    radio.checked = (radio.value === currentLang);
  });
  const langName = document.getElementById('currentLangName');
  if (langName) langName.textContent = LANGUAGES[currentLang];
  document.querySelectorAll('#langSelectorSettings input[type="radio"]').forEach(radio => {
    radio.checked = (radio.value === currentLang);
  });
}
// Soporta radio en header y en settings (por AJAX)
document.addEventListener('change', function (e) {
  if (e.target && e.target.name === 'lang') {
    updateLang(e.target.value);
  }
  if (e.target && e.target.name === 'settingsLang') {
    updateLang(e.target.value);
  }
});
window.addEventListener("DOMContentLoaded", () => {
  updateLang(currentLang);
});

// ---------------- FECHA/HORA HEADER --------------------------
function updateCurrentDateTime() {
  const now = new Date();
  const day = String(now.getDate()).padStart(2, '0');
  const month = String(now.getMonth() + 1).padStart(2, '0');
  const year = now.getFullYear();
  const hour = String(now.getHours()).padStart(2, '0');
  const min = String(now.getMinutes()).padStart(2, '0');
  const formatted = `${day}/${month}/${year} ${hour}:${min}`;
  document.getElementById('currentDateTime').innerHTML = formatted + ' <i class="bi bi-calendar-date"></i>';
}
updateCurrentDateTime();
setInterval(updateCurrentDateTime, 60000);

// ------------------- CHART.JS (REPORTS) ----------------------
let activityChartInstance = null;
const chartDataSets = {
  week: {
    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
    datasets: [
      {
        label: 'Accesses',
        data: [14, 19, 12, 17, 13, 8, 15],
        borderWidth: 2,
        borderColor: '#00ADB5',
        backgroundColor: 'rgba(0,173,181,0.15)',
        tension: 0.3
      },
      {
        label: 'Errors',
        data: [1, 2, 0, 3, 1, 1, 2],
        borderWidth: 2,
        borderColor: '#e53935',
        backgroundColor: 'rgba(229,57,53,0.08)',
        tension: 0.3
      }
    ]
  },
  month: {
    labels: Array.from({ length: 30 }, (_, i) => (i + 1).toString()),
    datasets: [
      {
        label: 'Accesses',
        data: Array.from({ length: 30 }, () => Math.floor(Math.random() * 20 + 10)),
        borderWidth: 2,
        borderColor: '#00ADB5',
        backgroundColor: 'rgba(0,173,181,0.13)',
        tension: 0.3
      },
      {
        label: 'Errors',
        data: Array.from({ length: 30 }, () => Math.floor(Math.random() * 5)),
        borderWidth: 2,
        borderColor: '#e53935',
        backgroundColor: 'rgba(229,57,53,0.08)',
        tension: 0.3
      }
    ]
  },
  year: {
    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
    datasets: [
      {
        label: 'Accesses',
        data: [420, 410, 460, 475, 500, 480, 470, 490, 510, 495, 505, 520],
        borderWidth: 2,
        borderColor: '#00ADB5',
        backgroundColor: 'rgba(0,173,181,0.13)',
        tension: 0.25
      },
      {
        label: 'Errors',
        data: [10, 12, 9, 15, 11, 10, 13, 9, 10, 8, 14, 11],
        borderWidth: 2,
        borderColor: '#e53935',
        backgroundColor: 'rgba(229,57,53,0.09)',
        tension: 0.25
      }
    ]
  }
};

function renderActivityChart(range = "week") {
  setTimeout(() => {
    const ctx = document.getElementById('activityChart');
    if (ctx) {
      if (activityChartInstance) activityChartInstance.destroy();
      activityChartInstance = new Chart(ctx, {
        type: 'line',
        data: chartDataSets[range],
        options: {
          plugins: {
            legend: {
              display: true,
              labels: { color: getComputedStyle(document.body).color }
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: { color: getComputedStyle(document.body).color }
            },
            x: {
              ticks: { color: getComputedStyle(document.body).color }
            }
          }
        }
      });
    }
  }, 100);
}
document.addEventListener('DOMContentLoaded', function () {
  let fileIdToDelete = null;
  document.querySelectorAll('.delete-file-btn').forEach(btn => {
    btn.addEventListener('click', function () {
      fileIdToDelete = this.getAttribute('data-file-id');
      document.getElementById('deleteFileName').textContent = this.getAttribute('data-file-name');
      new bootstrap.Modal(document.getElementById('confirmDeleteFileModal')).show();
    });
  });

  document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
    if (!fileIdToDelete) return;
    fetch('delete_file.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({id: fileIdToDelete})
    })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          location.reload();
        } else {
          alert(data.error || 'Error deleting file.');
        }
      });
  });
});