<div class="container-fluid p-4 pb-6">
  <h4 data-i18n="reportsTitle">Reports</h4>
  <div class="row g-3 mb-3">
    <div class="col-12 col-md-6">
      <div class="card h-100">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
          <span data-i18n="activityChart">Activity Chart</span>
          <select class="form-select form-select-sm w-auto ms-auto" id="chartRangeSelect" style="min-width:110px;">
            <option value="week" selected>Week</option>
            <option value="month">Month</option>
            <option value="year">Year</option>
          </select>
        </div>
        <div class="card-body">
          <canvas id="activityChart" height="160"></canvas>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-6">
      <div class="card h-100">
        <div class="card-header" data-i18n="recentReports">Recent Reports</div>
        <div class="card-body p-0">
          <ul class="list-group list-group-flush">
            <li class="list-group-item">#4567 - <span data-i18n="reportSystem">System report</span> - 2025-06-01</li>
            <li class="list-group-item">#4566 - <span data-i18n="reportUsers">User report</span> - 2025-05-30</li>
            <!-- ...más reportes -->
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
