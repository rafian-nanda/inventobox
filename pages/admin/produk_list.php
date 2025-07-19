<?php
// Prevent any output before session_start
if (ob_get_level()) {
    ob_end_clean();
}

session_start();
require_once '../../database/koneksi.php';

$pageTitle = "Product Management";
$keyword = $_GET['keyword'] ?? '';

// Toggle status via switch
if (isset($_POST['toggle_id'])) {
  $id = (int) $_POST['toggle_id'];
  $get = mysqli_query($conn, "SELECT status FROM produk WHERE id = $id");
  if ($row = mysqli_fetch_assoc($get)) {
    $newStatus = $row['status'] == 1 ? 0 : 1;
    mysqli_query($conn, "UPDATE produk SET status = $newStatus WHERE id = $id");
  }
  header("Location: " . basename(__FILE__));
  exit;
}

// Bulk action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_action'], $_POST['produk_id'])) {
  $action = $_POST['bulk_action'];
  $ids = array_map('intval', $_POST['produk_id']);
  if (!empty($ids)) {
    $idList = implode(',', $ids);
    if ($action === 'aktifkan') {
      mysqli_query($conn, "UPDATE produk SET status = 1 WHERE id IN ($idList)");
    } elseif ($action === 'nonaktifkan') {
      mysqli_query($conn, "UPDATE produk SET status = 0 WHERE id IN ($idList)");
    } elseif ($action === 'hapus') {
      mysqli_query($conn, "DELETE FROM produk WHERE id IN ($idList)");
    }
  }
  header("Location: " . basename(__FILE__));
  exit;
}

// Start output buffering for content
ob_start();
?>

