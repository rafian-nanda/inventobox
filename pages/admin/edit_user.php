<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header('Location: /index.php');
  exit;
}

require_once __DIR__ . '/../../database/koneksi.php';

$pageTitle = 'Edit User';
$error = '';
$success = '';
$user = null;

// Get user ID from URL
$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($user_id <= 0) {
  header('Location: index.php');
  exit;
}

// Fetch user data
$query = "SELECT id, username, role, tipe_toko FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
  header('Location: index.php');
  exit;
}

$user = mysqli_fetch_assoc($result);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username']);
  $role = $_POST['role'];
  $tipe_toko = isset($_POST['tipe_toko']) ? trim($_POST['tipe_toko']) : null;
  $new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';
  $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
  
  // Validation
  if (empty($username) || empty($role)) {
    $error = 'Username and role are required';
  } elseif (!empty($new_password) && $new_password !== $confirm_password) {
    $error = 'Password confirmation does not match';
  } elseif (!empty($new_password) && strlen($new_password) < 6) {
    $error = 'Password must be at least 6 characters long';
  } else {
    // Check if username already exists (excluding current user)
    $check_query = "SELECT id FROM users WHERE username = ? AND id != ?";
    $check_stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($check_stmt, "si", $username, $user_id);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);
    
    if (mysqli_num_rows($check_result) > 0) {
      $error = 'Username already exists';
    } else {
      // Update user
      if (!empty($new_password)) {
        // Update with new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_query = "UPDATE users SET username = ?, password = ?, role = ?, tipe_toko = ? WHERE id = ?";
        $update_stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($update_stmt, "ssssi", $username, $hashed_password, $role, $tipe_toko, $user_id);
      } else {
        // Update without password change
        $update_query = "UPDATE users SET username = ?, role = ?, tipe_toko = ? WHERE id = ?";
        $update_stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($update_stmt, "sssi", $username, $role, $tipe_toko, $user_id);
      }
      
      if (mysqli_stmt_execute($update_stmt)) {
        $success = 'User updated successfully!';
        // Update local user data
        $user['username'] = $username;
        $user['role'] = $role;
        $user['tipe_toko'] = $tipe_toko;
        // Redirect after success
        header('refresh:2;url=manajemen_user.php');
      } else {
        $error = 'Failed to update user';
      }
    }
  }
}

ob_start();
?>

