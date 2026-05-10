
<?php require_once ADMIN_PATH . '/views/layout/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Edit Setting</h3>
                <a href="?action=settings" class="btn btn-sm btn-secondary float-end">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
            
            <form method="POST" action="?action=settings&method=edit&id=<?= $setting['id'] ?>">
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="setting_key" class="form-label">
                                    Setting Key *
                                    <small class="text-muted">(unique identifier)</small>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="setting_key" 
                                       name="setting_key" 
                                       value="<?= htmlspecialchars($setting['setting_key']) ?>" 
                                       required
                                       <?= $setting['setting_group'] === 'system' ? 'readonly' : '' ?>>
                                <?php if ($setting['setting_group'] === 'system'): ?>
                                    <div class="form-text text-warning">System settings key cannot be changed</div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="setting_group" class="form-label">Group *</label>
                                <select class="form-control" id="setting_group" name="setting_group" required>
                                    <option value="">Select Group</option>
                                    <option value="general" <?= $setting['setting_group'] === 'general' ? 'selected' : '' ?>>General</option>
                                    <option value="seo" <?= $setting['setting_group'] === 'seo' ? 'selected' : '' ?>>SEO</option>
                                    <option value="contact" <?= $setting['setting_group'] === 'contact' ? 'selected' : '' ?>>Contact</option>
                                    <option value="social" <?= $setting['setting_group'] === 'social' ? 'selected' : '' ?>>Social Media</option>
                                    <option value="email" <?= $setting['setting_group'] === 'email' ? 'selected' : '' ?>>Email</option>
                                    <option value="footer" <?= $setting['setting_group'] === 'footer' ? 'selected' : '' ?>>Footer</option>
                                    <option value="header" <?= $setting['setting_group'] === 'header' ? 'selected' : '' ?>>Header</option>
                                    <option value="system" <?= $setting['setting_group'] === 'system' ? 'selected' : '' ?>>System</option>
                                    <option value="other" <?= $setting['setting_group'] === 'other' ? 'selected' : '' ?>>Other</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="setting_type" class="form-label">Type *</label>
                                <select class="form-control" id="setting_type" name="setting_type" required>
                                    <option value="">Select Type</option>
                                    <option value="text" <?= $setting['setting_type'] === 'text' ? 'selected' : '' ?>>Text</option>
                                    <option value="textarea" <?= $setting['setting_type'] === 'textarea' ? 'selected' : '' ?>>Text Area</option>
                                    <option value="number" <?= $setting['setting_type'] === 'number' ? 'selected' : '' ?>>Number</option>
                                    <option value="email" <?= $setting['setting_type'] === 'email' ? 'selected' : '' ?>>Email</option>
                                    <option value="boolean" <?= $setting['setting_type'] === 'boolean' ? 'selected' : '' ?>>Boolean (Yes/No)</option>
                                    <option value="json" <?= $setting['setting_type'] === 'json' ? 'selected' : '' ?>>JSON</option>
                                    <option value="html" <?= $setting['setting_type'] === 'html' ? 'selected' : '' ?>>HTML</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="display_order" class="form-label">Display Order</label>
                                <input type="number" 
                                       class="form-control" 
                                       id="display_order" 
                                       name="display_order" 
                                       value="<?= htmlspecialchars($setting['display_order'] ?? 0) ?>" 
                                       min="0">
                                <div class="form-text">Lower numbers display first</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="setting_value" class="form-label">Value</label>
                        
                        <?php if ($setting['setting_type'] === 'boolean'): ?>
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="setting_value_checkbox" 
                                       name="setting_value" 
                                       value="1"
                                       <?= $setting['setting_value'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="setting_value_checkbox">
                                    Enabled
                                </label>
                            </div>
                        <?php else: ?>
                            <?php if ($setting['setting_type'] === 'textarea'): ?>
                                <textarea class="form-control" 
                                          id="setting_value" 
                                          name="setting_value" 
                                          rows="5"><?= htmlspecialchars($setting['setting_value']) ?></textarea>
                            <?php elseif ($setting['setting_type'] === 'json'): ?>
                                <textarea class="form-control" 
                                          id="setting_value" 
                                          name="setting_value" 
                                          rows="5"
                                          placeholder='{"key": "value"}'><?= htmlspecialchars($setting['setting_value']) ?></textarea>
                                <div class="form-text">Enter valid JSON data</div>
                            <?php elseif ($setting['setting_type'] === 'html'): ?>
                                <textarea class="form-control" 
                                          id="setting_value" 
                                          name="setting_value" 
                                          rows="8"><?= htmlspecialchars($setting['setting_value']) ?></textarea>
                                <div class="form-text">HTML content allowed</div>
                            <?php else: ?>
                                <input type="text" 
                                       class="form-control" 
                                       id="setting_value" 
                                       name="setting_value" 
                                       value="<?= htmlspecialchars($setting['setting_value']) ?>">
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <?php if ($setting['setting_type'] === 'boolean'): ?>
                            <input type="hidden" id="setting_value_hidden" name="setting_value" value="0">
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" 
                                  id="description" 
                                  name="description" 
                                  rows="2"><?= htmlspecialchars($setting['description'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="is_public" 
                                   name="is_public" 
                                   value="1"
                                   <?= $setting['is_public'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_public">
                                Public Setting (visible through API)
                            </label>
                        </div>
                    </div>
                    
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-title">Setting Information</h6>
                            <dl class="row mb-0">
                                <dt class="col-sm-4">Created:</dt>
                                <dd class="col-sm-8"><?= date('Y-m-d H:i:s', strtotime($setting['created_at'] ?? 'now')) ?></dd>
                                
                                <dt class="col-sm-4">Last Updated:</dt>
                                <dd class="col-sm-8"><?= date('Y-m-d H:i:s', strtotime($setting['updated_at'] ?? 'now')) ?></dd>
                                
                                <?php if ($setting['setting_group'] === 'system'): ?>
                                    <dt class="col-sm-4 text-warning">Status:</dt>
                                    <dd class="col-sm-8"><span class="badge bg-warning">System Setting</span></dd>
                                <?php endif; ?>
                            </dl>
                        </div>
                    </div>
                </div>
                
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                    <a href="?action=settings" class="btn btn-secondary">Cancel</a>
                    
                    <?php if ($setting['setting_group'] !== 'system'): ?>
                        <a href="?action=settings&method=delete&id=<?= $setting['id'] ?>" 
                           class="btn btn-danger float-end" 
                           onclick="return confirm('Are you sure you want to delete this setting?')">
                            <i class="fas fa-trash"></i> Delete
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Обработка чекбокса для boolean типа
    const checkbox = document.getElementById('setting_value_checkbox');
    const hiddenInput = document.getElementById('setting_value_hidden');
    
    if (checkbox && hiddenInput) {
        checkbox.addEventListener('change', function() {
            hiddenInput.value = this.checked ? '1' : '0';
        });
    }
    
    // Валидация JSON
    const typeSelect = document.getElementById('setting_type');
    const valueField = document.getElementById('setting_value');
    
    if (valueField) {
        valueField.addEventListener('blur', function() {
            if (typeSelect.value === 'json') {
                try {
                    JSON.parse(this.value);
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                } catch (e) {
                    this.classList.remove('is-valid');
                    this.classList.add('is-invalid');
                    console.error('Invalid JSON:', e.message);
                }
            }
        });
    }
    
    // Автоматическое изменение высоты textarea
    if (valueField && valueField.tagName === 'TEXTAREA') {
        valueField.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
        // Инициализация высоты
        setTimeout(() => {
            valueField.style.height = 'auto';
            valueField.style.height = (valueField.scrollHeight) + 'px';
        }, 100);
    }
});
</script>

<?php require_once ADMIN_PATH . '/views/layout/footer.php'; ?>
