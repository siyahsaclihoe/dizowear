<?php
/**
 * Dızo Wear - Admin Categories
 */

session_start();
if (!isset($_SESSION['admin'])) { header('Location: login.php'); exit; }

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/helpers/functions.php';
require_once __DIR__ . '/../app/models/Category.php';

$categoryModel = new Category();

// Silme
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $categoryModel->delete((int)$_GET['delete']);
    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Kategori silindi.'];
    header('Location: categories.php');
    exit;
}

// Ekleme/Güncelleme
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'name' => trim($_POST['name'] ?? ''),
        'slug' => slug($_POST['name'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'sort_order' => (int) ($_POST['sort_order'] ?? 0),
        'status' => $_POST['status'] ?? 'active',
    ];
    
    $editId = (int) ($_POST['edit_id'] ?? 0);
    
    if ($editId > 0) {
        $categoryModel->update($editId, $data);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Kategori güncellendi.'];
    } else {
        $categoryModel->create($data);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Kategori eklendi.'];
    }
    
    header('Location: categories.php');
    exit;
}

$categories = $categoryModel->all('sort_order', 'ASC');

$pageTitle = 'Kategoriler';
include 'views/layouts/header.php';
?>

<div class="row g-4">
    <!-- Form -->
    <div class="col-lg-4">
        <div class="admin-card">
            <div class="card-header">
                <h5 class="mb-0" id="formTitle">Yeni Kategori</h5>
            </div>
            <div class="card-body">
                <form method="POST" id="categoryForm">
                    <input type="hidden" name="edit_id" id="editId" value="0">
                    
                    <div class="mb-3">
                        <label class="form-label">Kategori Adı *</label>
                        <input type="text" name="name" id="catName" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Açıklama</label>
                        <textarea name="description" id="catDesc" class="form-control" rows="3"></textarea>
                    </div>
                    
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label">Sıra</label>
                            <input type="number" name="sort_order" id="sortOrder" class="form-control" value="0">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Durum</label>
                            <select name="status" id="catStatus" class="form-select">
                                <option value="active">Aktif</option>
                                <option value="inactive">Pasif</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <button type="submit" class="btn btn-dark w-100">
                            <i class="bi bi-check-circle me-2"></i><span id="submitText">Ekle</span>
                        </button>
                        <button type="button" class="btn btn-outline-secondary w-100 mt-2 d-none" id="cancelEdit" onclick="resetForm()">
                            İptal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- List -->
    <div class="col-lg-8">
        <div class="admin-card">
            <div class="card-header">
                <h5 class="mb-0">Kategori Listesi</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Kategori</th>
                                <th>Sıra</th>
                                <th>Durum</th>
                                <th width="100">İşlem</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($categories)): ?>
                                <tr><td colspan="4" class="text-center py-4 text-muted">Kategori yok</td></tr>
                            <?php else: ?>
                                <?php foreach ($categories as $cat): ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($cat['name']) ?></strong></td>
                                        <td><?= $cat['sort_order'] ?></td>
                                        <td>
                                            <span class="badge bg-<?= $cat['status'] === 'active' ? 'success' : 'secondary' ?>">
                                                <?= $cat['status'] === 'active' ? 'Aktif' : 'Pasif' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    onclick="editCategory(<?= htmlspecialchars(json_encode($cat)) ?>)">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <a href="?delete=<?= $cat['id'] ?>" class="btn btn-sm btn-outline-danger"
                                               onclick="return confirm('Silmek istediğinize emin misiniz?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function editCategory(cat) {
    document.getElementById('editId').value = cat.id;
    document.getElementById('catName').value = cat.name;
    document.getElementById('catDesc').value = cat.description || '';
    document.getElementById('sortOrder').value = cat.sort_order;
    document.getElementById('catStatus').value = cat.status;
    document.getElementById('formTitle').textContent = 'Kategori Düzenle';
    document.getElementById('submitText').textContent = 'Güncelle';
    document.getElementById('cancelEdit').classList.remove('d-none');
}

function resetForm() {
    document.getElementById('categoryForm').reset();
    document.getElementById('editId').value = '0';
    document.getElementById('formTitle').textContent = 'Yeni Kategori';
    document.getElementById('submitText').textContent = 'Ekle';
    document.getElementById('cancelEdit').classList.add('d-none');
}
</script>

<?php include 'views/layouts/footer.php'; ?>
