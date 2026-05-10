
<div class="bg-glow"></div>

<!-- Top nav -->
<nav class="topnav">
<a href="#" class="brand">spb<em>Vkurse</em></a>
<span class="nav-title">/ Сообщения</span>
<div class="nav-spacer"></div>
<div class="nav-avatar" title="Ваш профиль">М</div>
</nav>

<div class="layout">

<!-- ── Conversation list ── -->
<aside class="conv-panel">
    <div class="conv-header">
    <h2>Входящие</h2>
    <div class="search-wrap">
        <span class="search-icon">
        <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        </span>
        <input type="text" id="searchInput" placeholder="Поиск сообщений..." oninput="filterConvs(this.value)" />
    </div>
    </div>

    <div class="conv-list" id="convList">
    <!-- populated by JS -->
    </div>

    <button class="new-conv-btn" onclick="openModal()">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    Написать автору
    </button>
</aside>

<!-- ── Message panel ── -->
<main class="msg-panel" id="msgPanel">
    <div class="empty-state" id="emptyState">
    <div class="es-icon"><i class="fa-solid fa-message"></i></div>
    <h3>Нет выбранного диалога</h3>
    <p>Выберите переписку слева или начните новый диалог с автором</p>
    </div>
</main>

</div>

<!-- ── New conversation modal ── -->
<div class="modal-overlay" id="modalOverlay" onclick="handleOverlayClick(event)">
<div class="modal">
    <h3>Написать автору</h3>
    <label>Имя автора или email</label>
    <input type="text" id="newRecipient" placeholder="Например: Анна Смирнова" />
    <label>Сообщение</label>
    <textarea id="newMessage" placeholder="Введите ваше сообщение..."></textarea>
    <div class="modal-actions">
    <button class="btn-cancel" onclick="closeModal()">Отмена</button>
    <button class="btn-send-modal" onclick="sendNewConv()">Отправить</button>
    </div>
</div>
</div>

<script src="<?= BASE_URL . '/static/js/inbox-page-mert.js'?>"></script>
