<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

require_once '../../database/koneksi.php';
$pageTitle = "Add New Product";
$error = '';
$success = '';

// Proses insert
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_produk = trim($_POST['nama_produk']);
    $sku = trim($_POST['sku']);
    $barcode = trim($_POST['barcode']);
    $kategori_id = $_POST['kategori_id_final'];
    $harga_beli = $_POST['harga_beli'];
    $harga_jual = $_POST['harga_jual'];
    $satuan = $_POST['satuan'];
    $stok = $_POST['stok'];
    $deskripsi = trim($_POST['deskripsi']);
    $status = isset($_POST['status']) ? 1 : 0;

    // Validation
    if (empty($nama_produk) || empty($sku) || empty($kategori_id) || empty($harga_beli) || empty($harga_jual)) {
        $error = 'All required fields must be filled';
    } elseif ($harga_beli <= 0 || $harga_jual <= 0) {
        $error = 'Prices must be greater than zero';
    } elseif ($harga_jual <= $harga_beli) {
        $error = 'Selling price must be higher than buying price';
    } else {
        // Check if SKU already exists
        $check_query = "SELECT id FROM produk WHERE sku = ?";
        $check_stmt = mysqli_prepare($conn, $check_query);
        mysqli_stmt_bind_param($check_stmt, "s", $sku);
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);

        if (mysqli_num_rows($check_result) > 0) {
            $error = 'SKU already exists';
        } else {
            // Upload gambar
            $foto = '';
            if (!empty($_FILES['foto']['name'])) {
                $target = "../../uploads/";
                if (!is_dir($target)) {
                    mkdir($target, 0755, true);
                }
                $filename = time() . '_' . basename($_FILES['foto']['name']);
                if (move_uploaded_file($_FILES['foto']['tmp_name'], $target . $filename)) {
                    $foto = $filename;
                } else {
                    $error = 'Failed to upload image';
                }
            }

            if (empty($error)) {
                $sql = "INSERT INTO produk (nama_produk, sku, barcode, kategori_id, harga_beli, harga_jual, satuan, stok, foto, deskripsi, status)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "sssiidsissi", $nama_produk, $sku, $barcode, $kategori_id, $harga_beli, $harga_jual, $satuan, $stok, $foto, $deskripsi, $status);

                if (mysqli_stmt_execute($stmt)) {
                    $success = 'Product added successfully!';
                    // Redirect after success
                    header('refresh:2;url=index.php');
                } else {
                    $error = 'Failed to add product: ' . mysqli_error($conn);
                }
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

    .form-textarea {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        font-size: 0.875rem;
        background-color: white;
        transition: all 0.2s ease;
        resize: vertical;
        min-height: 80px;
    }

    .form-textarea:focus {
        outline: none;
        border-color: #374151;
        box-shadow: 0 0 0 3px rgba(55, 65, 81, 0.1);
    }

    .form-file {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px dashed #d1d5db;
        border-radius: 8px;
        font-size: 0.875rem;
        background-color: #f9fafb;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .form-file:hover {
        border-color: #9ca3af;
        background-color: #f3f4f6;
    }

    .form-file:focus {
        outline: none;
        border-color: #374151;
        box-shadow: 0 0 0 3px rgba(55, 65, 81, 0.1);
    }

    .checkbox-group {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .checkbox {
        width: 1.25rem;
        height: 1.25rem;
        border: 2px solid #d1d5db;
        border-radius: 4px;
        cursor: pointer;
        background-color: white;
    }

    .checkbox:checked {
        background-color: #374151;
        border-color: #374151;
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

    .price-section {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .price-section h3 {
        font-size: 1rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .category-section {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .category-section h3 {
        font-size: 1rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .category-chain {
        display: flex;
        flex-direction: column;
        gap: 1rem;
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

        .form-container {
            margin: 0 1rem;
            padding: 1.5rem;
        }

        .form-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
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

        .price-section,
        .category-section {
            padding: 1.25rem;
        }

        .category-chain {
            gap: 0.75rem;
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
        .form-select,
        .form-textarea {
            font-size: 16px;
            /* Prevents zoom on iOS */
        }

        .price-section,
        .category-section {
            padding: 1rem;
        }
    }

    /* Touch-friendly improvements */
    @media (hover: none) and (pointer: coarse) {

        .btn-primary,
        .btn-secondary {
            min-height: 48px;
        }

        .form-input,
        .form-select,
        .form-textarea {
            min-height: 44px;
        }

        .checkbox {
            width: 1.5rem;
            height: 1.5rem;
        }
    }
</style>

<!-- Page Header -->
<div class="professional-header">
    <div class="px-4 sm:px-6 lg:px-8 py-8">
        <div class="max-w-4xl mx-auto">
            <div class="flex items-center gap-3 mb-2">
                <svg class="icon-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <h1 class="text-3xl font-bold leading-tight">Add New Product</h1>
            </div>
            <p class="text-lg text-gray-300">Create a new product with details and pricing</p>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="px-4 sm:px-6 lg:px-8 py-8 bg-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto">

        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <a href="index.php">Product Management</a>
            <svg class="icon-sm breadcrumb-separator" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
            <span>Add Product</span>
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
            <form method="POST" enctype="multipart/form-data" novalidate>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 form-grid">

                    <!-- Product Name -->
                    <div class="form-group md:col-span-1">
                        <label for="nama_produk" class="form-label required">Product Name</label>
                        <input type="text"
                            id="nama_produk"
                            name="nama_produk"
                            class="form-input"
                            value="<?= isset($_POST['nama_produk']) ? htmlspecialchars($_POST['nama_produk']) : '' ?>"
                            placeholder="Enter product name"
                            required>
                        <div class="form-help">Clear and descriptive product name</div>
                    </div>

                    <!-- SKU -->
                    <div class="form-group md:col-span-1">
                        <label for="sku" class="form-label required">SKU</label>
                        <input type="text"
                            id="sku"
                            name="sku"
                            class="form-input"
                            value="<?= isset($_POST['sku']) ? htmlspecialchars($_POST['sku']) : '' ?>"
                            placeholder="e.g., PRD-001"
                            required>
                        <div class="form-help">Unique stock keeping unit identifier</div>
                    </div>

                    <!-- Barcode -->
                    <div class="form-group md:col-span-1">
                        <label for="barcode" class="form-label">Barcode</label>
                        <input type="text"
                            id="barcode"
                            name="barcode"
                            class="form-input"
                            value="<?= isset($_POST['barcode']) ? htmlspecialchars($_POST['barcode']) : '' ?>"
                            placeholder="Optional barcode">
                        <div class="form-help">Product barcode if available</div>
                    </div>

                    <!-- Unit -->
                    <div class="form-group md:col-span-1">
                        <label for="satuan" class="form-label required">Unit</label>
                        <select id="satuan" name="satuan" class="form-select" required>
                            <option value="pcs" <?= (isset($_POST['satuan']) && $_POST['satuan'] === 'pcs') ? 'selected' : '' ?>>Pieces</option>
                            <option value="lusin" <?= (isset($_POST['satuan']) && $_POST['satuan'] === 'lusin') ? 'selected' : '' ?>>Dozen</option>
                            <option value="dus" <?= (isset($_POST['satuan']) && $_POST['satuan'] === 'dus') ? 'selected' : '' ?>>Box</option>
                            <option value="kg" <?= (isset($_POST['satuan']) && $_POST['satuan'] === 'kg') ? 'selected' : '' ?>>Kilogram</option>
                            <option value="liter" <?= (isset($_POST['satuan']) && $_POST['satuan'] === 'liter') ? 'selected' : '' ?>>Liter</option>
                            <option value="paket_2pcs" <?= (isset($_POST['satuan']) && $_POST['satuan'] === 'paket_2pcs') ? 'selected' : '' ?>>Paket (2 pcs)</option>
                        </select>
                        <div class="form-help">Select measurement unit</div>
                    </div>

                </div>

                <!-- Category Section -->
                <div class="category-section">
                    <h3>
                        <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        Product Category
                    </h3>

                    <div class="category-chain">
                        <div class="form-group">
                            <label for="kategori_utama" class="form-label required">Main Category</label>
                            <select id="kategori_utama" class="form-select" required>
                                <option value="">-- Select Main Category --</option>
                                <?php
                                $katUtama = mysqli_query($conn, "SELECT * FROM kategori WHERE parent_id IS NULL ORDER BY nama_kategori");
                                while ($row = mysqli_fetch_assoc($katUtama)) {
                                    echo "<option value='{$row['id']}'>{$row['nama_kategori']}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="subkategori_1" class="form-label">Subcategory Level 1</label>
                            <select id="subkategori_1" class="form-select" style="display:none;">
                                <option value="">-- Select Subcategory --</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="subkategori_2" class="form-label">Subcategory Level 2</label>
                            <select id="subkategori_2" class="form-select" style="display:none;">
                                <option value="">-- Select Subcategory --</option>
                            </select>
                        </div>
                    </div>

                    <input type="hidden" name="kategori_id_final" id="kategori_id_final">
                </div>

                <!-- Price Section -->
                <div class="price-section">
                    <h3>
                        <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                        Pricing Information
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="form-group">
                            <label for="harga_beli" class="form-label required">Buy Price</label>
                            <input type="number"
                                id="harga_beli"
                                name="harga_beli"
                                class="form-input"
                                value="<?= isset($_POST['harga_beli']) ? $_POST['harga_beli'] : '' ?>"
                                placeholder="0"
                                step="0.01"
                                min="0"
                                required>
                            <div class="form-help">Cost price from supplier</div>
                        </div>

                        <div class="form-group">
                            <label for="harga_jual" class="form-label required">Sell Price</label>
                            <input type="number"
                                id="harga_jual"
                                name="harga_jual"
                                class="form-input"
                                value="<?= isset($_POST['harga_jual']) ? $_POST['harga_jual'] : '' ?>"
                                placeholder="0"
                                step="0.01"
                                min="0"
                                required>
                            <div class="form-help">Selling price to customers</div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 form-grid">

                    <!-- Initial Stock -->
                    <div class="form-group md:col-span-1">
                        <label for="stok" class="form-label">Initial Stock</label>
                        <input type="number"
                            id="stok"
                            name="stok"
                            class="form-input"
                            value="<?= isset($_POST['stok']) ? $_POST['stok'] : '0' ?>"
                            placeholder="0"
                            min="0">
                        <div class="form-help">Starting inventory quantity</div>
                    </div>

                    <!-- Status -->
                    <div class="form-group md:col-span-1">
                        <label class="form-label">Product Status</label>
                        <div class="checkbox-group">
                            <input type="checkbox" id="status" name="status" value="1" class="checkbox" <?= (!isset($_POST['status']) || $_POST['status']) ? 'checked' : '' ?>>
                            <label for="status" class="text-sm font-medium text-gray-700">Active Product</label>
                        </div>
                        <div class="form-help">Enable this product for sale</div>
                    </div>

                    <!-- Product Image -->
                    <div class="form-group md:col-span-2">
                        <label for="foto" class="form-label">Product Image</label>
                        <input type="file"
                            id="foto"
                            name="foto"
                            class="form-file"
                            accept="image/*">
                        <div class="form-help">Upload product photo (JPG, PNG, max 5MB)</div>
                    </div>

                    <!-- Description -->
                    <div class="form-group md:col-span-2">
                        <label for="deskripsi" class="form-label">Description</label>
                        <textarea id="deskripsi"
                            name="deskripsi"
                            class="form-textarea"
                            placeholder="Product description, features, specifications..."><?= isset($_POST['deskripsi']) ? htmlspecialchars($_POST['deskripsi']) : '' ?></textarea>
                        <div class="form-help">Detailed product information for customers</div>
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Save Product</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function loadSubkategori(parentId, targetId) {
        fetch("/inventobox/ajax/get_subkategori.php?parent_id=" + parentId)
            .then(res => res.json())
            .then(data => {
                const select = document.getElementById(targetId);
                select.innerHTML = '<option value="">-- Select Subcategory --</option>';

                if (data.length > 0) {
                    select.style.display = 'block';
                    select.parentElement.style.display = 'block';
                    data.forEach(item => {
                        const opt = document.createElement('option');
                        opt.value = item.id;
                        opt.textContent = item.nama_kategori;
                        select.appendChild(opt);
                    });
                } else {
                    select.style.display = 'none';
                    select.parentElement.style.display = 'none';
                }

                select.onchange = () => {
                    document.getElementById('kategori_id_final').value = select.value || parentId;
                    if (targetId === 'subkategori_1') {
                        loadSubkategori(select.value, 'subkategori_2');
                        // Hide subkategori_2 if subkategori_1 is empty
                        if (!select.value) {
                            document.getElementById('subkategori_2').style.display = 'none';
                            document.getElementById('subkategori_2').parentElement.style.display = 'none';
                        }
                    }
                };
            })
            .catch(error => {
                console.error('Error loading subcategories:', error);
            });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const kategoriUtama = document.getElementById('kategori_utama');
        const hargaBeli = document.getElementById('harga_beli');
        const hargaJual = document.getElementById('harga_jual');

        // Category selection
        kategoriUtama.onchange = function() {
            document.getElementById('kategori_id_final').value = this.value;
            if (this.value) {
                loadSubkategori(this.value, 'subkategori_1');
            } else {
                // Hide all subcategory selects
                document.getElementById('subkategori_1').style.display = 'none';
                document.getElementById('subkategori_1').parentElement.style.display = 'none';
                document.getElementById('subkategori_2').style.display = 'none';
                document.getElementById('subkategori_2').parentElement.style.display = 'none';
            }
        };

        // Price validation
        function validatePrices() {
            const buyPrice = parseFloat(hargaBeli.value) || 0;
            const sellPrice = parseFloat(hargaJual.value) || 0;

            if (buyPrice > 0 && sellPrice > 0 && sellPrice <= buyPrice) {
                hargaJual.setCustomValidity('Selling price must be higher than buying price');
            } else {
                hargaJual.setCustomValidity('');
            }
        }

        hargaBeli.addEventListener('input', validatePrices);
        hargaJual.addEventListener('input', validatePrices);

        // Form submission validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const kategoriId = document.getElementById('kategori_id_final').value;
            if (!kategoriId) {
                e.preventDefault();
                alert('Please select a product category');
                kategoriUtama.focus();
                return;
            }

            const buyPrice = parseFloat(hargaBeli.value) || 0;
            const sellPrice = parseFloat(hargaJual.value) || 0;

            if (buyPrice <= 0) {
                e.preventDefault();
                alert('Buy price must be greater than zero');
                hargaBeli.focus();
                return;
            }

            if (sellPrice <= 0) {
                e.preventDefault();
                alert('Sell price must be greater than zero');
                hargaJual.focus();
                return;
            }

            if (sellPrice <= buyPrice) {
                e.preventDefault();
                alert('Selling price must be higher than buying price');
                hargaJual.focus();
                return;
            }
        });

        // File upload preview
        const fotoInput = document.getElementById('foto');
        fotoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.size > 5 * 1024 * 1024) { // 5MB limit
                    alert('File size must be less than 5MB');
                    this.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    // Create preview if needed
                    console.log('Image selected:', file.name);
                };
                reader.readAsDataURL(file);
            }
        });

        // Auto-generate SKU based on product name (optional)
        const namaProduct = document.getElementById('nama_produk');
        const skuInput = document.getElementById('sku');

        // Convert product name to Title Case
        namaProduct.addEventListener('input', function() {
            const cursorPosition = this.selectionStart;
            const titleCase = this.value.toLowerCase().replace(/\b\w/g, function(char) {
                return char.toUpperCase();
            });
            this.value = titleCase;
            this.setSelectionRange(cursorPosition, cursorPosition);
        });

        // Convert SKU to uppercase
        skuInput.addEventListener('input', function() {
            const cursorPosition = this.selectionStart;
            this.value = this.value.toUpperCase();
            this.setSelectionRange(cursorPosition, cursorPosition);
        });

        // Auto-generate SKU based on product name
        namaProduct.addEventListener('blur', function() {
            if (!skuInput.value && this.value) {
                const sku = this.value
                    .toUpperCase()
                    .replace(/[^A-Z0-9]/g, '')
                    .substring(0, 6) + '-' + Math.floor(Math.random() * 1000).toString().padStart(3, '0');
                skuInput.value = sku;
            }
        });
    });
</script>

<?php
$content = ob_get_clean();
include_once(__DIR__ . '/../../layouts/layout.php');
?>