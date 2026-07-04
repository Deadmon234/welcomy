document.addEventListener('DOMContentLoaded', () => {
  const cfg = window.HOSTESS_CONFIG || {};
  const baseUrl = cfg.baseUrl || '/welcomy/Backend/controllers';
  const template = cfg.whatsappTemplate || '';
  const supportPhone = cfg.supportPhone || '';

  const guestList = document.getElementById('guestList');
  const eventSelect = document.getElementById('eventSelect');
  const searchInput = document.getElementById('search');
  const emptyState = document.getElementById('emptyState');
  const statsTotal = document.getElementById('statTotal');
  const statsPresent = document.getElementById('statPresent');
  const statsAbsent = document.getElementById('statAbsent');
  const eventInfoCard = document.getElementById('eventInfoCard');
  const eventTitleEl = document.getElementById('eventTitle');
  const eventMetaEl = document.getElementById('eventMeta');
  const filterBtns = document.querySelectorAll('[data-filter]');
  const toastContainer = document.getElementById('toastContainer');

  const whatsappModal = document.getElementById('whatsappModal');
  const whatsappGuestName = document.getElementById('whatsappGuestName');
  const whatsappGuestPhone = document.getElementById('whatsappGuestPhone');
  const whatsappPreview = document.getElementById('whatsappPreview');
  const whatsappConfirmBtn = document.getElementById('whatsappConfirmBtn');
  const whatsappCancelBtn = document.getElementById('whatsappCancelBtn');
  const whatsappPhoneWarning = document.getElementById('whatsappPhoneWarning');

  const inviteModal = document.getElementById('inviteModal');
  const inviteForm = document.getElementById('inviteForm');
  const openInviteModal = document.getElementById('openInviteModal');
  const cancelInviteModal = document.getElementById('cancelInviteModal');
  const inviteMessage = document.getElementById('inviteMessage');
  const eventSelectForm = document.getElementById('eventSelectForm');

  let guests = [];
  let events = [];
  let currentFilter = 'all';
  let pendingGuest = null;

  function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    const colors = {
      success: 'bg-emerald-600',
      error: 'bg-red-600',
      info: 'bg-slate-700'
    };
    toast.className = `${colors[type] || colors.info} text-white px-4 py-3 rounded-xl shadow-lg text-sm font-medium animate-fade-in flex items-center gap-2`;
    toast.innerHTML = message;
    toastContainer.appendChild(toast);
    setTimeout(() => {
      toast.classList.add('opacity-0', 'transition-opacity', 'duration-300');
      setTimeout(() => toast.remove(), 300);
    }, 3500);
  }

  function formatDate(dateStr) {
    if (!dateStr) return '—';
    const d = new Date(dateStr.replace(' ', 'T'));
    if (isNaN(d.getTime())) return dateStr;
    return d.toLocaleDateString('fr-FR', {
      weekday: 'long',
      day: 'numeric',
      month: 'long',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });
  }

  function buildWhatsAppMessage(guest) {
    return template
      .replace('{nom}', guest.nom || '')
      .replace('{event_title}', guest.event_title || 'votre événement')
      .replace('{event_date}', formatDate(guest.event_date))
      .replace('{event_location}', guest.event_location || '—')
      .replace('{support_phone}', supportPhone);
  }

  function updateStats(data) {
    const total = data.length;
    const present = data.filter(g => g.statut === 'present' || g.est_present == 1).length;
    statsTotal.textContent = total;
    statsPresent.textContent = present;
    statsAbsent.textContent = total - present;
  }

  function getFilteredGuests() {
    let list = guests;
    const query = searchInput.value.trim().toLowerCase();
    if (query) {
      list = list.filter(g =>
        g.nom.toLowerCase().includes(query) ||
        (g.telephone || '').includes(query) ||
        (g.email || '').toLowerCase().includes(query)
      );
    }
    if (currentFilter === 'present') {
      list = list.filter(g => g.statut === 'present' || g.est_present == 1);
    } else if (currentFilter === 'absent') {
      list = list.filter(g => g.statut !== 'present' && g.est_present != 1);
    }
    return list;
  }

  function renderGuests() {
    const data = getFilteredGuests();
    guestList.innerHTML = '';

    if (!eventSelect.value) {
      guestList.innerHTML = '';
      emptyState.classList.remove('hidden');
      emptyState.querySelector('p').textContent = 'Sélectionnez un événement pour afficher la liste des invités.';
      return;
    }

    if (!data.length) {
      emptyState.classList.remove('hidden');
      emptyState.querySelector('p').textContent = currentFilter === 'all'
        ? 'Aucun invité inscrit pour cet événement.'
        : 'Aucun invité ne correspond à ce filtre.';
      return;
    }

    emptyState.classList.add('hidden');

    data.forEach(guest => {
      const isPresent = guest.statut === 'present' || guest.est_present == 1;
      const card = document.createElement('div');
      card.className = 'guest-card bg-slate-800/60 border border-slate-700/50 rounded-2xl p-4 hover:border-slate-600 transition-all';
      card.innerHTML = `
        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
          <div class="flex items-center gap-3 flex-1 min-w-0">
            <div class="w-11 h-11 rounded-full flex items-center justify-center text-sm font-bold shrink-0 ${isPresent ? 'bg-emerald-500/20 text-emerald-400' : 'bg-slate-700 text-slate-300'}">
              ${(guest.nom || '?').charAt(0).toUpperCase()}
            </div>
            <div class="min-w-0">
              <p class="font-semibold text-white truncate">${escapeHtml(guest.nom)}</p>
              <p class="text-sm text-slate-400 truncate">${escapeHtml(guest.telephone || '—')}${guest.email ? ' · ' + escapeHtml(guest.email) : ''}</p>
            </div>
          </div>
          <div class="flex items-center gap-3 sm:shrink-0">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold ${isPresent ? 'bg-emerald-500/15 text-emerald-400 ring-1 ring-emerald-500/30' : 'bg-slate-700/80 text-slate-400 ring-1 ring-slate-600'}">
              <span class="w-1.5 h-1.5 rounded-full ${isPresent ? 'bg-emerald-400' : 'bg-slate-500'}"></span>
              ${isPresent ? 'Présent' : 'Absent'}
            </span>
            ${isPresent
              ? `<button disabled class="px-4 py-2 rounded-xl text-sm font-medium bg-slate-700/50 text-slate-500 cursor-not-allowed">Confirmé</button>`
              : `<button data-id="${guest.id_invite}" class="confirm-btn px-4 py-2 rounded-xl text-sm font-semibold bg-emerald-600 hover:bg-emerald-500 text-white transition-colors flex items-center gap-2">
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.625.846 5.059 2.284 7.034L.789 23.492a.75.75 0 0 0 .918.918l4.458-1.495A11.945 11.945 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.75a9.714 9.714 0 0 1-4.95-1.352l-.355-.21-3.742 1.254 1.254-3.742-.21-.355A9.714 9.714 0 0 1 2.25 12C2.25 6.615 6.615 2.25 12 2.25S21.75 6.615 21.75 12 17.385 21.75 12 21.75z"/></svg>
                  Confirmer
                </button>`
            }
          </div>
        </div>
      `;
      guestList.appendChild(card);
    });

    document.querySelectorAll('.confirm-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        const guest = guests.find(g => String(g.id_invite) === btn.dataset.id);
        if (guest) openWhatsAppModal(guest);
      });
    });
  }

  function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str || '';
    return div.innerHTML;
  }

  function resetConfirmButton(hasPhone) {
    whatsappConfirmBtn.disabled = false;
    if (!hasPhone) {
      whatsappConfirmBtn.textContent = 'Marquer présent sans WhatsApp';
      whatsappConfirmBtn.classList.remove('bg-emerald-600', 'hover:bg-emerald-500');
      whatsappConfirmBtn.classList.add('bg-amber-600', 'hover:bg-amber-500');
    } else {
      whatsappConfirmBtn.innerHTML = `
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/></svg>
        Marquer présent et ouvrir WhatsApp
      `;
      whatsappConfirmBtn.classList.add('bg-emerald-600', 'hover:bg-emerald-500');
      whatsappConfirmBtn.classList.remove('bg-amber-600', 'hover:bg-amber-500');
    }
  }

  function openWhatsAppModal(guest) {
    pendingGuest = guest;
    const message = buildWhatsAppMessage(guest);
    const hasPhone = !!(guest.telephone || '').replace(/\D/g, '');

    whatsappGuestName.textContent = guest.nom;
    whatsappGuestPhone.textContent = guest.telephone || 'Non renseigné';
    whatsappPreview.textContent = message;

    if (!hasPhone) {
      whatsappPhoneWarning.classList.remove('hidden');
    } else {
      whatsappPhoneWarning.classList.add('hidden');
    }

    resetConfirmButton(hasPhone);
    whatsappModal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
  }

  function closeWhatsAppModal() {
    whatsappModal.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
    pendingGuest = null;
  }

  async function confirmPresence() {
    if (!pendingGuest) return;

    whatsappConfirmBtn.disabled = true;
    whatsappConfirmBtn.textContent = 'Traitement…';

    try {
      const response = await fetch(`${baseUrl}/mark_presentController.php`, {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id_invite=${pendingGuest.id_invite}&statut=present&validate=1&event_id=${eventSelect.value || ''}`
      });
      const data = await response.json();

      if (data.status !== 'success') {
        throw new Error(data.message || 'Erreur lors de la confirmation.');
      }

      closeWhatsAppModal();

      if (data.notify && data.notify.whatsapp_url) {
        window.open(data.notify.whatsapp_url, '_blank');
        showToast('✓ Présence confirmée — WhatsApp ouvert pour envoyer le message.');
      } else if (data.notify && data.notify.whatsapp === 'missing_phone') {
        showToast('Présence confirmée, mais aucun numéro WhatsApp disponible.', 'info');
      } else {
        showToast('Présence confirmée avec succès.');
      }

      await loadGuests(eventSelect.value);
    } catch (err) {
      console.error(err);
      showToast(err.message || 'Erreur lors de la confirmation.', 'error');
      if (pendingGuest) {
        resetConfirmButton(!!(pendingGuest.telephone || '').replace(/\D/g, ''));
      }
    }
  }

  function updateEventInfo(eventId) {
    const ev = events.find(e => String(e.id_even) === String(eventId));
    if (!ev) {
      eventInfoCard.classList.add('hidden');
      return;
    }
    eventInfoCard.classList.remove('hidden');
    eventTitleEl.textContent = ev.title;
    eventMetaEl.innerHTML = `
      <span class="flex items-center gap-1.5">
        <svg class="w-4 h-4 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        ${formatDate(ev.event_date)}
      </span>
      <span class="flex items-center gap-1.5">
        <svg class="w-4 h-4 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        ${escapeHtml(ev.location || '—')}
      </span>
    `;
  }

  async function loadEvents() {
    try {
      const res = await fetch(`${baseUrl}/get_eventsController.php`, { credentials: 'same-origin' });
      events = await res.json();
      if (!Array.isArray(events)) events = [];

      eventSelect.innerHTML = '<option value="" disabled selected>— Choisir un événement —</option>';
      eventSelectForm.innerHTML = '<option value="">— Choisir un événement —</option>';

      events.forEach(event => {
        const label = `${event.title} — ${formatDate(event.event_date)}`;
        [eventSelect, eventSelectForm].forEach(sel => {
          const opt = document.createElement('option');
          opt.value = event.id_even;
          opt.textContent = label;
          sel.appendChild(opt);
        });
      });
    } catch (err) {
      console.error(err);
      showToast('Impossible de charger les événements.', 'error');
    }
  }

  async function loadGuests(eventId) {
    if (!eventId) {
      guests = [];
      updateStats([]);
      renderGuests();
      eventInfoCard.classList.add('hidden');
      return;
    }

    guestList.innerHTML = '<div class="col-span-full text-center py-8 text-slate-400">Chargement…</div>';
    emptyState.classList.add('hidden');

    try {
      const res = await fetch(`${baseUrl}/get_invitesController.php?event_id=${eventId}`, { credentials: 'same-origin' });
      const data = await res.json();
      guests = Array.isArray(data) ? data : [];
      updateStats(guests);
      updateEventInfo(eventId);
      renderGuests();
    } catch (err) {
      console.error(err);
      showToast('Erreur lors du chargement des invités.', 'error');
      emptyState.classList.remove('hidden');
      emptyState.querySelector('p').textContent = 'Erreur lors du chargement des invités.';
    }
  }

  // Event listeners
  eventSelect.addEventListener('change', () => loadGuests(eventSelect.value));
  searchInput.addEventListener('input', renderGuests);

  filterBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      currentFilter = btn.dataset.filter;
      filterBtns.forEach(b => {
        b.classList.toggle('bg-violet-600', b === btn);
        b.classList.toggle('text-white', b === btn);
        b.classList.toggle('bg-slate-800', b !== btn);
        b.classList.toggle('text-slate-400', b !== btn);
      });
      renderGuests();
    });
  });

  whatsappConfirmBtn.addEventListener('click', confirmPresence);
  whatsappCancelBtn.addEventListener('click', closeWhatsAppModal);
  whatsappModal.addEventListener('click', e => {
    if (e.target === whatsappModal) closeWhatsAppModal();
  });

  openInviteModal.addEventListener('click', () => {
    inviteModal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
    if (eventSelect.value) eventSelectForm.value = eventSelect.value;
  });
  cancelInviteModal.addEventListener('click', () => {
    inviteModal.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
  });
  inviteModal.addEventListener('click', e => {
    if (e.target === inviteModal) {
      inviteModal.classList.add('hidden');
      document.body.classList.remove('overflow-hidden');
    }
  });

  inviteForm.addEventListener('submit', async e => {
    e.preventDefault();
    const phoneField = inviteForm.querySelector('[data-phone-field]');
    const phoneCheck = phoneField?.validate?.();
    if (!phoneCheck || !phoneCheck.ok) {
      inviteMessage.innerHTML = `<span class="text-red-400">${phoneCheck?.error || 'Numéro de téléphone invalide.'}</span>`;
      return;
    }
    const formData = new FormData(inviteForm);
    inviteMessage.textContent = '';

    try {
      const res = await fetch(`${baseUrl}/create_inviteController.php`, {
        method: 'POST',
        credentials: 'same-origin',
        body: formData
      });
      const data = await res.json();
      if (data.status === 'success') {
        inviteMessage.innerHTML = '<span class="text-emerald-400">Invité ajouté avec succès.</span>';
        inviteForm.reset();
        if (eventSelect.value) eventSelectForm.value = eventSelect.value;
        inviteForm.querySelector('[data-phone-field]')?.validate?.();
        setTimeout(async () => {
          inviteModal.classList.add('hidden');
          document.body.classList.remove('overflow-hidden');
          inviteMessage.textContent = '';
          if (eventSelect.value) await loadGuests(eventSelect.value);
          showToast('Invité ajouté avec succès.');
        }, 800);
      } else {
        inviteMessage.innerHTML = `<span class="text-red-400">${escapeHtml(data.message || 'Erreur.')}</span>`;
      }
    } catch (err) {
      inviteMessage.innerHTML = '<span class="text-red-400">Erreur de connexion.</span>';
    }
  });

  loadEvents();

  if (window.WELCOMY_PHONE) {
    WELCOMY_PHONE.initAll('#inviteForm [data-phone-field]');
  }
});
