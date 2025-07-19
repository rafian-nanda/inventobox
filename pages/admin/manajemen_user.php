<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header('Location: /index.php');
  exit;
}

require_once __DIR__ . '/../../database/koneksi.php';

// Ambil data user dari database
$query = "SELECT id, username, role, tipe_toko FROM users ORDER BY id ASC";
$result = mysqli_query($conn, $query);

$users = [];
while ($row = mysqli_fetch_assoc($result)) {
  $users[] = $row;
}

$pageTitle = 'Manajemen User';
ob_start();
?>

<style>
.professional-header {
  background: linear-gradient(135deg, #1f2937 0%, #374151 50%, #4b5563 100%);
  color: white;
  border-bottom: 3px solid #6b7280;
}

.card-shadow {
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.hover-lift:hover {
  transform: translateY(-2px);
  transition: transform 0.2s ease-in-out;
  box-shadow: 0 8px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

.table-row:hover {
  background-color: #f8fafc;
  border-left: 4px solid #374151;
}

.search-focus:focus {
  border-color: #374151;
  box-shadow: 0 0 0 3px rgba(55, 65, 81, 0.1);
}

.btn-primary {
  background: linear-gradient(135deg, #374151 0%, #4b5563 100%);
  border: none;
  color: white;
  transition: all 0.3s ease;
}

.btn-primary:hover {
  transform: translateY(-1px);
  box-shadow: 0 8px 25px -5px rgba(55, 65, 81, 0.5);
  background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
}

.stats-card {
  background: white;
  border-radius: 8px;
  padding: 1.5rem;
  border: 1px solid #e5e7eb;
  transition: all 0.3s ease;
}

.stats-card:hover {
  border-color: #6b7280;
  transform: translateY(-2px);
}

.stats-icon {
  width: 48px;
  height: 48px;
  background: #f3f4f6;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #374151;
}

.role-badge {
  padding: 0.375rem 0.75rem;
  border-radius: 6px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  display: inline-flex;
  align-items: center;
  gap: 0.375rem;
}

.role-admin {
  background-color: #1f2937;
  color: white;
}

.role-gudang {
  background-color: #6b7280;
  color: white;
}

.role-toko {
  background-color: #9ca3af;
  color: white;
}

.role-unknown {
  background-color: #f3f4f6;
  color: #374151;
  border: 1px solid #d1d5db;
}

.action-btn {
  padding: 0.5rem 1rem;
  border-radius: 6px;
  font-size: 0.875rem;
  font-weight: 500;
  transition: all 0.2s ease;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
}

.action-btn:hover {
  transform: translateY(-1px);
}

.btn-edit {
  background-color: #f9fafb;
  color: #374151;
  border: 1px solid #d1d5db;
}

.btn-edit:hover {
  background-color: #f3f4f6;
  border-color: #9ca3af;
}

.btn-delete {
  background-color: #f3f4f6;
  color: #dc2626;
  border: 1px solid #d1d5db;
}

.btn-delete:hover {
  background-color: #fee2e2;
  border-color: #fca5a5;
}

.search-container {
  position: relative;
  max-width: 400px;
}

.search-input {
  width: 100%;
  padding: 0.75rem 1rem 0.75rem 2.5rem;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  font-size: 0.875rem;
  transition: all 0.2s ease;
}

.search-input:focus {
  outline: none;
  border-color: #374151;
  box-shadow: 0 0 0 3px rgba(55, 65, 81, 0.1);
}

.search-icon {
  position: absolute;
  left: 0.75rem;
  top: 50%;
  transform: translateY(-50%);
  color: #6b7280;
}

.filter-select {
  padding: 0.75rem 2.5rem 0.75rem 1rem;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  font-size: 0.875rem;
  background-color: white;
  transition: all 0.2s ease;
}

.filter-select:focus {
  outline: none;
  border-color: #374151;
  box-shadow: 0 0 0 3px rgba(55, 65, 81, 0.1);
}

.professional-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  background: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  border: 1px solid #e5e7eb;
}

.professional-table th {
  background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
  color: white;
  padding: 1rem;
  text-align: left;
  font-weight: 600;
  font-size: 0.875rem;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.professional-table td {
  padding: 1rem;
  border-bottom: 1px solid #e5e7eb;
}

.professional-table tr:last-child td {
  border-bottom: none;
}

.avatar-placeholder {
  width: 48px;
  height: 48px;
  border-radius: 8px;
  background: #f3f4f6;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #374151;
  font-weight: 600;
  font-size: 1.125rem;
  border: 1px solid #e5e7eb;
}

.user-info h4 {
  margin: 0 0 0.25rem 0;
  font-size: 0.875rem;
  font-weight: 600;
  color: #1f2937;
}

.user-info p {
  margin: 0;
  font-size: 0.75rem;
  color: #6b7280;
}

.empty-state {
  text-align: center;
  padding: 3rem 2rem;
  color: #6b7280;
}

.empty-state h3 {
  margin: 1rem 0 0.5rem 0;
  font-size: 1.125rem;
  font-weight: 600;
  color: #374151;
}

.mobile-card {
  background: white;
  border-radius: 8px;
  padding: 1.5rem;
  margin-bottom: 1rem;
  border: 1px solid #e5e7eb;
  transition: all 0.3s ease;
}

.mobile-card:hover {
  border-color: #6b7280;
  transform: translateY(-2px);
  box-shadow: 0 8px 25px -5px rgba(0, 0, 0, 0.1);
}

.icon-sm {
  width: 16px;
  height: 16px;
}

.icon-md {
  width: 20px;
  height: 20px;
}

.icon-lg {
  width: 24px;
  height: 24px;
}

.controls-panel {
  background: white;
  border-radius: 8px;
  border: 1px solid #e5e7eb;
  padding: 1.5rem;
}

/* Enhanced Mobile Support */
@media (max-width: 768px) {
  .professional-header {
    padding: 1.5rem 1rem;
  }
  
  .professional-header h1 {
    font-size: 1.875rem;
  }
  
  .professional-header p {
    font-size: 1rem;
  }
  
  .stats-grid {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
  
  .stats-card {
    padding: 1.25rem;
  }
  
  .controls-container {
    flex-direction: column;
    gap: 1rem;
    align-items: stretch;
  }
  
  .search-container {
    max-width: 100%;
  }
  
  .filter-controls {
    flex-direction: column;
    gap: 0.75rem;
    align-items: stretch;
  }
  
  .filter-select {
    width: 100%;
  }
  
  .mobile-card {
    margin-bottom: 0.75rem;
    padding: 1.25rem;
  }
  
  .mobile-actions {
    flex-direction: column;
    gap: 0.5rem;
  }
  
  .action-btn {
    padding: 0.75rem 1rem;
    font-size: 0.875rem;
    text-align: center;
  }
  
  .btn-primary {
    width: 100%;
    justify-content: center;
    padding: 0.875rem 1.5rem;
  }
}

@media (max-width: 480px) {
  .professional-header {
    padding: 1.25rem 0.75rem;
  }
  
  .professional-header h1 {
    font-size: 1.5rem;
  }
  
  .stats-card {
    padding: 1rem;
  }
  
  .stats-card .flex {
    flex-direction: column;
    text-align: center;
    gap: 0.75rem;
  }
  
  .controls-panel {
    padding: 1rem;
  }
  
  .mobile-card {
    padding: 1rem;
  }
  
  .avatar-placeholder {
    width: 40px;
    height: 40px;
  }
  
  .user-info h4 {
    font-size: 0.8rem;
  }
  
  .role-badge {
    font-size: 0.625rem;
    padding: 0.25rem 0.5rem;
  }
}

/* Touch-friendly improvements */
@media (hover: none) and (pointer: coarse) {
  .action-btn {
    min-height: 44px;
    min-width: 44px;
  }
  
  .btn-primary {
    min-height: 48px;
  }
  
  .search-input {
    min-height: 44px;
  }
  
  .filter-select {
    min-height: 44px;
  }
  
  .mobile-card {
    padding: 1.5rem;
    margin-bottom: 1rem;
  }
  
  .hover-lift:hover {
    transform: none;
  }
  
  .table-row:hover {
    background-color: transparent;
    border-left: none;
  }
}
</style>

<!-- Page Header -->
<div class="professional-header">
  <div class="px-4 sm:px-6 lg:px-8 py-8">
    <div class="max-w-7xl mx-auto">
      <div class="sm:flex sm:items-center sm:justify-between">
        <div class="min-w-0 flex-1">
          <div class="flex items-center gap-3 mb-2">
            <svg class="icon-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
            </svg>
            <h1 class="text-3xl font-bold leading-tight">Manajemen User</h1>
          </div>
          <p class="text-lg text-gray-300">Manage system users and their permissions</p>
        </div>
        <div class="mt-4 sm:mt-0">
          <button type="button" onclick="window.location.href='add_user.php'" 
                  class="btn-primary inline-flex items-center px-6 py-3 text-sm font-semibold rounded-lg shadow-lg">
            <svg class="icon-sm mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add New User
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Statistics Overview -->
<div class="px-4 sm:px-6 lg:px-8 py-8 bg-gray-50">
  <div class="max-w-7xl mx-auto">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 stats-grid">
      <?php
      $stats = [
        'admin' => 0,
        'gudang' => 0,
        'toko' => 0,
        'total' => count($users)
      ];
      
      foreach ($users as $user) {
        if (isset($stats[$user['role']])) {
          $stats[$user['role']]++;
        }
      }
      
      $statCards = [
        [
          'name' => 'Total Users',
          'value' => $stats['total'],
          'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z'
        ],
        [
          'name' => 'Administrators',
          'value' => $stats['admin'],
          'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'
        ],
        [
          'name' => 'Warehouse Staff',
          'value' => $stats['gudang'],
          'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'
        ],
        [
          'name' => 'Store Staff',
          'value' => $stats['toko'],
          'icon' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z'
        ]
      ];
      ?>
      
      <?php foreach ($statCards as $card): ?>
        <div class="stats-card hover-lift">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600 mb-1"><?= $card['name'] ?></p>
              <p class="text-2xl font-bold text-gray-900"><?= $card['value'] ?></p>
            </div>
            <div class="stats-icon">
              <svg class="icon-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $card['icon'] ?>"></path>
              </svg>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<!-- Search and Filter Controls -->
<div class="px-4 sm:px-6 lg:px-8 py-6">
  <div class="max-w-7xl mx-auto">
    <div class="controls-panel">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 controls-container">
        <div class="search-container">
          <div class="relative">
            <svg class="search-icon icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input id="searchInput" 
                   type="text" 
                   class="search-input" 
                   placeholder="Search users by name...">
          </div>
        </div>
        <div class="flex items-center gap-3 filter-controls">
          <div class="relative flex-1 sm:flex-none">
            <select id="roleFilter" class="filter-select w-full sm:w-auto">
              <option value="">All Roles</option>
              <option value="admin">Administrator</option>
              <option value="gudang">Warehouse</option>
              <option value="toko">Store</option>
            </select>
          </div>
          <button type="button" id="clearFilters"
                  class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border-2 border-gray-300 rounded-lg hover:bg-gray-50 transition-colors whitespace-nowrap">
            <svg class="icon-sm mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
            <span class="hidden sm:inline">Clear</span>
            <span class="sm:hidden">Clear</span>
          </button>
          <div class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg whitespace-nowrap">
            <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
            </svg>
            <span id="userCount"><?= count($users) ?> users</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- User List -->
<div class="px-4 sm:px-6 lg:px-8 pb-8">
  <div class="max-w-7xl mx-auto">
    
    <!-- Mobile View -->
    <div class="sm:hidden">
      <div id="emptyStateMobile" class="empty-state hidden">
        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
        </svg>
        <h3>No users found</h3>
        <p>Try adjusting your search or filter criteria.</p>
      </div>

      <div id="mobileUserList">
        <?php foreach ($users as $user): ?>
          <div class="mobile-card user-card" data-username="<?= strtolower(htmlspecialchars($user['username'])) ?>" data-role="<?= $user['role'] ?>">
            <div class="flex items-start justify-between mb-4">
              <div class="flex items-center">
                <div class="avatar-placeholder">
                  <svg class="icon-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                  </svg>
                </div>
                <div class="ml-4">
                  <h4 class="font-semibold text-gray-900"><?= htmlspecialchars($user['username']) ?></h4>
                  <p class="text-sm text-gray-500">ID: <?= $user['id'] ?></p>
                </div>
              </div>
              <div>
                <?php if ($user['role'] === 'admin'): ?>
                  <span class="role-badge role-admin">
                    <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    Admin
                  </span>
                <?php elseif ($user['role'] === 'gudang'): ?>
                  <span class="role-badge role-gudang">
                    <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    Warehouse
                  </span>
                <?php elseif ($user['role'] === 'toko'): ?>
                  <span class="role-badge role-toko">
                    <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                    Store
                    <?php if ($user['tipe_toko']): ?>
                      (<?= htmlspecialchars($user['tipe_toko']) ?>)
                    <?php endif; ?>
                  </span>
                <?php else: ?>
                  <span class="role-badge role-unknown">
                    <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    Unknown
                  </span>
                <?php endif; ?>
              </div>
            </div>
            <div class="flex gap-2 pt-4 border-t border-gray-200 mobile-actions">
              <a href="edit_user.php?id=<?= $user['id'] ?>" 
                 class="action-btn btn-edit flex-1 justify-center">
                <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                <span>Edit</span>
              </a>
                 <a href="#" 
                    class="action-btn btn-delete flex-1 justify-center delete-user-btn"
                    data-id="<?= $user['id'] ?>" data-username="<?= htmlspecialchars($user['username']) ?>">
                <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                <span>Delete</span>
              </a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Desktop View -->
    <div class="hidden sm:block overflow-x-auto">
      <div id="emptyStateDesktop" class="empty-state hidden">
        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
        </svg>
        <h3>No users found</h3>
        <p>Try adjusting your search or filter criteria.</p>
      </div>

      <div id="tableContainer" class="min-w-full">
        <table class="professional-table">
          <thead>
            <tr>
              <th>
                <div class="flex items-center gap-2">
                  <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                  </svg>
                  <span class="hidden md:inline">User Information</span>
                  <span class="md:hidden">User</span>
                </div>
              </th>
              <th>
                <div class="flex items-center gap-2">
                  <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                  </svg>
                  <span class="hidden lg:inline">Role & Permissions</span>
                  <span class="lg:hidden">Role</span>
                </div>
              </th>
              <th>
                <div class="flex items-center gap-2">
                  <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                  </svg>
                  <span>Actions</span>
                </div>
              </th>
            </tr>
          </thead>
          <tbody id="userTableBody">
            <?php foreach ($users as $user): ?>
              <tr class="user-row table-row" data-username="<?= strtolower(htmlspecialchars($user['username'])) ?>" data-role="<?= $user['role'] ?>">
                <td>
                  <div class="flex items-center">
                    <div class="avatar-placeholder">
                      <svg class="icon-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                      </svg>
                    </div>
                    <div class="ml-4 user-info">
                      <h4><?= htmlspecialchars($user['username']) ?></h4>
                      <p>User ID: <?= $user['id'] ?></p>
                    </div>
                  </div>
                </td>
                <td>
                  <?php if ($user['role'] === 'admin'): ?>
                    <span class="role-badge role-admin">
                      <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                      </svg>
                      Administrator
                    </span>
                  <?php elseif ($user['role'] === 'gudang'): ?>
                    <span class="role-badge role-gudang">
                      <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                      </svg>
                      Warehouse
                    </span>
                  <?php elseif ($user['role'] === 'toko'): ?>
                    <span class="role-badge role-toko">
                      <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                      </svg>
                      Store
                      <?php if ($user['tipe_toko']): ?>
                        (<?= htmlspecialchars($user['tipe_toko']) ?>)
                      <?php endif; ?>
                    </span>
                  <?php else: ?>
                    <span class="role-badge role-unknown">
                      <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                      </svg>
                      Unknown
                    </span>
                  <?php endif; ?>
                </td>
                <td>
                  <div class="flex items-center gap-2 justify-end">
                    <a href="edit_user.php?id=<?= $user['id'] ?>" 
                       class="action-btn btn-edit">
                      <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                      </svg>
                      <span class="hidden lg:inline">Edit</span>
                    </a>
                    <a href="#" 
                       class="action-btn btn-delete delete-user-btn"
                       data-id="<?= $user['id'] ?>" data-username="<?= htmlspecialchars($user['username']) ?>">
                      <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                      </svg>
                      <span class="hidden lg:inline">Delete</span>
                    </a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

</script>
<script>
// Modal konfirmasi hapus user
function showDeleteModal(userId, username) {
  const modal = document.getElementById('deleteModal');
  const modalUsername = document.getElementById('deleteModalUsername');
  const confirmBtn = document.getElementById('confirmDeleteBtn');
  modalUsername.textContent = username;
  confirmBtn.setAttribute('data-id', userId);
  modal.classList.remove('hidden');
}
function hideDeleteModal() {
  document.getElementById('deleteModal').classList.add('hidden');
}
document.addEventListener('DOMContentLoaded', function() {
  const searchInput = document.getElementById('searchInput');
  const roleFilter = document.getElementById('roleFilter');
  const clearFiltersBtn = document.getElementById('clearFilters');
  const userCount = document.getElementById('userCount');
  const mobileCards = document.querySelectorAll('.user-card');
  const desktopRows = document.querySelectorAll('.user-row');
  const emptyStateMobile = document.getElementById('emptyStateMobile');
  const emptyStateDesktop = document.getElementById('emptyStateDesktop');
  const mobileUserList = document.getElementById('mobileUserList');
  const tableContainer = document.getElementById('tableContainer');

  // Delete button event
  document.querySelectorAll('.delete-user-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      const userId = this.getAttribute('data-id');
      const username = this.getAttribute('data-username');
      showDeleteModal(userId, username);
    });
  });
  document.getElementById('cancelDeleteBtn').addEventListener('click', hideDeleteModal);
  document.getElementById('closeDeleteModal').addEventListener('click', hideDeleteModal);
  document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    const userId = this.getAttribute('data-id');
    window.location.href = 'hapus_user.php?id=' + userId;
  });

  function filterUsers() {
    const searchTerm = searchInput.value.toLowerCase().trim();
    const roleValue = roleFilter.value.toLowerCase();
    let visibleCount = 0;
    // Filter mobile cards
    mobileCards.forEach(card => {
      const username = card.getAttribute('data-username');
      const role = card.getAttribute('data-role');
      const matchesSearch = !searchTerm || username.includes(searchTerm);
      const matchesRole = !roleValue || role === roleValue;
      if (matchesSearch && matchesRole) {
        card.style.display = 'block';
        visibleCount++;
      } else {
        card.style.display = 'none';
      }
    });
    // Filter desktop rows
    desktopRows.forEach(row => {
      const username = row.getAttribute('data-username');
      const role = row.getAttribute('data-role');
      const matchesSearch = !searchTerm || username.includes(searchTerm);
      const matchesRole = !roleValue || role === roleValue;
      if (matchesSearch && matchesRole) {
        row.style.display = 'table-row';
      } else {
        row.style.display = 'none';
      }
    });
    // Update counter and empty states
    userCount.textContent = `${visibleCount} users`;
    if (visibleCount === 0) {
      emptyStateMobile.classList.remove('hidden');
      emptyStateDesktop.classList.remove('hidden');
      mobileUserList.style.display = 'none';
      tableContainer.style.display = 'none';
    } else {
      emptyStateMobile.classList.add('hidden');
      emptyStateDesktop.classList.add('hidden');
      mobileUserList.style.display = 'block';
      tableContainer.style.display = 'block';
    }
  }
  function clearFilters() {
    searchInput.value = '';
    roleFilter.value = '';
    filterUsers();
  }
  searchInput.addEventListener('input', filterUsers);
  roleFilter.addEventListener('change', filterUsers);
  clearFiltersBtn.addEventListener('click', clearFilters);
  let searchTimeout;
  searchInput.addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(filterUsers, 300);
  });
});
</script>
</script>

<!-- Modal Konfirmasi Hapus User -->
<div id="deleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
  <div class="bg-white rounded-lg shadow-lg max-w-sm w-full p-6 relative">
    <button id="closeDeleteModal" class="absolute top-2 right-2 text-gray-400 hover:text-gray-600 focus:outline-none">
      <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
      </svg>
    </button>
    <div class="flex items-center mb-4">
      <svg class="icon-lg text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
      </svg>
      <h3 class="text-lg font-semibold text-gray-800">Konfirmasi Hapus User</h3>
    </div>
    <p class="mb-6 text-gray-700">Apakah Anda yakin ingin menghapus user <span class="font-bold" id="deleteModalUsername"></span>? Tindakan ini tidak dapat dibatalkan.</p>
    <div class="flex justify-end gap-3">
      <button id="cancelDeleteBtn" class="px-4 py-2 rounded-lg bg-gray-200 text-gray-700 font-medium hover:bg-gray-300">Batal</button>
      <button id="confirmDeleteBtn" class="px-4 py-2 rounded-lg bg-red-600 text-white font-semibold hover:bg-red-700" data-id="">Hapus</button>
    </div>
  </div>
</div>

<?php
$content = ob_get_clean();
include_once(__DIR__ . '/../../layouts/layout.php');