<style>
.professional-header {
  background: linear-gradient(135deg, #1f2937 0%, #374151 50%, #4b5563 100%);
  color: white;
  border-bottom: 3px solid #6b7280;
}

.form-container {
  background: white;
  border-radius: 12px;
  border: 1px solid #e5e7eb;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.info-card {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 1rem;
  margin-bottom: 1.5rem;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-label {
  display: block;
  font-size: 0.875rem;
  font-weight: 600;
  color: #374151;
  margin-bottom: 0.5rem;
}

.form-label.required::after {
  content: ' *';
  color: #dc2626;
}

.form-input {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  font-size: 0.875rem;
  transition: all 0.2s ease;
  background-color: white;
}

.form-input:focus {
  outline: none;
  border-color: #374151;
  box-shadow: 0 0 0 3px rgba(55, 65, 81, 0.1);
}

.form-input:invalid {
  border-color: #dc2626;
}

.form-select {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  font-size: 0.875rem;
  background-color: white;
  transition: all 0.2s ease;
}

.form-select:focus {
  outline: none;
  border-color: #374151;
  box-shadow: 0 0 0 3px rgba(55, 65, 81, 0.1);
}

.btn-primary {
  background: linear-gradient(135deg, #374151 0%, #4b5563 100%);
  color: white;
  border: none;
  padding: 0.875rem 2rem;
  border-radius: 8px;
  font-size: 0.875rem;
  font-weight: 600;
  transition: all 0.3s ease;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  text-decoration: none;
  line-height: 1;
}

.btn-primary:hover {
  background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
  transform: translateY(-1px);
  box-shadow: 0 8px 25px -5px rgba(55, 65, 81, 0.5);
}

.btn-secondary {
  background: white;
  color: #374151;
  border: 2px solid #e5e7eb;
  padding: 0.875rem 2rem;
  border-radius: 8px;
  font-size: 0.875rem;
  font-weight: 600;
  transition: all 0.3s ease;
  cursor: pointer;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
}

.btn-secondary:hover {
  background: #f9fafb;
  border-color: #9ca3af;
  transform: translateY(-1px);
}

.btn-danger {
  background: #dc2626;
  color: white;
  border: none;
  padding: 0.875rem 2rem;
  border-radius: 8px;
  font-size: 0.875rem;
  font-weight: 600;
  transition: all 0.3s ease;
  cursor: pointer;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  line-height: 1;
}

.btn-danger:hover {
  background: #b91c1c;
  transform: translateY(-1px);
  box-shadow: 0 8px 25px -5px rgba(220, 38, 38, 0.5);
}

.alert {
  padding: 1rem 1.25rem;
  border-radius: 8px;
  margin-bottom: 1.5rem;
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.alert-error {
  background-color: #fef2f2;
  color: #991b1b;
  border: 1px solid #fecaca;
}

.alert-success {
  background-color: #f0fdf4;
  color: #166534;
  border: 1px solid #bbf7d0;
}

.alert-warning {
  background-color: #fffbeb;
  color: #92400e;
  border: 1px solid #fed7aa;
}

.form-help {
  font-size: 0.75rem;
  color: #6b7280;
  margin-top: 0.25rem;
}

.password-section {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 1.5rem;
  margin-top: 1.5rem;
}

.password-section h3 {
  font-size: 1rem;
  font-weight: 600;
  color: #374151;
  margin-bottom: 1rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.checkbox-group {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 1rem;
}

.checkbox {
  width: 1.25rem;
  height: 1.25rem;
  border: 2px solid #d1d5db;
  border-radius: 4px;
  cursor: pointer;
}

.checkbox:checked {
  background-color: #374151;
  border-color: #374151;
}

.icon-sm {
  width: 22px;
  height: 22px;
}

.icon-md {
  width: 20px;
  height: 20px;
}

.icon-lg {
  width: 24px;
  height: 24px;
}

.breadcrumb {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.875rem;
  color: #6b7280;
  margin-bottom: 1.5rem;
}

.breadcrumb a {
  color: #374151;
  text-decoration: none;
  transition: color 0.2s ease;
}

.breadcrumb a:hover {
  color: #1f2937;
}

.breadcrumb-separator {
  color: #9ca3af;
}

.user-status {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.375rem 0.75rem;
  border-radius: 6px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.action-buttons-mobile {
  display: none;
}

/* Enhanced Mobile Support for Edit User */
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
  
  .form-container {
    margin: 0 1rem;
    padding: 1.5rem;
  }
  
  .info-card {
    padding: 1rem;
  }
  
  .info-card .flex {
    flex-direction: column;
    gap: 1rem;
    align-items: flex-start;
  }
  
  .info-card .mobile-avatar {
    width: 2.75rem;
    height: 2.75rem;
  }
  
  .info-card .mobile-icon {
    width: 1.75rem;
    height: 1.75rem;
  }
  
  .form-grid {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
  
  .password-section {
    padding: 1.25rem;
  }
  
  .password-section h3 {
    font-size: 0.875rem;
  }
  
  .action-buttons-desktop {
    display: none !important;
  }
  
  .action-buttons-mobile {
    display: block;
  }
  
  .btn-primary,
  .btn-secondary,
  .btn-danger {
    padding: 1rem 1.5rem;
    font-size: 0.875rem;
    min-height: 48px;
  }
  
  .breadcrumb {
    flex-wrap: wrap;
    gap: 0.25rem;
  }
  
  .user-status {
    align-self: flex-start;
  }
}

@media (max-width: 480px) {
  .professional-header {
    padding: 1.25rem 0.75rem;
  }
  
  .professional-header h1 {
    font-size: 1.5rem;
  }
  
  .professional-header .flex {
    flex-direction: column;
    gap: 0.5rem;
    align-items: flex-start;
  }
  
  .form-container {
    margin: 0 0.75rem;
    padding: 1.25rem;
  }
  
  .info-card {
    padding: 0.875rem;
  }
  
  .info-card h3 {
    font-size: 0.875rem;
  }
  
  .info-card p {
    font-size: 0.75rem;
  }
  
  .info-card .mobile-avatar {
    width: 2.5rem;
    height: 2.5rem;
  }
  
  .info-card .mobile-icon {
    width: 1.5rem;
    height: 1.5rem;
  }
  
  .form-input,
  .form-select {
    font-size: 16px; /* Prevents zoom on iOS */
    padding: 0.875rem 1rem;
  }
  
  .password-section {
    padding: 1rem;
  }
  
  .alert {
    padding: 0.875rem 1rem;
    font-size: 0.875rem;
  }
  
  .form-help {
    font-size: 0.6875rem;
  }
  
  .breadcrumb {
    font-size: 0.75rem;
  }
  
  .user-status {
    padding: 0.25rem 0.5rem;
    font-size: 0.625rem;
  }
}

/* Tablet-specific adjustments */
@media (min-width: 481px) and (max-width: 768px) {
  .form-grid {
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
  }
  
  .form-group[class*="md:col-span-2"] {
    grid-column: 1 / -1;
  }
  
  .password-section .grid {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
}

/* Touch-friendly improvements */
@media (hover: none) and (pointer: coarse) {
  .btn-primary,
  .btn-secondary,
  .btn-danger {
    min-height: 48px;
    min-width: 44px;
    touch-action: manipulation;
  }
  
  .form-input,
  .form-select {
    min-height: 44px;
    touch-action: manipulation;
  }
  
  .checkbox {
    width: 1.5rem;
    height: 1.5rem;
    touch-action: manipulation;
  }
  
  /* Remove hover effects on touch devices */
  .btn-primary:hover,
  .btn-secondary:hover,
  .btn-danger:hover {
    transform: none;
    box-shadow: none;
  }
  
  .form-input:hover,
  .form-select:hover {
    border-color: #e5e7eb;
  }
}

/* Landscape mobile optimization */
@media (max-height: 500px) and (orientation: landscape) {
  .professional-header {
    padding: 1rem;
  }
  
  .professional-header h1 {
    font-size: 1.25rem;
  }
  
  .professional-header p {
    display: none;
  }
  
  .form-container {
    padding: 1rem;
  }
  
  .password-section {
    padding: 1rem;
  }
}

/* Focus improvements for mobile */
@media (max-width: 768px) {
  .form-input:focus,
  .form-select:focus {
    transform: scale(1.02);
    transition: transform 0.1s ease;
  }
  
  .btn-primary:focus,
  .btn-secondary:focus,
  .btn-danger:focus {
    outline: 3px solid rgba(55, 65, 81, 0.3);
    outline-offset: 2px;
  }
}
</style>

<!-- Page Header -->
<div class="professional-header">
  <div class="px-4 sm:px-6 lg:px-8 py-8">
    <div class="max-w-4xl mx-auto">
      <div class="flex items-center gap-3 mb-2">
        <svg class="icon-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
        </svg>
        <h1 class="text-3xl font-bold leading-tight">Edit User</h1>
      </div>
      <p class="text-lg text-gray-300">Modify user account details and permissions</p>
    </div>
  </div>
</div>

<!-- Main Content -->
<div class="px-4 sm:px-6 lg:px-8 py-8 bg-gray-50 min-h-screen">
  <div class="max-w-4xl mx-auto">
    
    <!-- Breadcrumb -->
    <div class="breadcrumb">
      <a href="manajemen_user.php">User Management</a>
      <svg class="icon-sm breadcrumb-separator" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
      </svg>
      <span>Edit User</span>
    </div>

    <!-- Form Container -->
    <div class="form-container p-8">
      
      <!-- User Info Card -->
      <div class="info-card">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center mobile-avatar" style="display: flex; align-items: center; justify-content: center;">
              <svg class="w-7 h-7 text-gray-600 mobile-icon flex-shrink-0" style="display: block; margin: auto;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
              </svg>
            </div>
            <div>
              <h3 class="font-semibold text-gray-900">User ID: <?= $user['id'] ?></h3>
              <p class="text-sm text-gray-600">Currently editing user account</p>
            </div>
          </div>
          <div class="user-status status-active">
            <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Active
          </div>
        </div>
      </div>
      
      <!-- Alerts -->
      <?php if ($error): ?>
        <div class="alert alert-error">
          <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
          </svg>
          <span><?= htmlspecialchars($error) ?></span>
        </div>
      <?php endif; ?>

      <?php if ($success): ?>
        <div class="alert alert-success">
          <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <span><?= htmlspecialchars($success) ?></span>
        </div>
      <?php endif; ?>

      <!-- Warning Alert -->
      <div class="alert alert-warning">
        <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
        </svg>
        <span>Changes to user roles and permissions will take effect immediately.</span>
      </div>

      <!-- Form -->
      <form method="POST" action="" novalidate>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 form-grid">
          
          <!-- Username -->
          <div class="form-group md:col-span-1">
            <label for="username" class="form-label required">Username</label>
            <input type="text" 
                   id="username" 
                   name="username" 
                   class="form-input"
                   value="<?= htmlspecialchars($user['username']) ?>"
                   placeholder="Enter username"
                   required>
            <div class="form-help">Username must be unique and contain only letters, numbers, and underscores</div>
          </div>

          <!-- Role -->
          <div class="form-group md:col-span-1">
            <label for="role" class="form-label required">Role</label>
            <select id="role" name="role" class="form-select" required>
              <option value="">Select Role</option>
              <option value="admin" <?= ($user['role'] === 'admin') ? 'selected' : '' ?>>
                Administrator
              </option>
              <option value="gudang" <?= ($user['role'] === 'gudang') ? 'selected' : '' ?>>
                Warehouse Staff
              </option>
              <option value="toko" <?= ($user['role'] === 'toko') ? 'selected' : '' ?>>
                Store Staff
              </option>
            </select>
            <div class="form-help">Select the appropriate role for this user</div>
          </div>

          <!-- Store Type (conditional) -->
          <div class="form-group md:col-span-2" id="tipe-toko-group" style="<?= ($user['role'] === 'toko') ? 'display: block;' : 'display: none;' ?>">
            <label for="tipe_toko" class="form-label">Store Type</label>
            <input type="text" 
                   id="tipe_toko" 
                   name="tipe_toko" 
                   class="form-input"
                   value="<?= htmlspecialchars($user['tipe_toko'] ?? '') ?>"
                   placeholder="Enter store type (e.g., Main Store, Branch A, etc.)">
            <div class="form-help">Specify the store type if this user is assigned to a specific store</div>
          </div>

        </div>

        <!-- Password Section -->
        <div class="password-section">
          <h3>
            <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
            </svg>
            Change Password
          </h3>
          
          <div class="checkbox-group">
            <input type="checkbox" id="change_password" class="checkbox">
            <label for="change_password" class="text-sm font-medium text-gray-700">I want to change the password</label>
          </div>

          <div id="password-fields" style="display: none;">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- New Password -->
              <div class="form-group">
                <label for="new_password" class="form-label">New Password</label>
                <input type="password" 
                       id="new_password" 
                       name="new_password" 
                       class="form-input"
                       placeholder="Enter new password"
                       minlength="6">
                <div class="form-help">Leave blank to keep current password</div>
              </div>

              <!-- Confirm Password -->
              <div class="form-group">
                <label for="confirm_password" class="form-label">Confirm New Password</label>
                <input type="password" 
                       id="confirm_password" 
                       name="confirm_password" 
                       class="form-input"
                       placeholder="Confirm new password"
                       minlength="6">
                <div class="form-help">Re-enter the new password to confirm</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="mt-8 pt-6 border-t border-gray-200">
          <!-- Desktop Layout -->
          <div class="hidden sm:flex items-center justify-between action-buttons-desktop">
            <div>
              <a href="index.php" class="btn-secondary">
                <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Back to List</span>
              </a>
            </div>
            <div class="flex items-center gap-3">
              <a href="hapus_user.php?id=<?= $user['id'] ?>" 
                 onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')"
                 class="btn-danger">
                <svg class="icon-sm mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                <span>Delete User</span>
              </a>
              <button type="submit" class="btn-primary">
                <svg class="icon-sm mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                </svg>
                <span>Update User</span>
              </button>
            </div>
          </div>

          <!-- Mobile Layout -->
          <div class="sm:hidden action-buttons-mobile">
            <div class="flex flex-col gap-3">
              <button type="submit" class="btn-primary w-full flex items-center justify-center">
                <svg class="icon-sm mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                </svg>
                <span>Update User</span>
              </button>
              <div class="grid grid-cols-2 gap-3">
                <a href="manajemen_user.php" class="btn-secondary w-full flex items-center justify-center">
                  <svg class="icon-sm mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                  </svg>
                  <span>Back</span>
                </a>
                <a href="hapus_user.php?id=<?= $user['id'] ?>" 
                   onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')"
                   class="btn-danger w-full flex items-center justify-center">
                  <svg class="icon-sm mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                  </svg>
                  <span>Delete</span>
                </a>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const roleSelect = document.getElementById('role');
  const tipoTokoGroup = document.getElementById('tipe-toko-group');
  const tipoTokoInput = document.getElementById('tipe_toko');
  const changePasswordCheckbox = document.getElementById('change_password');
  const passwordFields = document.getElementById('password-fields');
  const newPasswordInput = document.getElementById('new_password');
  const confirmPasswordInput = document.getElementById('confirm_password');

  // Show/hide store type field based on role
  function toggleStoreType() {
    if (roleSelect.value === 'toko') {
      tipoTokoGroup.style.display = 'block';
    } else {
      tipoTokoGroup.style.display = 'none';
      tipoTokoInput.value = '';
    }
  }

  // Show/hide password fields
  function togglePasswordFields() {
    if (changePasswordCheckbox.checked) {
      passwordFields.style.display = 'block';
      newPasswordInput.setAttribute('required', 'required');
      confirmPasswordInput.setAttribute('required', 'required');
    } else {
      passwordFields.style.display = 'none';
      newPasswordInput.removeAttribute('required');
      confirmPasswordInput.removeAttribute('required');
      newPasswordInput.value = '';
      confirmPasswordInput.value = '';
    }
  }

  // Initialize
  toggleStoreType();
  togglePasswordFields();

  // Listen for changes
  roleSelect.addEventListener('change', toggleStoreType);
  changePasswordCheckbox.addEventListener('change', togglePasswordFields);

  // Password confirmation validation
  function validatePasswordMatch() {
    if (changePasswordCheckbox.checked && confirmPasswordInput.value && newPasswordInput.value !== confirmPasswordInput.value) {
      confirmPasswordInput.setCustomValidity('Passwords do not match');
    } else {
      confirmPasswordInput.setCustomValidity('');
    }
  }

  newPasswordInput.addEventListener('input', validatePasswordMatch);
  confirmPasswordInput.addEventListener('input', validatePasswordMatch);

  // Form submission validation
  document.querySelector('form').addEventListener('submit', function(e) {
    if (changePasswordCheckbox.checked && newPasswordInput.value !== confirmPasswordInput.value) {
      e.preventDefault();
      alert('Password confirmation does not match');
      confirmPasswordInput.focus();
    }
  });
});
</script>

<?php
$content = ob_get_clean();
include_once(__DIR__ . '/../../layouts/layout.php');