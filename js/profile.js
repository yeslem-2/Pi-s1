/* ============================================
   profile.js  –  PIN gate + profile settings
   ============================================ */

// ============================================
// PIN Gate (runs when profile is locked)
// ============================================
let gatePin = '';
const GATE_MAX = 6;

function gatePinInput(digit) {
    if (gatePin.length >= GATE_MAX) return;
    gatePin += digit;
    updateGateDots();
    // Auto-submit at 4 digits (minimum PIN length)
    if (gatePin.length === 4) {
        setTimeout(gatePinSubmit, 200);
    }
}

function gatePinClear() {
    gatePin = gatePin.slice(0, -1);
    updateGateDots();
}

function updateGateDots() {
    for (let i = 1; i <= 4; i++) {
        const dot = document.getElementById('gdot-' + i);
        if (dot) dot.classList.toggle('filled', i <= gatePin.length);
    }
}

function gatePinSubmit() {
    if (gatePin.length < 4) { showGateError('Enter at least 4 digits'); return; }

    const btn = document.querySelector('.pin-submit');
    if (btn) btn.disabled = true;

    fetch('../api/update_profile.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'verify_code', code: gatePin })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Unlock: show a success flash then reload
            const card = document.querySelector('.gate-card');
            if (card) {
                card.style.transition = 'transform 0.4s ease, opacity 0.4s ease';
                card.style.transform  = 'scale(1.05)';
                card.style.opacity    = '0';
            }
            setTimeout(() => location.reload(), 400);
        } else {
            showGateError(data.message || 'Incorrect code');
            shakeGate();
            gatePin = '';
            updateGateDots();
            if (btn) btn.disabled = false;
        }
    })
    .catch(() => {
        showGateError('Network error – try again');
        gatePin = '';
        updateGateDots();
        if (btn) btn.disabled = false;
    });
}

function showGateError(msg) {
    const el = document.getElementById('gate-error');
    if (!el) return;
    el.textContent = msg;
    el.classList.remove('hidden');
    setTimeout(() => el.classList.add('hidden'), 3000);
}

function shakeGate() {
    const card = document.querySelector('.gate-card');
    if (!card) return;
    card.classList.add('shake');
    setTimeout(() => card.classList.remove('shake'), 600);
}

// ============================================
// Profile settings (runs when unlocked)
// ============================================

function saveProfile() {
    const btn       = document.querySelector('#info-feedback').closest('.control-panel').querySelector('.save-btn');
    const full_name = (document.getElementById('full_name')?.value || '').trim();
    const email     = (document.getElementById('email')?.value || '').trim();

    if (!email) { showFeedback('info-feedback', 'Email is required', 'danger'); return; }

    setLoading(btn, true);

    fetch('../api/update_profile.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'update_info', full_name, email })
    })
    .then(r => r.json())
    .then(data => {
        showFeedback('info-feedback', data.message, data.success ? 'success' : 'danger');
        setLoading(btn, false);
    })
    .catch(() => {
        showFeedback('info-feedback', 'Network error', 'danger');
        setLoading(btn, false);
    });
}

function saveDoorCode() {
    const btn          = document.querySelector('#code-feedback').closest('.control-panel').querySelector('.save-btn');
    const current_code = document.getElementById('current_code')?.value || '';
    const new_code     = (document.getElementById('new_code')?.value || '').trim();
    const confirm_code = (document.getElementById('confirm_code')?.value || '').trim();

    if (!/^\d{4,6}$/.test(new_code)) {
        showFeedback('code-feedback', 'Code must be 4 – 6 digits', 'danger'); return;
    }
    if (new_code !== confirm_code) {
        showFeedback('code-feedback', 'Codes do not match', 'danger'); return;
    }

    setLoading(btn, true);

    fetch('../api/update_profile.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'update_door_code', current_code, new_code })
    })
    .then(r => r.json())
    .then(data => {
        showFeedback('code-feedback', data.message, data.success ? 'success' : 'danger');
        setLoading(btn, false);
        if (data.success) {
            // Clear fields
            const fields = ['current_code', 'new_code', 'confirm_code'];
            fields.forEach(id => { const el = document.getElementById(id); if (el) el.value = ''; });
        }
    })
    .catch(() => {
        showFeedback('code-feedback', 'Network error', 'danger');
        setLoading(btn, false);
    });
}

// ============================================
// Helpers
// ============================================

function showFeedback(containerId, message, type) {
    const el = document.getElementById(containerId);
    if (!el) return;
    el.innerHTML = `<div class="alert alert-${type}">${escapeHtml(message)}</div>`;
    setTimeout(() => { el.innerHTML = ''; }, 4000);
}

function setLoading(btn, loading) {
    if (!btn) return;
    const text    = btn.querySelector('.btn-text');
    const spinner = btn.querySelector('.btn-spinner');
    btn.disabled  = loading;
    if (text)    text.classList.toggle('hidden', loading);
    if (spinner) spinner.classList.toggle('hidden', !loading);
}
