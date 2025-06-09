// Sidebar toggle
const sidebar = document.getElementById("sidebar");
const toggleSidebarBtn = document.getElementById("toggleSidebar");
const mainContent = document.querySelector(".main-content");

let sidebarCollapsed = false;
toggleSidebarBtn.addEventListener("click", () => {
  sidebarCollapsed = !sidebarCollapsed;
  sidebar.classList.toggle("collapsed", sidebarCollapsed);
  sidebar.classList.toggle("expanded", !sidebarCollapsed);
  updateSidebarLogo();
});

// Tabs
const navLinks = document.querySelectorAll("#sidebarLinks .nav-link");
navLinks.forEach((link) => {
  link.addEventListener("click", function (e) {
    e.preventDefault();
    navLinks.forEach((l) => l.classList.remove("active"));
    this.classList.add("active");
    const tabId = this.dataset.tab;
    document
      .querySelectorAll(".tab-content-section")
      .forEach((tab) => (tab.style.display = "none"));
    document.getElementById("tab-" + tabId).style.display = "block";

    if(tabId === "reports") renderActivityChart();
  });
});

// Theme switch (dark/light)
const htmlEl = document.documentElement;
const themeBtn = document.getElementById("themeBtn");
themeBtn.addEventListener("click", () => {
  if (htmlEl.getAttribute("data-bs-theme") === "dark") {
    htmlEl.setAttribute("data-bs-theme", "light");
    themeBtn.innerHTML = '<i class="bi bi-sun"></i>';
    updateSidebarLogo();
  } else {
    htmlEl.setAttribute("data-bs-theme", "dark");
    themeBtn.innerHTML = '<i class="bi bi-moon"></i>';
    updateSidebarLogo();
  }
});
if (htmlEl.getAttribute("data-bs-theme") === "dark") {
  themeBtn.innerHTML = '<i class="bi bi-moon"></i>';
} else {
  themeBtn.innerHTML = '<i class="bi bi-sun"></i>';
}

let currentLang = 'en';
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
}
document.querySelectorAll('.lang-option').forEach(opt => {
  opt.addEventListener('click', function(e) {
    e.preventDefault();
    updateLang(this.getAttribute('data-lang'));
  });
});
updateLang(currentLang);

function updateSidebarLogo() {
  const isDark = htmlEl.getAttribute("data-bs-theme") === "dark";
  const isCollapsed = sidebar.classList.contains("collapsed");
  const logo = document.getElementById("sidebarLogo");
  if (isDark && !isCollapsed) logo.src = "img/RHEONICS WHITE LOGO.png";
  if (isDark && isCollapsed) logo.src = "img/RHEONICS WHITE LOGO ICON.png";
  if (!isDark && !isCollapsed) logo.src = "img/RHEONICS LOGO.png";
  if (!isDark && isCollapsed) logo.src = "img/RHEONICS LOGO ICON.png";
}
updateSidebarLogo();

document.querySelectorAll('.see-more').forEach(btn => {
  btn.addEventListener('click', function(e) {
    e.preventDefault();
    const target = this.getAttribute('data-target');
    document.querySelectorAll('#sidebarLinks .nav-link').forEach(link => {
      if(link.dataset.tab === target){
        link.click();
      }
    });
  });
});