<style>
.professional-header {
  background: linear-gradient(135deg, #1f2937 0%, #374151 50%, #4b5563 100%);
  color: white;
  border-bottom: 3px solid #6b7280;
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

.btn-primary {
  background: linear-gradient(135deg, #374151 0%, #4b5563 100%);
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-size: 0.875rem;
  font-weight: 600;
  transition: all 0.3s ease;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
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
  padding: 0.5rem 1rem;
  border-radius: 6px;
  font-size: 0.875rem;
  font-weight: 500;
  transition: all 0.2s ease;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 0.375rem;
  text-decoration: none;
}

.btn-secondary:hover {
  background: #f9fafb;
  border-color: #9ca3af;
}

.btn-success {
  background: #1f2937;
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  font-size: 0.875rem;
  font-weight: 500;
  transition: all 0.2s ease;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 0.375rem;
}

.btn-success:hover {
  background: #111827;
  transform: translateY(-1px);
}

.btn-warning {
  background: #6b7280;
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  font-size: 0.875rem;
  font-weight: 500;
  transition: all 0.2s ease;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 0.375rem;
}

.btn-warning:hover {
  background: #4b5563;
  transform: translateY(-1px);
}

.btn-danger {
  background: #9ca3af;
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  font-size: 0.875rem;
  font-weight: 500;
  transition: all 0.2s ease;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 0.375rem;
}

.btn-danger:hover {
  background: #6b7280;
  transform: translateY(-1px);
}

.switch {
  position: relative;
  display: inline-block;
  width: 42px;
  height: 22px;
}

.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #d1d5db;
  transition: 0.3s;
  border-radius: 22px;
}

.slider:before {
  position: absolute;
  content: "";
  height: 16px;
  width: 16px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  transition: 0.3s;
  border-radius: 50%;
}

input:checked + .slider {
  background-color: #374151;
}

input:checked + .slider:before {
  transform: translateX(18px);
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

.search-clear-btn {
  position: absolute;
  right: 0.75rem;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: #6b7280;
  cursor: pointer;
  padding: 2px;
  border-radius: 50%;
  display: none;
}

.search-clear-btn:hover {
  background: #f3f4f6;
  color: #374151;
}

.search-clear-btn.show {
  display: block;
}

.dropdown {
  position: relative;
  display: inline-block;
}

.dropdown-toggle {
  background: white;
  color: #374151;
  border: 2px solid #e5e7eb;
  padding: 0.75rem 1rem;
  border-radius: 8px;
  font-size: 0.875rem;
  font-weight: 500;
  transition: all 0.2s ease;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  min-width: 140px;
  justify-content: space-between;
}

.dropdown-toggle:hover {
  background: #f9fafb;
  border-color: #9ca3af;
}

.dropdown-toggle:focus {
  outline: none;
  border-color: #374151;
  box-shadow: 0 0 0 3px rgba(55, 65, 81, 0.1);
}

.dropdown-menu {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  background: white;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
  z-index: 50;
  opacity: 0;
  visibility: hidden;
  transform: translateY(-10px);
  transition: all 0.2s ease;
  margin-top: 4px;
}

.dropdown.open .dropdown-menu {
  opacity: 1;
  visibility: visible;
  transform: translateY(0);
}

.dropdown-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  width: 100%;
  padding: 0.75rem 1rem;
  text-align: left;
  font-size: 0.875rem;
  font-weight: 500;
  color: #374151;
  background: none;
  border: none;
  cursor: pointer;
  transition: all 0.15s ease;
  border-bottom: 1px solid #f3f4f6;
}

.dropdown-item:last-child {
  border-bottom: none;
}

.dropdown-item:hover {
  background: #f9fafb;
  color: #1f2937;
}

.dropdown-item.activate {
  color: #059669;
}

.dropdown-item.activate:hover {
  background: #ecfdf5;
  color: #047857;
}

.dropdown-item.deactivate {
  color: #d97706;
}

.dropdown-item.deactivate:hover {
  background: #fffbeb;
  color: #b45309;
}

.dropdown-item.delete {
  color: #dc2626;
}

.dropdown-item.delete:hover {
  background: #fef2f2;
  color: #b91c1c;
}

.dropdown-chevron {
  transition: transform 0.2s ease;
}

.dropdown.open .dropdown-chevron {
  transform: rotate(180deg);
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
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
            </svg>
            <h1 class="text-3xl font-bold leading-tight">Product Management</h1>
          </div>
          <p class="text-lg text-gray-300">Manage your product inventory and pricing</p>
        </div>
        <div class="mt-4 sm:mt-0">
          <a href="tambah_produk.php" class="btn-primary">
            <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add Product
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Main Content -->
<div class="px-4 sm:px-6 lg:px-8 py-8 bg-gray-50 min-h-screen">
  <div class="max-w-7xl mx-auto">

    <!-- Controls Panel -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
      
      <!-- Column Visibility Controls -->
      <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
        <h4 class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-3">
          <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
          </svg>
          Column Visibility
        </h4>
        <div class="flex flex-wrap gap-4">
          <div class="flex items-center gap-2">
            <input type="checkbox" class="toggle-col w-4 h-4 text-gray-600 border-2 border-gray-300 rounded focus:ring-gray-500" data-col="foto" id="col-foto">
            <label for="col-foto" class="text-sm text-gray-700 cursor-pointer">Photo</label>
          </div>
          <div class="flex items-center gap-2">
            <input type="checkbox" class="toggle-col w-4 h-4 text-gray-600 border-2 border-gray-300 rounded focus:ring-gray-500" data-col="sku" id="col-sku">
            <label for="col-sku" class="text-sm text-gray-700 cursor-pointer">SKU</label>
          </div>
          <div class="flex items-center gap-2">
            <input type="checkbox" class="toggle-col w-4 h-4 text-gray-600 border-2 border-gray-300 rounded focus:ring-gray-500" data-col="barcode" id="col-barcode">
            <label for="col-barcode" class="text-sm text-gray-700 cursor-pointer">Barcode</label>
          </div>
          <div class="flex items-center gap-2">
            <input type="checkbox" class="toggle-col w-4 h-4 text-gray-600 border-2 border-gray-300 rounded focus:ring-gray-500" data-col="harga_beli" id="col-harga-beli">
            <label for="col-harga-beli" class="text-sm text-gray-700 cursor-pointer">Buy Price</label>
          </div>
        </div>
      </div>

      <!-- Search and Actions -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        
        <!-- Search Form -->
        <div class="search-container">
          <form method="GET" id="searchForm" class="relative">
            <svg class="search-icon icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input type="text" 
                   name="keyword" 
                   id="searchInput"
                   placeholder="Search products..." 
                   value="<?= htmlspecialchars($keyword) ?>" 
                   class="search-input"
                   autocomplete="off">
            <button type="button" class="search-clear-btn" id="clearSearch">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </form>
        </div>

        <!-- Bulk Actions Dropdown -->
        <div class="flex flex-col sm:flex-row items-center gap-2">
          <div class="dropdown" id="bulkActionsDropdown">
            <button type="button" class="dropdown-toggle" id="dropdownToggle">
              <div class="flex items-center gap-2">
                <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
                Bulk Actions
              </div>
              <svg class="icon-sm dropdown-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
              </svg>
            </button>
            <div class="dropdown-menu" id="dropdownMenu">
              <form method="POST" id="bulkForm">
                <button type="button" class="dropdown-item activate" onclick="submitBulkAction('aktifkan')">
                  <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  Activate Selected
                </button>
                <button type="button" class="dropdown-item deactivate" onclick="submitBulkAction('nonaktifkan')">
                  <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  Deactivate Selected
                </button>
                <button type="button" class="dropdown-item delete" onclick="submitBulkAction('hapus')">
                  <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                  </svg>
                  Delete Selected
                </button>
                <input type="hidden" name="bulk_action" id="bulk_action">
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Product Table with Tailwind CSS -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gradient-to-r from-gray-800 to-gray-600">
            <tr>
              <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-white uppercase tracking-wider w-12">
                <input type="checkbox" onclick="toggleAll(this)" class="w-4 h-4 text-gray-600 border-2 border-gray-300 rounded focus:ring-gray-500">
              </th>
              <th scope="col" class="col-foto px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                <div class="flex items-center gap-2">
                  <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                  </svg>
                  Photo
                </div>
              </th>
              <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider min-w-48">
                <div class="flex items-center gap-2">
                  <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                  </svg>
                  Product Name
                </div>
              </th>
              <th scope="col" class="col-sku px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider min-w-28">SKU</th>
              <th scope="col" class="col-barcode px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider min-w-32">Barcode</th>
              <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider min-w-32">
                <div class="flex items-center gap-2">
                  <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                  </svg>
                  Category
                </div>
              </th>
              <th scope="col" class="col-harga_beli px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider min-w-32">Buy Price</th>
              <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider min-w-32">Sell Price</th>
              <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider min-w-24">Unit</th>
              <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider min-w-24">
                <div class="flex items-center gap-2">
                  <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                  </svg>
                  Stock
                </div>
              </th>
              <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-white uppercase tracking-wider min-w-24">
                <div class="flex items-center gap-2 justify-center">
                  <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  Status
                </div>
              </th>
              <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-white uppercase tracking-wider min-w-36">
                <div class="flex items-center gap-2 justify-center">
                  <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                  </svg>
                  Actions
                </div>
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <?php
            $sql = "SELECT produk.*, kategori.nama_kategori AS kategori_nama
                    FROM produk
                    LEFT JOIN kategori ON produk.kategori_id = kategori.id";

            if ($keyword) {
              $keywordEsc = mysqli_real_escape_string($conn, $keyword);
              $sql .= " WHERE produk.nama_produk LIKE '%$keywordEsc%'";
            }

            $sql .= " ORDER BY produk.nama_produk ASC";
            $query = mysqli_query($conn, $sql);

            if (mysqli_num_rows($query) === 0) {
              echo "<tr><td colspan='12' class='px-6 py-8 text-center'>
                      <div class='text-gray-400'>
                        <svg class='w-16 h-16 mx-auto mb-4' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
                          <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'></path>
                        </svg>
                        <h3 class='text-lg font-semibold text-gray-700 mb-2'>No products found</h3>
                        <p class='text-sm text-gray-500'>Try adjusting your search criteria or add new products.</p>
                      </div>
                    </td></tr>";
            }

            while ($row = mysqli_fetch_assoc($query)) {
              $status = $row['status'] == 1;
              $satuanText = $row['satuan'];
              if ($row['satuan'] === 'paket' && $row['isi_per_paket']) {
                $satuanText .= " ({$row['isi_per_paket']} pcs)";
              }

              echo "<tr class='hover:bg-gray-50 transition-colors duration-150'>
                <td class='px-4 py-3 text-center'>
                  <input type='checkbox' name='produk_id[]' value='{$row['id']}' form='bulkForm' class='w-4 h-4 text-gray-600 border-2 border-gray-300 rounded focus:ring-gray-500'>
                </td>
                <td class='col-foto px-4 py-3'>
                  <img src='../../uploads/{$row['foto']}' alt='Product' class='w-12 h-12 rounded-lg object-cover border border-gray-200'>
                </td>
                <td class='px-4 py-3 text-sm font-medium text-gray-900'>" . htmlspecialchars($row['nama_produk']) . "</td>
                <td class='col-sku px-4 py-3 text-sm text-gray-600'>" . htmlspecialchars($row['sku']) . "</td>
                <td class='col-barcode px-4 py-3 text-sm text-gray-600'>" . htmlspecialchars($row['barcode'] ?: '-') . "</td>
                <td class='px-4 py-3 text-sm text-gray-600'>" . htmlspecialchars($row['kategori_nama'] ?? '-') . "</td>
                <td class='col-harga_beli px-4 py-3 text-sm font-medium text-gray-900'>Rp " . number_format($row['harga_beli'], 0, ',', '.') . "</td>
                <td class='px-4 py-3 text-sm font-medium text-gray-900'>Rp " . number_format($row['harga_jual'], 0, ',', '.') . "</td>
                <td class='px-4 py-3 text-sm text-gray-600'>" . htmlspecialchars($satuanText) . "</td>
                <td class='px-4 py-3 text-sm font-medium text-gray-900'>" . number_format($row['stok']) . "</td>
                <td class='px-4 py-3 text-center'>
                  <label class='switch'>
                    <input type='checkbox' class='status-switch' data-id='{$row['id']}' " . ($status ? 'checked' : '') . ">
                    <span class='slider'></span>
                  </label>
                </td>
                <td class='px-4 py-3 text-center'>
                  <div class='flex items-center justify-center gap-2'>
                    <a href='edit_produk.php?id={$row['id']}' 
                       class='inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors duration-150'>
                      <svg class='icon-sm' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
                        <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'></path>
                      </svg>
                      Edit
                    </a>
                    <a href='hapus_produk.php?id={$row['id']}' 
                       class='inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-red-600 bg-white border border-gray-300 rounded-md hover:bg-red-50 hover:border-red-300 transition-colors duration-150' 
                       onclick='return confirm(\"Are you sure you want to delete this product?\")'>
                      <svg class='icon-sm' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
                        <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16'></path>
                      </svg>
                      Delete
                    </a>
                  </div>
                </td>
              </tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
// Status switch AJAX
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.status-switch').forEach(function(sw) {
    sw.addEventListener('change', function(e) {
      const id = this.dataset.id;
      const checked = this.checked;
      this.disabled = true;
      fetch('../../ajax/toggle_status.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + encodeURIComponent(id)
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          this.checked = data.status == 1;
        } else {
          alert('Gagal update status!');
          this.checked = !checked;
        }
      })
      .catch(() => {
        alert('Gagal koneksi server!');
        this.checked = !checked;
      })
      .finally(() => {
        this.disabled = false;
      });
    });
  });
});

