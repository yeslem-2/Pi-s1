/* ============================================
   main.js – Core JS: theme, sidebar, notifications,
             device toggles, toast system, door modal
   ============================================ */

// ============================================
// Theme Toggle
// ============================================
function toggleTheme() {
    const html = document.documentElement;
    const isDark = html.classList.toggle('dark');
    document.body.classList.toggle('light-mode', !isDark);
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
    const icon = document.getElementById('theme-icon');
    if (icon) icon.textContent = isDark ? 'dark_mode' : 'light_mode';
}

document.addEventListener('DOMContentLoaded', function () {
    const saved = localStorage.getItem('theme');
    const html = document.documentElement;
    if (saved === 'light') {
        html.classList.remove('dark');
        document.body.classList.add('light-mode');
        const icon = document.getElementById('theme-icon');
        if (icon) icon.textContent = 'light_mode';
    } else {
        html.classList.add('dark');
        document.body.classList.remove('light-mode');
    }

    // Stagger-animate cards on load
    document.querySelectorAll('.stat-card').forEach((card, i) => {
        card.style.animationDelay = (i * 80) + 'ms';
    });

    // Initial notification badge fetch
    loadNotifications();
});

// ============================================
// Sidebar Toggle (Mobile)
// ============================================
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    sidebar.classList.toggle('open');
    if (overlay) overlay.classList.toggle('show');
}

document.addEventListener('click', function (e) {
    const sidebar  = document.getElementById('sidebar');
    const toggle   = document.querySelector('.mobile-toggle');
    const overlay  = document.getElementById('sidebar-overlay');
    if (window.innerWidth <= 768 && sidebar && toggle &&
        !sidebar.contains(e.target) && !toggle.contains(e.target)) {
        sidebar.classList.remove('open');
        if (overlay) overlay.classList.remove('show');
    }
});

// ============================================
// Notifications Dropdown
// ============================================
function toggleNotifications() {
    const dropdown = document.getElementById('notif-dropdown');
    if (!dropdown) return;
    dropdown.classList.toggle('show');
    if (dropdown.classList.contains('show')) loadNotifications();
}

// Close dropdown when clicking outside
document.addEventListener('click', function (e) {
    const dropdown = document.getElementById('notif-dropdown');
    const bell     = document.querySelector('.notification-bell');
    if (dropdown && bell && !dropdown.contains(e.target) && !bell.contains(e.target)) {
        dropdown.classList.remove('show');
    }
});

// Load notifications via AJAX – updates badge + dropdown list
function loadNotifications() {
    fetch('../api/get_notifications.php')
        .then(r => r.json())
        .then(data => {
            if (!data.success) return;

            // Badge
            const badge = document.getElementById('notif-badge');
            if (badge) {
                if (data.unread_count > 0) {
                    badge.textContent    = data.unread_count > 9 ? '9+' : data.unread_count;
                    badge.style.display  = 'flex';
                } else {
                    badge.style.display  = 'none';
                }
            }

            // Dropdown list
            const list = document.getElementById('notif-list');
            if (!list) return;

            if (data.notifications.length === 0) {
                list.innerHTML = '<div class="notif-empty">No notifications yet</div>';
                return;
            }

            list.innerHTML = data.notifications.map(n => {
                const icon  = n.type === 'warning' ? '&#9888;' : n.type === 'success' ? '&#10003;' : '&#9432;';
                const cls   = n.is_read == 0 ? 'unread' : '';
                return `<div class="notification-item ${cls}">
                    <span class="notif-type-icon notif-${n.type}">${icon}</span>
                    <div class="notif-body">
                        <div class="notif-message">${escapeHtml(n.message)}</div>
                        <div class="notif-time">${timeAgo(n.created_at)}</div>
                    </div>
                </div>`;
            }).join('');
        })
        .catch(err => console.error('Notifications error:', err));
}

// Mark all notifications as read
function markAllRead() {
    fetch('../api/mark_notifications_read.php')
        .then(r => r.json())
        .then(data => { if (data.success) location.reload(); })
        .catch(err => console.error(err));
}

// Auto-refresh badge every 30 s
setInterval(loadNotifications, 30000);

// ============================================
// Device Toggle (AJAX)
// ============================================
function toggleDevice(type, checked, el) {
    fetch('../api/update_device.php', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json' },
        body:    JSON.stringify({ type, status: checked })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            if (type === 'device') {
                const v = document.getElementById('device-status');
                const t = document.getElementById('device-status-text');
                if (v) v.textContent = data.status;
                if (t) { t.textContent = data.status; t.className = 'status-text ' + (data.status === 'ON' ? 'status-on' : 'status-off'); }
            } else if (type === 'ac') {
                const v = document.getElementById('ac-status');
                const t = document.getElementById('ac-status-text');
                if (v) v.textContent = data.status;
                if (t) { t.textContent = data.status; t.className = 'status-text ' + (data.status === 'ON' ? 'status-on' : 'status-off'); }
            } else if (type === 'auto') {
                const t = document.getElementById('auto-status-text');
                if (t) { t.textContent = data.status == 1 ? 'ENABLED' : 'DISABLED'; t.className = 'status-text ' + (data.status == 1 ? 'status-on' : 'status-off'); }
            }
            showToast('Updated successfully', 'success');
        } else {
            showToast('Update failed', 'danger');
            if (el) el.checked = !checked;
        }
    })
    .catch(() => {
        showToast('Network error', 'danger');
        if (el) el.checked = !checked;
    });
}

