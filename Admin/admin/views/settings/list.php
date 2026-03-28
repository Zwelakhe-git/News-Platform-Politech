
<?php require_once ADMIN_PATH . '/views/layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Site Settings Management</h2>
    <div>
        <a href="?action=settings&method=create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Setting
        </a>
        <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#bulkEditModal">
            <i class="fas fa-edit"></i> Bulk Edit
        </button>
    </div>
</div>

<?php if (isset($_GET['success'])): ?>
    <?php 
    $messages = [
        'created' => 'Setting created successfully!',
        'updated' => 'Setting updated successfully!',
        'deleted' => 'Setting deleted successfully!',
        'bulk_updated' => 'Settings updated successfully!'
    ];
    ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $messages[$_GET['success']] ?? 'Operation successful!' ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    <?php 
    $errors = [
        'not_found' => 'Setting not found!',
        'delete_failed' => 'Failed to delete setting!',
        'system_protected' => 'System settings cannot be deleted!',
        'duplicate_key' => 'Setting key already exists!'
    ];
    ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= $errors[$_GET['error']] ?? htmlspecialchars($_GET['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <?php if (empty($groupedSettings)): ?>
            <div class="text-center py-5">
                <i class="fas fa-cog fa-3x text-muted mb-3"></i>
                <h4>No settings found</h4>
                <p class="text-muted">Create your first setting to get started</p>
                <a href="?action=settings&method=create" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create First Setting
                </a>
            </div>
        <?php else: ?>
            <!-- Accordion для групп настроек -->
            <div class="accordion" id="settingsAccordion">
                <?php foreach ($groupedSettings as $group => $settings): ?>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading-<?= htmlspecialchars($group) ?>">
                        <button class="accordion-button <?= $group !== array_key_first($groupedSettings) ? 'collapsed' : '' ?>" 
                                type="button" 
                                data-bs-toggle="collapse" 
                                data-bs-target="#collapse-<?= htmlspecialchars($group) ?>" 
                                aria-expanded="<?= $group === array_key_first($groupedSettings) ? 'true' : 'false' ?>">
                            <i class="fas fa-folder me-2"></i>
                            <?= ucfirst(htmlspecialchars($group)) ?>
                            <span class="badge bg-secondary ms-2"><?= count($settings) ?></span>
                        </button>
                    </h2>
                    
                    <div id="collapse-<?= htmlspecialchars($group) ?>" 
                         class="accordion-collapse collapse <?= $group === array_key_first($groupedSettings) ? 'show' : '' ?>" 
                         aria-labelledby="heading-<?= htmlspecialchars($group) ?>">
                        <div class="accordion-body p-0">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th width="15%">Key</th>
                                        <th width="35%">Value</th>
                                        <th width="15%">Type</th>
                                        <th width="20%">Description</th>
                                        <th width="15%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($settings as $setting): ?>
                                    <tr>
                                        <td>
                                            <code><?= htmlspecialchars($setting['setting_key']) ?></code>
                                            <?php if (!$setting['is_public']): ?>
                                                <span class="badge bg-warning ms-1">Private</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($setting['setting_type'] === 'boolean'): ?>
                                                <span class="badge bg-<?= $setting['setting_value'] ? 'success' : 'secondary' ?>">
                                                    <?= $setting['setting_value'] ? 'Yes' : 'No' ?>
                                                </span>
                                            <?php elseif (strlen($setting['setting_value']) > 50): ?>
                                                <span class="text-truncate d-inline-block" style="max-width: 300px;" 
                                                      title="<?= htmlspecialchars($setting['setting_value']) ?>">
                                                    <?= htmlspecialchars(substr($setting['setting_value'], 0, 50)) ?>...
                                                </span>
                                            <?php else: ?>
                                                <?= htmlspecialchars($setting['setting_value']) ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                <?= htmlspecialchars($setting['setting_type']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($setting['description'] ?? '—') ?>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="?action=settings&method=edit&id=<?= $setting['id'] ?>" 
                                                   class="btn btn-outline-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                
                                                <?php if ($setting['setting_group'] !== 'system'): ?>
                                                <a href="?action=settings&method=delete&id=<?= $setting['id'] ?>" 
                                                   class="btn btn-outline-danger" 
                                                   onclick="return confirm('Are you sure you want to delete this setting?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                                <?php endif; ?>
                                                
                                                <button type="button" 
                                                        class="btn btn-outline-secondary quick-edit-btn" 
                                                        data-id="<?= $setting['id'] ?>"
                                                        data-key="<?= htmlspecialchars($setting['setting_key']) ?>"
                                                        data-value="<?= htmlspecialchars($setting['setting_value']) ?>"
                                                        data-type="<?= $setting['setting_type'] ?>"
                                                        title="Quick Edit">
                                                    <i class="fas fa-bolt"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Модальное окно для массового редактирования -->
<div class="modal fade" id="bulkEditModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Edit Settings</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="?action=settings&method=bulkUpdate">
                <div class="modal-body">
                    <div class="row">
                        <?php if (!empty($settings)): ?>
                            <?php foreach ($settings as $setting): ?>
                                <?php if (in_array($setting['setting_type'], ['text', 'number', 'email', 'textarea'])): ?>
                                <div class="col-md-6 mb-3">
                                    <label for="setting_<?= $setting['id'] ?>" class="form-label">
                                        <?= htmlspecialchars($setting['setting_key']) ?>
                                        <small class="text-muted">(<?= $setting['setting_type'] ?>)</small>
                                    </label>
                                    
                                    <?php if ($setting['setting_type'] === 'textarea'): ?>
                                        <textarea class="form-control" 
                                                  id="setting_<?= $setting['id'] ?>" 
                                                  name="setting_<?= $setting['id'] ?>" 
                                                  rows="3"><?= htmlspecialchars($setting['setting_value']) ?></textarea>
                                    <?php else: ?>
                                        <input type="text" 
                                               class="form-control" 
                                               id="setting_<?= $setting['id'] ?>" 
                                               name="setting_<?= $setting['id'] ?>" 
                                               value="<?= htmlspecialchars($setting['setting_value']) ?>">
                                    <?php endif; ?>
                                    
                                    <?php if ($setting['description']): ?>
                                        <div class="form-text"><?= htmlspecialchars($setting['description']) ?></div>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-12 text-center py-4">
                                <p class="text-muted">No settings available for bulk edit</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Модальное окно для быстрого редактирования -->
<div class="modal fade" id="quickEditModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Quick Edit Setting</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="quickEditForm">
                <div class="modal-body">
                    <input type="hidden" id="edit_setting_id">
                    <div class="mb-3">
                        <label for="edit_setting_key" class="form-label">Key</label>
                        <input type="text" class="form-control" id="edit_setting_key" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="edit_setting_value" class="form-label">Value</label>
                        <input type="text" class="form-control" id="edit_setting_value" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Быстрое редактирование
    const quickEditBtns = document.querySelectorAll('.quick-edit-btn');
    const quickEditModal = new bootstrap.Modal(document.getElementById('quickEditModal'));
    
    quickEditBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const key = this.dataset.key;
            const value = this.dataset.value;
            const type = this.dataset.type;
            
            document.getElementById('edit_setting_id').value = id;
            document.getElementById('edit_setting_key').value = key;
            document.getElementById('edit_setting_value').value = value;
            
            // Настройка поля ввода в зависимости от типа
            const valueInput = document.getElementById('edit_setting_value');
            if (type === 'number') {
                valueInput.type = 'number';
            } else if (type === 'email') {
                valueInput.type = 'email';
            } else {
                valueInput.type = 'text';
            }
            
            quickEditModal.show();
        });
    });
    
    // Отправка формы быстрого редактирования
    document.getElementById('quickEditForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const id = document.getElementById('edit_setting_id').value;
        const value = document.getElementById('edit_setting_value').value;
        
        fetch(`?action=settings&method=edit&id=${id}&ajax=1`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `setting_value=${encodeURIComponent(value)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to update'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred');
        });
    });
});
</script>

<?php require_once ADMIN_PATH . '/views/layout/footer.php'; ?>