function toggleAll(source) {
  document.querySelectorAll('input[name="produk_id[]"]').forEach(cb => cb.checked = source.checked);
}

function submitBulkAction(action) {
  const selected = document.querySelectorAll('input[name="produk_id[]"]:checked');
  if (selected.length === 0) {
    alert('Please select at least one product first.');
    return;
  }
  
  let confirmMessage = '';
  switch(action) {
    case 'aktifkan':
      confirmMessage = `Activate ${selected.length} selected products?`;
      break;
    case 'nonaktifkan':
      confirmMessage = `Deactivate ${selected.length} selected products?`;
      break;
    case 'hapus':
      confirmMessage = `Delete ${selected.length} selected products? This action cannot be undone.`;
      break;
  }
  
  if (confirm(confirmMessage)) {
    document.getElementById('bulk_action').value = action;
    document.getElementById('bulkForm').submit();
  }
}

document.addEventListener('DOMContentLoaded', function() {
  const searchInput = document.getElementById('searchInput');
  const clearButton = document.getElementById('clearSearch');
  const searchForm = document.getElementById('searchForm');
  const dropdown = document.getElementById('bulkActionsDropdown');
  const dropdownToggle = document.getElementById('dropdownToggle');
  const dropdownMenu = document.getElementById('dropdownMenu');
  
  // Dropdown functionality
  function toggleDropdown() {
    dropdown.classList.toggle('open');
  }
  
  function closeDropdown() {
    dropdown.classList.remove('open');
  }
  
  // Dropdown toggle click
  dropdownToggle.addEventListener('click', function(e) {
    e.preventDefault();
    e.stopPropagation();
    toggleDropdown();
  });
  
  // Close dropdown when clicking outside
  document.addEventListener('click', function(e) {
    if (!dropdown.contains(e.target)) {
      closeDropdown();
    }
  });
  
  // Close dropdown when pressing Escape
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      closeDropdown();
    }
  });
  
  // Prevent dropdown from closing when clicking inside menu
  dropdownMenu.addEventListener('click', function(e) {
    // Only prevent if it's not a button click (let the action execute)
    if (e.target.tagName !== 'BUTTON') {
      e.stopPropagation();
    } else {
      // Close dropdown after action
      setTimeout(closeDropdown, 100);
    }
  });
  
  // Update clear button visibility
  function updateClearButton() {
    if (searchInput.value.length > 0) {
      clearButton.classList.add('show');
    } else {
      clearButton.classList.remove('show');
    }
  }
  
  // Initialize clear button state
  updateClearButton();
  
  // Clear search functionality
  clearButton.addEventListener('click', function() {
    searchInput.value = '';
    updateClearButton();
    searchForm.submit();
  });
  
  // Update clear button on input
  searchInput.addEventListener('input', updateClearButton);
  
  // Manual search on Enter key
  searchInput.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
      e.preventDefault();
      searchForm.submit();
    }
  });
  
  // Column visibility toggle
  document.querySelectorAll('.toggle-col').forEach(function(checkbox) {
    // Set initial state - hide columns that are unchecked
    const colClass = 'col-' + checkbox.dataset.col;
    const isChecked = checkbox.checked;
    
    document.querySelectorAll('.' + colClass).forEach(function(el) {
      el.style.display = isChecked ? '' : 'none';
    });
    
    // Add change event listener
    checkbox.addEventListener('change', function() {
      const colClass = 'col-' + this.dataset.col;
      const isChecked = this.checked;
      
      document.querySelectorAll('.' + colClass).forEach(function(el) {
        el.style.display = isChecked ? '' : 'none';
      });
    });
  });

  // Table row interactions
  const tableRows = document.querySelectorAll('tbody tr');
  tableRows.forEach(row => {
    row.addEventListener('click', function(e) {
      // Don't trigger on checkbox, switch, or link clicks
      if (e.target.tagName === 'INPUT' || e.target.tagName === 'A' || e.target.closest('.switch') || e.target.closest('a')) {
        return;
      }
      
      // Toggle checkbox when clicking row
      const checkbox = row.querySelector('input[name="produk_id[]"]');
      if (checkbox) {
        checkbox.checked = !checkbox.checked;
      }
    });
  });

  // Keyboard shortcuts
  document.addEventListener('keydown', function(e) {
    // Don't trigger shortcuts when dropdown is open
    if (dropdown.classList.contains('open')) {
      return;
    }
    
    // Ctrl+A to select all (only when not focused on input)
    if (e.ctrlKey && e.key === 'a' && e.target.tagName !== 'INPUT') {
      e.preventDefault();
      const checkboxes = document.querySelectorAll('input[name="produk_id[]"]');
      const allChecked = Array.from(checkboxes).every(cb => cb.checked);
      checkboxes.forEach(cb => cb.checked = !allChecked);
      
      // Update master checkbox
      const masterCheckbox = document.querySelector('thead input[type="checkbox"]');
      if (masterCheckbox) {
        masterCheckbox.checked = !allChecked;
      }
    }
    
    // Delete key for bulk delete (only when not focused on input)
    if (e.key === 'Delete' && e.target.tagName !== 'INPUT') {
      const selected = document.querySelectorAll('input[name="produk_id[]"]:checked');
      if (selected.length > 0) {
        submitBulkAction('hapus');
      }
    }
    
    // Escape key to clear search when focused on search input
    if (e.key === 'Escape' && e.target === searchInput) {
      searchInput.value = '';
      updateClearButton();
      searchForm.submit();
    }
    
    // Ctrl+F to focus search
    if (e.ctrlKey && e.key === 'f') {
      e.preventDefault();
      searchInput.focus();
      searchInput.select();
    }
  });
  
  // Auto-focus search input with slash key
  document.addEventListener('keydown', function(e) {
    if (e.key === '/' && e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA') {
      e.preventDefault();
      searchInput.focus();
    }
  });
});
</script>

<?php
$content = ob_get_clean();
include_once(__DIR__ . '/../../layouts/layout.php');
?>