// ============================================
// Door Control Modal with PIN pad
// ============================================
let doorPin      = '';
const DOOR_MAX   = 4;
let doorPending  = null; // 'open' | 'close' | 'toggle'

function openDoorModal(action) {
    doorPin     = '';
    doorPending = action || 'toggle';
    updateDoorDots();

    const label = document.getElementById('door-action-label');
    if (label) label.textContent = doorPending === 'open' ? 'open' : doorPending === 'close' ? 'close' : 'toggle';

    const err = document.getElementById('door-modal-error');
    if (err) err.classList.add('hidden');

    document.getElementById('door-modal').classList.add('show');
}

function closeDoorModal() {
    document.getElementById('door-modal').classList.remove('show');
    doorPin = '';
}

function doorPinInput(digit) {
    if (doorPin.length >= DOOR_MAX) return;
    doorPin += digit;
    updateDoorDots();
    if (doorPin.length === 4) setTimeout(doorPinSubmit, 200);
}

function doorPinClear() {
    doorPin = doorPin.slice(0, -1);
    updateDoorDots();
}

function updateDoorDots() {
    for (let i = 1; i <= 4; i++) {
        const dot = document.getElementById('ddot-' + i);
        if (dot) dot.classList.toggle('filled', i <= doorPin.length);
    }
}

function doorPinSubmit() {
    if (doorPin.length < 4) { showDoorError('Enter at least 4 digits'); return; }

    const submitBtn = document.querySelector('#door-modal .pin-submit');
    if (submitBtn) submitBtn.disabled = true;

    fetch('../api/door_control.php', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json' },
        body:    JSON.stringify({ code: doorPin, action: doorPending })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            closeDoorModal();
            updateDoorUI(data.status);
            showToast('Door ' + data.status.toLowerCase(), 'success');
            loadNotifications();
        } else {
            showDoorError(data.message);
            shakeDoorModal();
            doorPin = '';
            updateDoorDots();
        }
        if (submitBtn) submitBtn.disabled = false;
    })
    .catch(() => {
        showDoorError('Network error – try again');
        doorPin = '';
        updateDoorDots();
        if (submitBtn) submitBtn.disabled = false;
    });
}

function showDoorError(msg) {
    const el = document.getElementById('door-modal-error');
    if (!el) return;
    el.textContent = msg;
    el.classList.remove('hidden');
    setTimeout(() => el.classList.add('hidden'), 3000);
}

function shakeDoorModal() {
    const content = document.querySelector('#door-modal .modal-content');
    if (!content) return;
    content.classList.add('shake');
    setTimeout(() => content.classList.remove('shake'), 600);
}

function updateDoorUI(status) {
    // Status card value
    const card = document.getElementById('door-status-value');
    if (card) {
        card.textContent  = status;
        card.className    = 'card-value ' + (status === 'OPEN' ? 'status-on' : 'status-off');
    }
    // Control panel text
    const text = document.getElementById('door-status-text');
    if (text) {
        text.textContent = status;
        text.className   = 'status-text ' + (status === 'OPEN' ? 'status-on' : 'status-off');
    }
    // Button label
    const btn = document.getElementById('door-toggle-btn');
    if (btn) btn.innerHTML = '<span class="material-symbols-outlined">' + (status === 'OPEN' ? 'lock_open' : 'lock') + '</span> ' + (status === 'OPEN' ? 'Close' : 'Open') + ' Door';
}

// Close door modal on backdrop click
document.addEventListener('click', function (e) {
    const modal = document.getElementById('door-modal');
    if (modal && e.target === modal) closeDoorModal();
});

// ============================================
// Toast Notification System
// ============================================
function showToast(message, type = 'info', duration = 3000) {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        document.body.appendChild(container);
    }

    const icons = { success: '&#10003;', warning: '&#9888;', danger: '&#10005;', info: '&#9432;' };
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `<span class="toast-icon">${icons[type] || icons.info}</span>
                       <span class="toast-msg">${escapeHtml(message)}</span>`;
    container.appendChild(toast);

    // Animate in
    requestAnimationFrame(() => { requestAnimationFrame(() => toast.classList.add('show')); });

    // Animate out
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 400);
    }, duration);
}

// ============================================
// Utility Functions
// ============================================
function escapeHtml(text) {
    const d = document.createElement('div');
    d.appendChild(document.createTextNode(String(text)));
    return d.innerHTML;
}

function timeAgo(dateString) {
    const seconds = Math.floor((Date.now() - new Date(dateString)) / 1000);
    if (seconds < 60)    return 'Just now';
    if (seconds < 3600)  return Math.floor(seconds / 60)   + ' min ago';
    if (seconds < 86400) return Math.floor(seconds / 3600) + ' hr ago';
    return Math.floor(seconds / 86400) + ' days ago';
}
