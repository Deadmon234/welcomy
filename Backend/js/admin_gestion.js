document.addEventListener('DOMContentLoaded', () => {
  const cfg = window.GESTION_CONFIG || {};
  const baseUrl = cfg.baseUrl || '/welcomy/Backend/controllers';
  const currentUserId = cfg.currentUserId || 0;

  const tabEvents = document.getElementById('tabEvents');
  const tabUsers = document.getElementById('tabUsers');
  const panelEvents = document.getElementById('panelEvents');
  const panelUsers = document.getElementById('panelUsers');
  const eventsList = document.getElementById('eventsList');
  const usersList = document.getElementById('usersList');
  const eventsEmpty = document.getElementById('eventsEmpty');
  const usersEmpty = document.getElementById('usersEmpty');
  const toastContainer = document.getElementById('toastContainer');

  const createEventModal = document.getElementById('createEventModal');
  const openCreateEvent = document.getElementById('openCreateEvent');
  const cancelCreateEvent = document.getElementById('cancelCreateEvent');
  const createEventBtn = document.getElementById('createEventBtn');
  const eventTitleInput = document.getElementById('eventTitleInput');
  const eventDateInput = document.getElementById('eventDateInput');
  const eventLocationInput = document.getElementById('eventLocationInput');
  const eventDescriptionInput = document.getElementById('eventDescriptionInput');

  const createUserModal = document.getElementById('createUserModal');
  const openCreateUser = document.getElementById('openCreateUser');
  const cancelCreateUser = document.getElementById('cancelCreateUser');
  const createUserBtn = document.getElementById('createUserBtn');
  const userNameInput = document.getElementById('userNameInput');
  const userEmailInput = document.getElementById('userEmailInput');
  const userPasswordInput = document.getElementById('userPasswordInput');
  const userRoleInput = document.getElementById('userRoleInput');

  let events = [];
  let users = [];

  function showToast(msg, type = 'success') {
    const colors = { success: 'bg-emerald-600', error: 'bg-red-600', info: 'bg-slate-700' };
    const t = document.createElement('div');
    t.className = `${colors[type]} text-white px-4 py-3 rounded-xl shadow-lg text-sm font-medium animate-fade-in`;
    t.textContent = msg;
    toastContainer.appendChild(t);
    setTimeout(() => { t.classList.add('opacity-0', 'transition-opacity'); setTimeout(() => t.remove(), 300); }, 3500);
  }

  function escapeHtml(s) {
    const el = document.createElement('div');
    el.textContent = s || '';
    return el.innerHTML;
  }

  function formatDate(d) {
    if (!d) return '—';
    const dt = new Date(String(d).replace(' ', 'T'));
    return isNaN(dt.getTime()) ? d : dt.toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
  }

  function formatRole(role) {
    return role === 'admin' ? 'Administrateur' : 'Hôtesse';
  }

  function switchTab(tab) {
    const isEvents = tab === 'events';
    tabEvents.className = `tab-btn flex-1 sm:flex-none px-5 py-2.5 rounded-lg text-sm font-semibold ${isEvents ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:text-white'}`;
    tabUsers.className = `tab-btn flex-1 sm:flex-none px-5 py-2.5 rounded-lg text-sm font-semibold ${!isEvents ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:text-white'}`;
    panelEvents.classList.toggle('hidden', !isEvents);
    panelUsers.classList.toggle('hidden', isEvents);
  }

  function openModal(modal) {
    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
  }

  function closeModal(modal) {
    modal.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
  }

  function renderEvents() {
    eventsList.innerHTML = '';
    if (!events.length) {
      eventsEmpty.classList.remove('hidden');
      return;
    }
    eventsEmpty.classList.add('hidden');

    events.forEach(ev => {
      const card = document.createElement('div');
      card.className = 'bg-slate-800/60 border border-slate-700/50 rounded-2xl p-4';
      card.innerHTML = `
        <div class="flex flex-col lg:flex-row lg:items-center gap-4">
          <div class="flex-1 min-w-0">
            <p class="font-semibold text-white">${escapeHtml(ev.title)}</p>
            <p class="text-sm text-slate-400 mt-1">${formatDate(ev.event_date)} · ${escapeHtml(ev.location || '—')}</p>
            ${ev.description ? `<p class="text-xs text-slate-500 mt-1">${escapeHtml(ev.description)}</p>` : ''}
            <div class="flex flex-wrap gap-3 mt-2 text-xs text-slate-500">
              <span>Créé par ${escapeHtml(ev.creator || '—')}</span>
              <span>·</span>
              <span>${ev.total_invites || 0} invité(s)</span>
            </div>
          </div>
          <button data-id="${ev.id_even}" class="delete-event-btn px-4 py-2 rounded-xl text-sm font-semibold bg-red-600/90 hover:bg-red-500 text-white shrink-0">
            Supprimer
          </button>
        </div>`;
      eventsList.appendChild(card);
    });

    document.querySelectorAll('.delete-event-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        const ev = events.find(x => String(x.id_even) === btn.dataset.id);
        deleteEvent(btn.dataset.id, ev?.title || 'cet événement');
      });
    });
  }

  function renderUsers() {
    usersList.innerHTML = '';
    if (!users.length) {
      usersEmpty.classList.remove('hidden');
      return;
    }
    usersEmpty.classList.add('hidden');

    users.forEach(u => {
      const isSelf = String(u.id_utilisateur) === String(currentUserId);
      const roleClass = u.role === 'admin' ? 'bg-violet-500/15 text-violet-400' : 'bg-emerald-500/15 text-emerald-400';
      const card = document.createElement('div');
      card.className = 'bg-slate-800/60 border border-slate-700/50 rounded-2xl p-4';
      card.innerHTML = `
        <div class="flex flex-col lg:flex-row lg:items-center gap-4">
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
              <p class="font-semibold text-white">${escapeHtml(u.nom)}</p>
              <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold ${roleClass}">${formatRole(u.role)}</span>
              ${isSelf ? '<span class="text-xs text-slate-500">(vous)</span>' : ''}
            </div>
            <p class="text-sm text-slate-400 mt-1">${escapeHtml(u.email)}</p>
            <p class="text-xs text-slate-500 mt-1">Inscrit le ${formatDate(u.created_at)}</p>
          </div>
          ${isSelf ? '' : `<button data-id="${u.id_utilisateur}" class="delete-user-btn px-4 py-2 rounded-xl text-sm font-semibold bg-red-600/90 hover:bg-red-500 text-white shrink-0">Supprimer</button>`}
        </div>`;
      usersList.appendChild(card);
    });

    document.querySelectorAll('.delete-user-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        const u = users.find(x => String(x.id_utilisateur) === btn.dataset.id);
        deleteUser(btn.dataset.id, u?.nom || 'cet utilisateur');
      });
    });
  }

  async function loadEvents() {
    try {
      const res = await fetch(`${baseUrl}/get_eventsController.php`, { credentials: 'same-origin' });
      events = await res.json();
      if (!Array.isArray(events)) events = [];
      renderEvents();
    } catch {
      showToast('Erreur de chargement des événements.', 'error');
    }
  }

  async function loadUsers() {
    try {
      const res = await fetch(`${baseUrl}/get_usersController.php`, { credentials: 'same-origin' });
      users = await res.json();
      if (!Array.isArray(users)) users = [];
      renderUsers();
    } catch {
      showToast('Erreur de chargement des utilisateurs.', 'error');
    }
  }

  async function deleteEvent(id, name) {
    if (!confirm(`Supprimer l'événement « ${name} » ?\nToutes les listes d'invités associées seront supprimées.`)) return;
    try {
      const res = await fetch(`${baseUrl}/delete_eventController.php`, {
        method: 'POST', credentials: 'same-origin',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id_even=${id}`
      });
      const data = await res.json();
      if (data.status !== 'success') throw new Error(data.message || 'Erreur.');
      showToast('Événement supprimé.');
      await loadEvents();
    } catch (err) {
      showToast(err.message || 'Erreur.', 'error');
    }
  }

  async function deleteUser(id, name) {
    if (!confirm(`Supprimer l'utilisateur « ${name} » ?`)) return;
    try {
      const res = await fetch(`${baseUrl}/delete_userController.php`, {
        method: 'POST', credentials: 'same-origin',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id_utilisateur=${id}`
      });
      const data = await res.json();
      if (data.status !== 'success') throw new Error(data.message || 'Erreur.');
      showToast('Utilisateur supprimé.');
      await loadUsers();
    } catch (err) {
      showToast(err.message || 'Erreur.', 'error');
    }
  }

  tabEvents.addEventListener('click', () => switchTab('events'));
  tabUsers.addEventListener('click', () => { switchTab('users'); loadUsers(); });

  openCreateEvent.addEventListener('click', () => openModal(createEventModal));
  cancelCreateEvent.addEventListener('click', () => closeModal(createEventModal));
  createEventModal.addEventListener('click', e => { if (e.target === createEventModal) closeModal(createEventModal); });

  openCreateUser.addEventListener('click', () => openModal(createUserModal));
  cancelCreateUser.addEventListener('click', () => closeModal(createUserModal));
  createUserModal.addEventListener('click', e => { if (e.target === createUserModal) closeModal(createUserModal); });

  createEventBtn.addEventListener('click', async () => {
    const title = eventTitleInput.value.trim();
    const date = eventDateInput.value.trim();
    const location = eventLocationInput.value.trim();
    const description = eventDescriptionInput.value.trim();
    if (!title || !date || !location) {
      showToast('Titre, date et lieu sont requis.', 'error');
      return;
    }
    createEventBtn.disabled = true;
    createEventBtn.textContent = 'Création…';
    try {
      const body = new URLSearchParams({ title, date, location, description });
      const res = await fetch(`${baseUrl}/create_eventController.php`, {
        method: 'POST', credentials: 'same-origin',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: body.toString()
      });
      const data = await res.json();
      if (data.status !== 'success') throw new Error(data.message || 'Erreur.');
      eventTitleInput.value = eventDateInput.value = eventLocationInput.value = eventDescriptionInput.value = '';
      closeModal(createEventModal);
      showToast('Événement créé.');
      await loadEvents();
    } catch (err) {
      showToast(err.message || 'Erreur.', 'error');
    } finally {
      createEventBtn.disabled = false;
      createEventBtn.textContent = "Créer l'événement";
    }
  });

  createUserBtn.addEventListener('click', async () => {
    const nom = userNameInput.value.trim();
    const email = userEmailInput.value.trim();
    const password = userPasswordInput.value;
    const role = userRoleInput.value;
    if (!nom || !email || !password) {
      showToast('Tous les champs sont requis.', 'error');
      return;
    }
    createUserBtn.disabled = true;
    createUserBtn.textContent = 'Création…';
    try {
      const body = new URLSearchParams({ nom, email, password, role });
      const res = await fetch(`${baseUrl}/create_userController.php`, {
        method: 'POST', credentials: 'same-origin',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: body.toString()
      });
      const data = await res.json();
      if (data.status !== 'success') throw new Error(data.message || 'Erreur.');
      userNameInput.value = userEmailInput.value = userPasswordInput.value = '';
      userRoleInput.value = 'hotesse';
      closeModal(createUserModal);
      showToast(data.message || 'Utilisateur créé.');
      await loadUsers();
    } catch (err) {
      showToast(err.message || 'Erreur.', 'error');
    } finally {
      createUserBtn.disabled = false;
      createUserBtn.textContent = "Créer l'utilisateur";
    }
  });

  loadEvents();
});
