document.addEventListener('DOMContentLoaded', () => {
  const cfg = window.ADMIN_CONFIG || {};
  const baseUrl = cfg.baseUrl || '/welcomy/Backend/controllers';
  const template = cfg.whatsappTemplate || '';
  const thankyouTemplate = cfg.thankyouTemplate || '';
  const supportPhone = cfg.supportPhone || '';

  const eventSelect = document.getElementById('eventSelect');
  const statsTotal = document.getElementById('statTotal');
  const statsPresent = document.getElementById('statPresent');
  const statsAbsent = document.getElementById('statAbsent');
  const guestList = document.getElementById('guestList');
  const emptyState = document.getElementById('emptyState');
  const toastContainer = document.getElementById('toastContainer');

  const whatsappModal = document.getElementById('whatsappModal');
  const whatsappGuestName = document.getElementById('whatsappGuestName');
  const whatsappGuestPhone = document.getElementById('whatsappGuestPhone');
  const whatsappPreview = document.getElementById('whatsappPreview');
  const whatsappConfirmBtn = document.getElementById('whatsappConfirmBtn');
  const whatsappCancelBtn = document.getElementById('whatsappCancelBtn');
  const whatsappPhoneWarning = document.getElementById('whatsappPhoneWarning');

  const thankyouModal = document.getElementById('thankyouModal');
  const thankyouGuestName = document.getElementById('thankyouGuestName');
  const thankyouGuestPhone = document.getElementById('thankyouGuestPhone');
  const thankyouPreview = document.getElementById('thankyouPreview');
  const thankyouConfirmBtn = document.getElementById('thankyouConfirmBtn');
  const thankyouCancelBtn = document.getElementById('thankyouCancelBtn');
  const thankyouPhoneWarning = document.getElementById('thankyouPhoneWarning');

  let attendance = [];
  let pendingGuest = null;
  let thankyouGuest = null;

  function showToast(msg, type = 'success') {
    const colors = { success: 'bg-emerald-600', error: 'bg-red-600', info: 'bg-slate-700' };
    const t = document.createElement('div');
    t.className = `${colors[type]} text-white px-4 py-3 rounded-xl shadow-lg text-sm font-medium animate-fade-in`;
    t.textContent = msg;
    toastContainer.appendChild(t);
    setTimeout(() => { t.classList.add('opacity-0', 'transition-opacity'); setTimeout(() => t.remove(), 300); }, 3500);
  }

  function formatDate(d) {
    if (!d) return '—';
    const dt = new Date(String(d).replace(' ', 'T'));
    return isNaN(dt.getTime()) ? d : dt.toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
  }

  function escapeHtml(s) {
    const el = document.createElement('div');
    el.textContent = s || '';
    return el.innerHTML;
  }

  function fillTemplate(tpl, g) {
    return tpl
      .replace('{nom}', g.nom || '')
      .replace('{event_title}', g.event_title || 'votre événement')
      .replace('{event_date}', formatDate(g.event_date))
      .replace('{event_location}', g.event_location || '—')
      .replace('{support_phone}', supportPhone);
  }

  function buildMessage(g) {
    return fillTemplate(template, g);
  }

  function buildThankyouMessage(g) {
    return fillTemplate(thankyouTemplate, g);
  }

  function buildWhatsAppUrl(phone, message) {
    const cleanPhone = (phone || '').replace(/[^0-9+]/g, '').replace(/^\+/, '');
    if (!cleanPhone) return null;
    return `https://wa.me/${encodeURIComponent(cleanPhone)}?text=${encodeURIComponent(message)}`;
  }

  function openModal(g) {
    pendingGuest = g;
    const hasPhone = !!(g.telephone || '').replace(/\D/g, '');
    whatsappGuestName.textContent = g.nom;
    whatsappGuestPhone.textContent = g.telephone || 'Non renseigné';
    whatsappPreview.textContent = buildMessage(g);
    whatsappPhoneWarning.classList.toggle('hidden', hasPhone);
    whatsappConfirmBtn.disabled = false;
    whatsappConfirmBtn.textContent = hasPhone ? 'Valider et ouvrir WhatsApp' : 'Valider sans WhatsApp';
    whatsappModal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
  }

  function closeModal() {
    whatsappModal.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
    pendingGuest = null;
  }

  function openThankyouModal(g) {
    thankyouGuest = g;
    const hasPhone = !!(g.telephone || '').replace(/\D/g, '');
    thankyouGuestName.textContent = g.nom;
    thankyouGuestPhone.textContent = g.telephone || 'Non renseigné';
    thankyouPreview.textContent = buildThankyouMessage(g);
    thankyouPhoneWarning.classList.toggle('hidden', hasPhone);
    thankyouConfirmBtn.disabled = !hasPhone;
    thankyouModal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
  }

  function closeThankyouModal() {
    thankyouModal.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
    thankyouGuest = null;
  }

  function renderRows(data) {
    guestList.innerHTML = '';
    if (!eventSelect.value) {
      emptyState.classList.remove('hidden');
      emptyState.querySelector('p').textContent = 'Sélectionnez un événement.';
      return;
    }
    if (!data.length) {
      emptyState.classList.remove('hidden');
      emptyState.querySelector('p').textContent = 'Aucun invité pour cet événement.';
      return;
    }
    emptyState.classList.add('hidden');

    data.forEach(g => {
      const validated = g.est_present == 1 || g.statut === 'present';
      const thanked = g.remerciement_envoye == 1;
      const thankAction = thanked
        ? `<div class="text-center min-w-[5.5rem]">
            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-sky-500/15 text-sky-400">Remercié</span>
            <p class="text-xs text-slate-500 mt-1.5">${escapeHtml(g.remerciement_par || '—')}</p>
          </div>`
        : `<button data-id="${g.id_invite}" class="thank-btn px-4 py-2 rounded-xl text-sm font-semibold bg-sky-600 hover:bg-sky-500 text-white">Remercier</button>`;
      const card = document.createElement('div');
      card.className = 'bg-slate-800/60 border border-slate-700/50 rounded-2xl p-4';
      card.innerHTML = `
        <div class="flex flex-col lg:flex-row lg:items-center gap-4">
          <div class="flex-1 min-w-0">
            <p class="font-semibold text-white">${escapeHtml(g.nom)}</p>
            <p class="text-sm text-slate-400">${escapeHtml(g.telephone || '—')}</p>
            ${g.enregistrer_par ? `<p class="text-xs text-slate-500 mt-1">Validé par ${escapeHtml(g.enregistrer_par)} · ${formatDate(g.date_validation)}</p>` : ''}
          </div>
          <div class="flex items-center gap-3">
            <span class="px-3 py-1 rounded-full text-xs font-semibold ${validated ? 'bg-emerald-500/15 text-emerald-400' : 'bg-slate-700 text-slate-400'}">${validated ? 'Présent' : 'En attente'}</span>
            ${validated ? thankAction : `<button data-id="${g.id_invite}" class="validate-btn px-4 py-2 rounded-xl text-sm font-semibold bg-emerald-600 hover:bg-emerald-500 text-white">Valider</button>`}
          </div>
        </div>`;
      guestList.appendChild(card);
    });

    document.querySelectorAll('.validate-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        const g = attendance.find(x => String(x.id_invite) === btn.dataset.id);
        if (g) openModal(g);
      });
    });

    document.querySelectorAll('.thank-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        const g = attendance.find(x => String(x.id_invite) === btn.dataset.id);
        if (g) openThankyouModal(g);
      });
    });
  }

  async function loadEvents() {
    const res = await fetch(`${baseUrl}/get_eventsController.php`, { credentials: 'same-origin' });
    const events = await res.json();
    eventSelect.innerHTML = '<option value="" disabled selected>— Choisir un événement —</option>';
    (Array.isArray(events) ? events : []).forEach(ev => {
      const opt = document.createElement('option');
      opt.value = ev.id_even;
      opt.textContent = `${ev.title} — ${formatDate(ev.event_date)}`;
      eventSelect.appendChild(opt);
    });
  }

  async function loadAttendance(eventId) {
    if (!eventId) {
      attendance = [];
      statsTotal.textContent = statsPresent.textContent = statsAbsent.textContent = '0';
      renderRows([]);
      return;
    }
    try {
      const res = await fetch(`${baseUrl}/get_event_attendanceController.php?event_id=${eventId}`, { credentials: 'same-origin' });
      attendance = await res.json();
      if (!Array.isArray(attendance)) attendance = [];
      const present = attendance.filter(g => g.statut === 'present' || g.est_present == 1).length;
      statsTotal.textContent = attendance.length;
      statsPresent.textContent = present;
      statsAbsent.textContent = attendance.length - present;
      renderRows(attendance);
    } catch {
      showToast('Erreur de chargement.', 'error');
    }
  }

  whatsappCancelBtn.addEventListener('click', closeModal);
  whatsappModal.addEventListener('click', e => { if (e.target === whatsappModal) closeModal(); });

  thankyouCancelBtn.addEventListener('click', closeThankyouModal);
  thankyouModal.addEventListener('click', e => { if (e.target === thankyouModal) closeThankyouModal(); });

  thankyouConfirmBtn.addEventListener('click', async () => {
    if (!thankyouGuest) return;
    thankyouConfirmBtn.disabled = true;
    thankyouConfirmBtn.textContent = 'Traitement…';
    try {
      const res = await fetch(`${baseUrl}/mark_thankyouController.php`, {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id_invite=${thankyouGuest.id_invite}&event_id=${eventSelect.value}`
      });
      const data = await res.json();
      if (data.status !== 'success') throw new Error(data.message || 'Erreur.');
      closeThankyouModal();
      if (data.notify?.whatsapp_url) {
        window.open(data.notify.whatsapp_url, '_blank');
        showToast('Remerciement enregistré — WhatsApp ouvert.');
      } else {
        showToast('Remerciement enregistré (numéro manquant pour WhatsApp).', 'info');
      }
      await loadAttendance(eventSelect.value);
    } catch (err) {
      showToast(err.message || 'Erreur.', 'error');
      thankyouConfirmBtn.disabled = false;
      thankyouConfirmBtn.textContent = 'Ouvrir WhatsApp';
    }
  });

  whatsappConfirmBtn.addEventListener('click', async () => {
    if (!pendingGuest) return;
    whatsappConfirmBtn.disabled = true;
    whatsappConfirmBtn.textContent = 'Traitement…';
    try {
      const res = await fetch(`${baseUrl}/mark_presence_validationController.php`, {
        method: 'POST', credentials: 'same-origin',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id_invite=${pendingGuest.id_invite}&statut=present&validate=1`
      });
      const data = await res.json();
      if (data.status !== 'success') throw new Error(data.message || 'Erreur.');
      closeModal();
      if (data.notify?.whatsapp_url) {
        window.open(data.notify.whatsapp_url, '_blank');
        showToast('Présence validée — WhatsApp ouvert.');
      } else {
        showToast('Présence validée.');
      }
      await loadAttendance(eventSelect.value);
    } catch (err) {
      showToast(err.message || 'Erreur.', 'error');
      whatsappConfirmBtn.disabled = false;
      whatsappConfirmBtn.textContent = 'Valider et ouvrir WhatsApp';
    }
  });

  eventSelect.addEventListener('change', () => loadAttendance(eventSelect.value));
  loadEvents();
});
