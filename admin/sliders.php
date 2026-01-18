<?php
/**
 * Dızo Wear - Admin Sliders
 */

session_start();
if (!isset($_SESSION['admin'])) { header('Location: login.php'); exit; }

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/helpers/functions.php';
require_once __DIR__ . '/../app/models/Slider.php';

$sliderModel = new Slider();

// Silme işlemi
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $slider = $sliderModel->find((int)$_GET['delete']);
    if ($slider && $slider['image']) {
        deleteFile($slider['image']);
    }
    $sliderModel->delete((int)$_GET['delete']);
    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Slider silindi.'];
    header('Location: sliders.php');
    exit;
}

// Ekleme/Güncelleme
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'title' => trim($_POST['title'] ?? ''),
        'subtitle' => trim($_POST['subtitle'] ?? ''),
        'button_text' => trim($_POST['button_text'] ?? ''),
        'button_link' => trim($_POST['button_link'] ?? ''),
        'sort_order' => (int) ($_POST['sort_order'] ?? 0),
        'status' => $_POST['status'] ?? 'active',
    ];
    
    // Görsel yükleme
    if (!empty($_FILES['image']['name'])) {
        $imagePath = uploadFile($_FILES['image'], 'sliders');
        if ($imagePath) {
            $data['image'] = $imagePath;
        }
    }
    
    $editId = (int) ($_POST['edit_id'] ?? 0);
    
    if ($editId > 0) {
        $sliderModel->update($editId, $data);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Slider güncellendi.'];
    } else {
        if (empty($data['image'])) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Görsel gereklidir.'];
        } else {
            $sliderModel->create($data);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Slider eklendi.'];
        }
    }
    
    header('Location: sliders.php');
    exit;
}

$sliders = $sliderModel->all('sort_order', 'ASC');

$pageTitle = 'Slider Yönetimi';
include 'views/layouts/header.php';
?>

<div class="row g-4">
    <!-- Slider Form -->
    <div class="col-lg-4">
        <div class="admin-card">
            <div class="card-header">
                <h5 class="mb-0" id="formTitle">Yeni Slider Ekle</h5>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data" id="sliderForm">
                    <input type="hidden" name="edit_id" id="editId" value="0">
                    
                    <div class="mb-3">
                        <label class="form-label">Başlık *</label>
                        <input type="text" name="title" id="title" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Alt Başlık</label>
                        <input type="text" name="subtitle" id="subtitle" class="form-control">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Görsel *</label>
                        <input type="file" name="image" class="form-control" accept="image/*" id="imageInput">
                        <small class="text-muted">Önerilen: 1920x800 px</small>
                    </div>
                    
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label">Buton Metni</label>
                            <input type="text" name="button_text" id="buttonText" class="form-control" placeholder="Keşfet">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Buton Linki</label>
                            <input type="text" name="button_link" id="buttonLink" class="form-control" placeholder="/products">
                        </div>
                    </div>
                    
                    <div class="row g-2 mt-2">
                        <div class="col-6">
                            <label class="form-label">Sıra</label>
                            <input type="number" name="sort_order" id="sortOrder" class="form-control" value="0">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Durum</label>
                            <select name="status" id="status" class="form-select">
                                <option value="active">Aktif</option>
                                <option value="inactive">Pasif</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <button type="submit" class="btn btn-dark w-100">
                            <i class="bi bi-check-circle me-2"></i><span id="submitText">Slider Ekle</span>
                        </button>
                        <button type="button" class="btn btn-outline-secondary w-100 mt-2 d-none" id="cancelEdit" onclick="resetForm()">
                            İptal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Slider List -->
    <div class="col-lg-8">
        <div class="admin-card">
            <div class="card-header">
                <h5 class="mb-0">Sliderlar</h5>
            </div>
            <div class="card-body">
                <?php if (empty($sliders)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-images display-4 d-block mb-3"></i>
                        Henüz slider eklenmemiş
                    </div>
                <?php else: ?>
                    <div class="row g-3">
                        <?php foreach ($sliders as $slider): ?>
                            <div class="col-md-6">
                                <div class="slider-card">
                                    <img src="../uploads/<?= htmlspecialchars($slider['image']) ?>" alt="" class="slider-preview">
                                    <div class="slider-info">
                                        <h6><?= htmlspecialchars($slider['title']) ?></h6>
                                        <span class="badge bg-<?= $slider['status'] === 'active' ? 'success' : 'secondary' ?>">
                                            <?= $slider['status'] === 'active' ? 'Aktif' : 'Pasif' ?>
                                        </span>
                                    </div>
                                    <div class="slider-actions">
                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                onclick="editSlider(<?= htmlspecialchars(json_encode($slider)) ?>)">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <a href="?delete=<?= $slider['id'] ?>" class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('Bu slider\'ı silmek istediğinize emin misiniz?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.slider-card { background: #f8f9fa; border-radius: 8px; overflow: hidden; }
.slider-preview { width: 100%; height: 120px; object-fit: cover; }
.slider-info { padding: 10px; display: flex; justify-content: space-between; align-items: center; }
.slider-info h6 { margin: 0; font-size: 14px; }
.slider-actions { padding: 0 10px 10px; display: flex; gap: 5px; }
</style>

<script>
function editSlider(slider) {
    document.getElementById('editId').value = slider.id;
    document.getElementById('title').value = slider.title;
    document.getElementById('subtitle').value = slider.subtitle || '';
    document.getElementById('buttonText').value = slider.button_text || '';
    document.getElementById('buttonLink').value = slider.button_link || '';
    document.getElementById('sortOrder').value = slider.sort_order;
    document.getElementById('status').value = slider.status;
    document.getElementById('formTitle').textContent = 'Slider Düzenle';
    document.getElementById('submitText').textContent = 'Güncelle';
    document.getElementById('cancelEdit').classList.remove('d-none');
    document.getElementById('imageInput').removeAttribute('required');
}

function resetForm() {
    document.getElementById('sliderForm').reset();
    document.getElementById('editId').value = '0';
    document.getElementById('formTitle').textContent = 'Yeni Slider Ekle';
    document.getElementById('submitText').textContent = 'Slider Ekle';
    document.getElementById('cancelEdit').classList.add('d-none');
}
</script>

<?php include 'views/layouts/footer.php'; ?>
