/* ── Demo data ── */
const ME = { name: 'Мерт', initials: 'М' };

const conversations = [
/*{
    id: 1,
    name: 'Анна Смирнова',
    role: 'Автор',
    initials: 'АС',
    color: '#7c6ff7',
    unread: 2,
    messages: [
    { from: 'them', text: 'Здравствуйте! Спасибо за интерес к моим статьям.', time: '10:12', date: 'Сегодня' },
    { from: 'me',   text: 'Добрый день, Анна! Ваш последний материал про ИИ был отличным.', time: '10:15', date: 'Сегодня' },
    { from: 'them', text: 'Очень рада слышать! Готовлю продолжение на следующей неделе.', time: '10:18', date: 'Сегодня' },
    { from: 'me',   text: 'С нетерпением жду! Есть вопрос по теме — можно уточнить?', time: '10:20', date: 'Сегодня' },
    { from: 'them', text: 'Конечно, спрашивайте!', time: '10:21', date: 'Сегодня' },
    { from: 'them', text: 'Буду рада помочь 😊', time: '10:21', date: 'Сегодня' },
    ]
},
{
    id: 2,
    name: 'Дмитрий Козлов',
    role: 'Автор',
    initials: 'ДК',
    color: '#10b981',
    unread: 0,
    messages: [
    { from: 'me',   text: 'Дмитрий, читал вашу статью про блокчейн — очень круто!', time: 'Вчера', date: 'Вчера' },
    { from: 'them', text: 'Спасибо большое! Если есть вопросы — задавайте.', time: 'Вчера', date: 'Вчера' },
    { from: 'me',   text: 'Обязательно воспользуюсь, спасибо!', time: 'Вчера', date: 'Вчера' },
    ]
},
{
    id: 3,
    name: 'Мария Иванова',
    role: 'Автор',
    initials: 'МИ',
    color: '#f59e0b',
    unread: 1,
    messages: [
    { from: 'them', text: 'Привет! Видела, что вы подписались на мои материалы.', time: '14:05', date: 'Сегодня' },
    { from: 'me',   text: 'Да, очень нравятся ваши репортажи!', time: '14:10', date: 'Сегодня' },
    { from: 'them', text: 'Скоро выйдет большой материал про политику в СПб 🔥', time: '14:33', date: 'Сегодня' },
    ]
},
{
    id: 4,
    name: 'Иван Петров',
    role: 'Автор',
    initials: 'ИП',
    color: '#ef4444',
    unread: 0,
    messages: [
    { from: 'me',   text: 'Иван, можете порекомендовать источники по теме экономики?', time: '2 дня назад', date: '2 дня назад' },
    { from: 'them', text: 'Конечно! Посмотрите РБК и Коммерсантъ — там хорошая аналитика.', time: '2 дня назад', date: '2 дня назад' },
    ]
},*/
];

let activeId = null;

/* ── Render conversation list ── */
function renderConvList(list) {
const el = document.getElementById('convList');
el.innerHTML = '';
list.forEach(conv => {
    const last = conv.messages[conv.messages.length - 1];
    const preview = (last.from === 'me' ? 'Вы: ' : '') + last.text;
    el.innerHTML += `
    <div class="conv-item ${activeId === conv.id ? 'active' : ''}" onclick="openConv(${conv.id})">
        <div class="conv-avatar" style="background: linear-gradient(135deg, ${conv.color}, ${conv.color}99)">
        ${conv.initials}
        ${conv.unread ? '<span class="unread-dot"></span>' : ''}
        </div>
        <div class="conv-info">
        <div class="conv-top">
            <span class="conv-name">${conv.name}</span>
            <span class="conv-time">${last.time}</span>
        </div>
        <div style="display:flex;align-items:center;justify-content:space-between;gap:6px">
            <div class="conv-preview ${conv.unread ? 'unread' : ''}">${preview}</div>
            ${conv.unread ? `<div class="conv-badge">${conv.unread}</div>` : ''}
        </div>
        </div>
    </div>`;
});
}

