<div class="container-fluid p-4 pb-6">
  <h4 data-i18n="settingsTitle">Settings</h4>
  <div class="card">
    <div class="card-body">
      <div class="mb-3">
        <label class="form-label" data-i18n="profile">Profile</label>
        <div>
          <span class="me-2"><i class="bi bi-person-circle"></i> Manager</span>
          <span class="text-muted small ms-2">manager@rheonics.com</span>
          <button class="btn btn-link btn-sm ms-2" data-i18n="editProfile">Edit profile</button>
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label" data-i18n="theme">Theme</label>
        <div class="d-flex align-items-center gap-4 mt-2" id="themeSelector">
          <label class="theme-option">
            <input type="radio" name="theme" value="dark" id="themeDarkRadio">
            <div class="theme-visual dark">
              <div class="theme-sidebar"></div>
              <div class="theme-main"></div>
            </div>
            <div class="theme-label" data-i18n="dark">Dark</div>
          </label>
          <label class="theme-option">
            <input type="radio" name="theme" value="light" id="themeLightRadio">
            <div class="theme-visual light">
              <div class="theme-sidebar"></div>
              <div class="theme-main"></div>
            </div>
            <div class="theme-label" data-i18n="light">Light</div>
          </label>
        </div>
      </div>
      <div>
        <label class="form-label" data-i18n="language">Language</label>
        <div class="d-flex flex-wrap gap-2 mt-2" id="langSelectorSettings">
          <label class="lang-option-radio">
            <input type="radio" name="settingsLang" value="en">
            English
          </label>
          <label class="lang-option-radio">
            <input type="radio" name="settingsLang" value="es">
            Español
          </label>
        </div>
      </div>
    </div>
  </div>
</div>
