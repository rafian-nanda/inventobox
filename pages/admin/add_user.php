<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header('Location: /index.php');
  exit;
}

require_once __DIR__ . '/../../database/koneksi.php';

$pageTitle = 'Add New User';
$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username']);
  $password = $_POST['password'];
  $confirm_password = $_POST['confirm_password'];
  $role = $_POST['role'];
  $tipe_toko = isset($_POST['tipe_toko']) ? trim($_POST['tipe_toko']) : null;
  
  // Validation
  if (empty($username) || empty($password) || empty($role)) {
    $error = 'All required fields must be filled';
  } elseif ($password !== $confirm_password) {
    $error = 'Password confirmation does not match';
  } elseif (strlen($password) < 6) {
    $error = 'Password must be at least 6 characters long';
  } else {
    // Check if username already exists
    $check_query = "SELECT id FROM users WHERE username = ?";
    $check_stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($check_stmt, "s", $username);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);
    
    if (mysqli_num_rows($check_result) > 0) {
      $error = 'Username already exists';
    } else {
      // Insert new user
      $hashed_password = password_hash($password, PASSWORD_DEFAULT);
      $insert_query = "INSERT INTO users (username, password, role, tipe_toko) VALUES (?, ?, ?, ?)";
      $insert_stmt = mysqli_prepare($conn, $insert_query);
      mysqli_stmt_bind_param($insert_stmt, "ssss", $username, $hashed_password, $role, $tipe_toko);
      
      if (mysqli_stmt_execute($insert_stmt)) {
        $success = 'User created successfully!';
        // Redirect after success
        header('refresh:2;url=manajemen_user.php');
      } else {
        $error = 'Failed to create user';
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
  border: none;
  color: white;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-size: 0.875rem;
  font-weight: 600;
  transition: all 0.3s ease;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  line-height: 1;
  text-decoration: none;
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
  padding: 0.75rem 1.5rem;
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

.btn-secondary:hover {
  background: #f9fafb;
  border-color: #9ca3af;
  transform: translateY(-1px);
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

.form-help {
  font-size: 0.75rem;
  color: #6b7280;
  margin-top: 0.25rem;
}

.password-requirements {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  padding: 0.75rem;
  margin-top: 0.5rem;
}

.requirement-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.75rem;
  color: #64748b;
  margin-bottom: 0.25rem;
}

.requirement-item:last-child {
  margin-bottom: 0;
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

/* Mobile Support */
@media (max-width: 768px) {
  .professional-header {
    padding: 1.5rem 1rem;
  }
  
  .professional-header h1 {
    font-size: 1.875rem;
  }
  
  .form-container {
    margin: 0 1rem;
    padding: 1.5rem;
  }
  
  .btn-group {
    flex-direction: column;
    gap: 0.75rem;
  }
  
  .btn-primary,
  .btn-secondary {
    width: 100%;
    justify-content: center;
  }
  
  .form-grid {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
}

@media (max-width: 480px) {
  .professional-header {
    padding: 1.25rem 0.75rem;
  }
  
  .professional-header h1 {
    font-size: 1.5rem;
  }
  
  .form-container {
    margin: 0 0.75rem;
    padding: 1.25rem;
  }
  
  .form-input,
  .form-select {
    font-size: 16px; /* Prevents zoom on iOS */
  }
}

/* Touch-friendly improvements */
@media (hover: none) and (pointer: coarse) {
  .btn-primary,
  .btn-secondary {
    min-height: 48px;
  }
  
  .form-input,
  .form-select {
    min-height: 44px;
  }
}
</style>

<!-- Page Header -->
<div class="professional-header">
  <div class="px-4 sm:px-6 lg:px-8 py-8">
    <div class="max-w-4xl mx-auto">
      <div class="flex items-center gap-3 mb-2">
        <svg class="icon-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
        </svg>
        <h1 class="text-3xl font-bold leading-tight">Add New User</h1>
      </div>
      <p class="text-lg text-gray-300">Create a new user account with appropriate permissions</p>
    </div>
  </div>
</div>

<!-- Main Content -->
<div class="px-4 sm:px-6 lg:px-8 py-8 bg-gray-50 min-h-screen">
  <div class="max-w-4xl mx-auto">
    
    <!-- Breadcrumb -->
    <div class="breadcrumb">
      <a href="index.php">User Management</a>
      <svg class="icon-sm breadcrumb-separator" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
      </svg>
      <span>Add User</span>
    </div>

    <!-- Form Container -->
    <div class="form-container p-8">
      
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
                   value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>"
                   placeholder="Enter username"
                   required>
            <div class="form-help">Username must be unique and contain only letters, numbers, and underscores</div>
          </div>

          <!-- Role -->
          <div class="form-group md:col-span-1">
            <label for="role" class="form-label required">Role</label>
            <select id="role" name="role" class="form-select" required>
              <option value="">Select Role</option>
              <option value="admin" <?= (isset($_POST['role']) && $_POST['role'] === 'admin') ? 'selected' : '' ?>>
                Administrator
              </option>
              <option value="gudang" <?= (isset($_POST['role']) && $_POST['role'] === 'gudang') ? 'selected' : '' ?>>
                Warehouse Staff
              </option>
              <option value="toko" <?= (isset($_POST['role']) && $_POST['role'] === 'toko') ? 'selected' : '' ?>>
                Store Staff
              </option>
            </select>
            <div class="form-help">Select the appropriate role for this user</div>
          </div>

          <!-- Store Type (conditional) -->
          <div class="form-group md:col-span-2" id="tipe-toko-group" style="display: none;">
            <label for="tipe_toko" class="form-label">Store Type</label>
            <input type="text" 
                   id="tipe_toko" 
                   name="tipe_toko" 
                   class="form-input"
                   value="<?= isset($_POST['tipe_toko']) ? htmlspecialchars($_POST['tipe_toko']) : '' ?>"
                   placeholder="Enter store type (e.g., Main Store, Branch A, etc.)">
            <div class="form-help">Specify the store type if this user is assigned to a specific store</div>
          </div>

          <!-- Password -->
          <div class="form-group md:col-span-1">
            <label for="password" class="form-label required">Password</label>
            <input type="password" 
                   id="password" 
                   name="password" 
                   class="form-input"
                   placeholder="Enter password"
                   minlength="6"
                   required>
            <div class="password-requirements">
              <div class="requirement-item">
                <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Minimum 6 characters
              </div>
              <div class="requirement-item">
                <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Use a strong, unique password
              </div>
            </div>
          </div>

          <!-- Confirm Password -->
          <div class="form-group md:col-span-1">
            <label for="confirm_password" class="form-label required">Confirm Password</label>
            <input type="password" 
                   id="confirm_password" 
                   name="confirm_password" 
                   class="form-input"
                   placeholder="Confirm password"
                   minlength="6"
                   required>
            <div class="form-help">Re-enter the password to confirm</div>
          </div>

        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-end gap-4 mt-8 pt-6 border-t border-gray-200 btn-group">
          <a href="index.php" class="btn-secondary">
            <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
            <span>Cancel</span>
          </a>
          <button type="submit" class="btn-primary">
            <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>Create User</span>
          </button>
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
  const passwordInput = document.getElementById('password');
  const confirmPasswordInput = document.getElementById('confirm_password');

  // Show/hide store type field based on role
  function toggleStoreType() {
    if (roleSelect.value === 'toko') {
      tipoTokoGroup.style.display = 'block';
      tipoTokoInput.setAttribute('required', 'required');
    } else {
      tipoTokoGroup.style.display = 'none';
      tipoTokoInput.removeAttribute('required');
      tipoTokoInput.value = '';
    }
  }

  // Initialize store type visibility
  toggleStoreType();

  // Listen for role changes
  roleSelect.addEventListener('change', toggleStoreType);

  // Password confirmation validation
  function validatePasswordMatch() {
    if (confirmPasswordInput.value && passwordInput.value !== confirmPasswordInput.value) {
      confirmPasswordInput.setCustomValidity('Passwords do not match');
    } else {
      confirmPasswordInput.setCustomValidity('');
    }
  }

  passwordInput.addEventListener('input', validatePasswordMatch);
  confirmPasswordInput.addEventListener('input', validatePasswordMatch);

  // Form submission validation
  document.querySelector('form').addEventListener('submit', function(e) {
    if (passwordInput.value !== confirmPasswordInput.value) {
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