/* ── Open conversation ── */
function openConv(id) {
activeId = id;
const conv = conversations.find(c => c.id === id);
conv.unread = 0;
renderConvList(conversations);

const panel = document.getElementById('msgPanel');
let dateShown = '';
let bubbles = '';
conv.messages.forEach(msg => {
    if (msg.date !== dateShown) {
    bubbles += `<div class="date-divider"><span>${msg.date}</span></div>`;
    dateShown = msg.date;
    }
    const isMine = msg.from === 'me';
    const initials = isMine ? ME.initials : conv.initials;
    const color = isMine ? 'var(--accent)' : conv.color;
    bubbles += `
    <div class="msg-row ${isMine ? 'mine' : ''}">
        <div class="msg-bubble-avatar" style="background:linear-gradient(135deg,${color},${color}99)">${initials}</div>
        <div class="msg-bubble">
        ${msg.text}
        <span class="msg-time">${msg.time}</span>
        </div>
    </div>`;
});

panel.innerHTML = `
    <div class="msg-header">
    <div class="msg-header-avatar" style="background:linear-gradient(135deg,${conv.color},${conv.color}99)">${conv.initials}</div>
    <div class="msg-header-info">
        <div class="msg-header-name">${conv.name}</div>
        <div class="msg-header-role">${conv.role}</div>
    </div>
    <div class="msg-header-actions">
        <button class="icon-btn" title="Поиск в переписке">
        <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        </button>
        <button class="icon-btn" title="Профиль автора">
        <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        </button>
    </div>
    </div>
    <div class="msg-body" id="msgBody">${bubbles}</div>
    <div class="compose">
    <div class="compose-inner">
        <textarea class="compose-input" id="composeInput" rows="1" placeholder="Написать сообщение..."
        onkeydown="handleKey(event)" oninput="autoResize(this)"></textarea>
        <div class="compose-actions">
        <button class="attach-btn" title="Прикрепить файл">
            <svg viewBox="0 0 24 24"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
        </button>
        <button class="send-btn" onclick="sendMessage()" title="Отправить">
            <svg viewBox="0 0 24 24"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
        </button>
        </div>
    </div>
    </div>`;

scrollToBottom();
}

function scrollToBottom() {
const body = document.getElementById('msgBody');
if (body) body.scrollTop = body.scrollHeight;
}

/* ── Send message ── */
function sendMessage() {
const input = document.getElementById('composeInput');
if (!input) return;
const text = input.value.trim();
if (!text) return;

const conv = conversations.find(c => c.id === activeId);
const now = new Date();
const time = now.getHours().toString().padStart(2,'0') + ':' + now.getMinutes().toString().padStart(2,'0');
conv.messages.push({ from: 'me', text, time, date: 'Сегодня' });

input.value = '';
input.style.height = '';
openConv(activeId);
}

function handleKey(e) {
if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
}

function autoResize(el) {
el.style.height = 'auto';
el.style.height = Math.min(el.scrollHeight, 120) + 'px';
}

/* ── Search ── */
function filterConvs(q) {
const filtered = q
    ? conversations.filter(c => c.name.toLowerCase().includes(q.toLowerCase()) ||
        c.messages.some(m => m.text.toLowerCase().includes(q.toLowerCase())))
    : conversations;
renderConvList(filtered);
}

/* ── New conversation modal ── */
function openModal() {
document.getElementById('modalOverlay').classList.add('open');
document.getElementById('newRecipient').focus();
}
function closeModal() {
document.getElementById('modalOverlay').classList.remove('open');
document.getElementById('newRecipient').value = '';
document.getElementById('newMessage').value = '';
}
function handleOverlayClick(e) {
if (e.target === document.getElementById('modalOverlay')) closeModal();
}
function sendNewConv() {
const recipient = document.getElementById('newRecipient').value.trim();
const text = document.getElementById('newMessage').value.trim();
if (!recipient || !text) return;

const colors = ['#7c6ff7','#10b981','#f59e0b','#ef4444','#3b82f6','#ec4899'];
const color = colors[Math.floor(Math.random() * colors.length)];
const initials = recipient.split(' ').map(w => w[0]).join('').slice(0,2).toUpperCase();
const now = new Date();
const time = now.getHours().toString().padStart(2,'0') + ':' + now.getMinutes().toString().padStart(2,'0');

const newConv = {
    id: Date.now(),
    name: recipient,
    role: 'Автор',
    initials,
    color,
    unread: 0,
    messages: [{ from: 'me', text, time, date: 'Сегодня' }]
};
conversations.unshift(newConv);
closeModal();
renderConvList(conversations);
openConv(newConv.id);
}

/* ── Init ── */
renderConvList(conversations);