// -------- CHART.JS CONFIG (Reports tab) --------
let activityChartInstance = null;
const chartDataSets = {
  week: {
    labels: ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'],
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
    labels: Array.from({length: 30}, (_,i)=> (i+1).toString()),
    datasets: [
      {
        label: 'Accesses',
        data: Array.from({length: 30}, () => Math.floor(Math.random()*20+10)),
        borderWidth: 2,
        borderColor: '#00ADB5',
        backgroundColor: 'rgba(0,173,181,0.13)',
        tension: 0.3
      },
      {
        label: 'Errors',
        data: Array.from({length: 30}, () => Math.floor(Math.random()*5)),
        borderWidth: 2,
        borderColor: '#e53935',
        backgroundColor: 'rgba(229,57,53,0.08)',
        tension: 0.3
      }
    ]
  },
  year: {
    labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
    datasets: [
      {
        label: 'Accesses',
        data: [420,410,460,475,500,480,470,490,510,495,505,520],
        borderWidth: 2,
        borderColor: '#00ADB5',
        backgroundColor: 'rgba(0,173,181,0.13)',
        tension: 0.25
      },
      {
        label: 'Errors',
        data: [10,12,9,15,11,10,13,9,10,8,14,11],
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

document.addEventListener('DOMContentLoaded', function() {
  const chartRangeSelect = document.getElementById('chartRangeSelect');
  if(chartRangeSelect){
    chartRangeSelect.addEventListener('change', function(){
      renderActivityChart(this.value);
    });
  }
});

document.querySelector('a[data-tab="reports"]').addEventListener('click', function() {
  const chartRangeSelect = document.getElementById('chartRangeSelect');
  renderActivityChart(chartRangeSelect ? chartRangeSelect.value : "week");
});

if(document.querySelector('#tab-reports').style.display !== "none") {
  const chartRangeSelect = document.getElementById('chartRangeSelect');
  renderActivityChart(chartRangeSelect ? chartRangeSelect.value : "week");
}

const settingsThemeBtn = document.getElementById('settingsThemeBtn');
if(settingsThemeBtn){
  settingsThemeBtn.addEventListener('click', function(){
    themeBtn.click(); 
  });
}
const themeDarkRadio = document.getElementById('themeDarkRadio');
const themeLightRadio = document.getElementById('themeLightRadio');


function syncThemeSelector() {
  if (htmlEl.getAttribute("data-bs-theme") === "dark") {
    themeDarkRadio.checked = true;
  } else {
    themeLightRadio.checked = true;
  }
}
syncThemeSelector();

themeDarkRadio.addEventListener('change', () => {
  if (themeDarkRadio.checked) {
    htmlEl.setAttribute("data-bs-theme", "dark");
    themeBtn.innerHTML = '<i class="bi bi-moon"></i>';
    updateSidebarLogo();
    syncThemeSelector();
  }
});
themeLightRadio.addEventListener('change', () => {
  if (themeLightRadio.checked) {
    htmlEl.setAttribute("data-bs-theme", "light");
    themeBtn.innerHTML = '<i class="bi bi-sun"></i>';
    updateSidebarLogo();
    syncThemeSelector();
  }
});

themeBtn.addEventListener("click", syncThemeSelector);

const LANGUAGES = {
  en: "English",
  es: "Español"
};

function syncLangRadios() {

  document.querySelectorAll('#langDropdownMenu input[type="radio"]').forEach(radio => {
    radio.checked = (radio.value === currentLang);
  });
  document.getElementById('currentLangName').textContent = LANGUAGES[currentLang];

  document.querySelectorAll('#langSelectorSettings input[type="radio"]').forEach(radio => {
    radio.checked = (radio.value === currentLang);
  });
}


document.querySelectorAll('#langDropdownMenu input[type="radio"]').forEach(radio => {
  radio.addEventListener('change', function() {
    updateLang(this.value);
    syncLangRadios();
  });
});

document.querySelectorAll('#langSelectorSettings input[type="radio"]').forEach(radio => {
  radio.addEventListener('change', function() {
    updateLang(this.value);
    syncLangRadios();
  });
});

// Inicializar idioma visual en header/settings
syncLangRadios();


function updateCurrentDateTime() {
  const now = new Date();
  const day   = String(now.getDate()).padStart(2, '0');
  const month = String(now.getMonth() + 1).padStart(2, '0');
  const year  = now.getFullYear();
  const hour  = String(now.getHours()).padStart(2, '0');
  const min   = String(now.getMinutes()).padStart(2, '0');
  const formatted = `${day}/${month}/${year} ${hour}:${min}`;
  document.getElementById('currentDateTime').innerHTML = formatted + ' <i class="bi bi-calendar-date"></i>';
}
updateCurrentDateTime();
setInterval(updateCurrentDateTime, 60000);
