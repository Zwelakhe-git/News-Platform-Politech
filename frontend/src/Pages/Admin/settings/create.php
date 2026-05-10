
<?php require_once ADMIN_PATH . '/views/layout/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Create New Setting</h3>
                <a href="?action=settings" class="btn btn-sm btn-secondary float-end">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
            
            <form method="POST" action="?action=settings&method=create">
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
                                       value="<?= htmlspecialchars($_POST['setting_key'] ?? '') ?>" 
                                       required
                                       placeholder="e.g., site_name, contact_email">
                                <div class="form-text">Use lowercase with underscores</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="setting_group" class="form-label">Group *</label>
                                <select class="form-control" id="setting_group" name="setting_group" required>
                                    <option value="">Select Group</option>
                                    <option value="general" <?= ($_POST['setting_group'] ?? '') === 'general' ? 'selected' : '' ?>>General</option>
                                    <option value="seo" <?= ($_POST['setting_group'] ?? '') === 'seo' ? 'selected' : '' ?>>SEO</option>
                                    <option value="contact" <?= ($_POST['setting_group'] ?? '') === 'contact' ? 'selected' : '' ?>>Contact</option>
                                    <option value="social" <?= ($_POST['setting_group'] ?? '') === 'social' ? 'selected' : '' ?>>Social Media</option>
                                    <option value="email" <?= ($_POST['setting_group'] ?? '') === 'email' ? 'selected' : '' ?>>Email</option>
                                    <option value="footer" <?= ($_POST['setting_group'] ?? '') === 'footer' ? 'selected' : '' ?>>Footer</option>
                                    <option value="header" <?= ($_POST['setting_group'] ?? '') === 'header' ? 'selected' : '' ?>>Header</option>
                                    <option value="system" <?= ($_POST['setting_group'] ?? '') === 'system' ? 'selected' : '' ?>>System</option>
                                    <option value="other" <?= ($_POST['setting_group'] ?? '') === 'other' ? 'selected' : '' ?>>Other</option>
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
                                    <option value="text" <?= ($_POST['setting_type'] ?? '') === 'text' ? 'selected' : '' ?>>Text</option>
                                    <option value="textarea" <?= ($_POST['setting_type'] ?? '') === 'textarea' ? 'selected' : '' ?>>Text Area</option>
                                    <option value="number" <?= ($_POST['setting_type'] ?? '') === 'number' ? 'selected' : '' ?>>Number</option>
                                    <option value="email" <?= ($_POST['setting_type'] ?? '') === 'email' ? 'selected' : '' ?>>Email</option>
                                    <option value="boolean" <?= ($_POST['setting_type'] ?? '') === 'boolean' ? 'selected' : '' ?>>Boolean (Yes/No)</option>
                                    <option value="json" <?= ($_POST['setting_type'] ?? '') === 'json' ? 'selected' : '' ?>>JSON</option>
                                    <option value="html" <?= ($_POST['setting_type'] ?? '') === 'html' ? 'selected' : '' ?>>HTML</option>
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
                                       value="<?= htmlspecialchars($_POST['display_order'] ?? 0) ?>" 
                                       min="0">
                                <div class="form-text">Lower numbers display first</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="setting_value" class="form-label">Value</label>
                        <textarea class="form-control" 
                                  id="setting_value" 
                                  name="setting_value" 
                                  rows="3"
                                  placeholder="Enter setting value"><?= htmlspecialchars($_POST['setting_value'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" 
                                  id="description" 
                                  name="description" 
                                  rows="2"
                                  placeholder="Brief description of this setting"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="is_public" 
                                   name="is_public" 
                                   value="1"
                                   <?= isset($_POST['is_public']) ? 'checked' : 'checked' ?>>
                            <label class="form-check-label" for="is_public">
                                Public Setting (visible through API)
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Setting
                    </button>
                    <a href="?action=settings" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Common Settings Examples</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Footer Settings:</h6>
                        <ul class="list-unstyled">
                            <li><code>footer_copyright</code> - Copyright text</li>
                            <li><code>footer_phone</code> - Contact phone</li>
                            <li><code>footer_email</code> - Contact email</li>
                            <li><code>footer_address</code> - Physical address</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>General Settings:</h6>
                        <ul class="list-unstyled">
                            <li><code>site_name</code> - Website name</li>
                            <li><code>site_description</code> - Meta description</li>
                            <li><code>contact_email</code> - Main contact email</li>
                            <li><code>contact_phone</code> - Main contact phone</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Пример подсказки для ключа
    document.getElementById('setting_key').addEventListener('input', function() {
        const key = this.value.toLowerCase().replace(/[^a-z0-9_]/g, '_');
        this.value = key;
    });
    
    // Показывать/скрывать дополнительные поля в зависимости от типа
    const typeSelect = document.getElementById('setting_type');
    const valueField = document.getElementById('setting_value');
    
    typeSelect.addEventListener('change', function() {
        if (this.value === 'boolean') {
            valueField.value = '1';
            valueField.rows = 1;
        } else if (this.value === 'textarea' || this.value === 'json') {
            valueField.rows = 5;
        } else {
            valueField.rows = 3;
        }
    });
});
</script>

<?php require_once ADMIN_PATH . '/views/layout/footer.php'; ?>
