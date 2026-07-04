document.addEventListener('DOMContentLoaded', () => {
  const guestList = document.getElementById('guestList');
  const eventSelect = document.getElementById('eventSelect');
  const searchInput = document.getElementById('search');
  const hostessMessage = document.getElementById('hostessMessage');

  let guests = [];

  function renderGuests(data) {
    guestList.innerHTML = '';
    if (!data.length) {
      hostessMessage.innerText = 'Aucun invité trouvé pour cet événement.';
      hostessMessage.classList.remove('hidden');
      return;
    }

    hostessMessage.classList.add('hidden');
    data.forEach(invite => {
      const row = document.createElement('tr');
      row.className = 'border-b border-purple-700 flex flex-col md:table-row';
      const statusLabel = invite.statut === 'present' ? 'Présent' : 'Absent';
      const actionButton = invite.statut === 'present'
        ? '<span class="text-gray-300">Déjà présent</span>'
        : `<button data-id="${invite.id_invite}" class="bg-green-500 hover:bg-green-600 text-sm px-4 py-2 rounded font-medium transition markBtn">Marquer Présent</button>`;

      row.innerHTML = `
        <td class="p-4 text-white text-xl font-semibold">${invite.nom}</td>
        <td class="p-4 text-center text-white">${statusLabel}</td>
        <td class="p-4 flex gap-3 justify-center md:justify-end">${actionButton}</td>
      `;
      guestList.appendChild(row);
    });
    bindMarkButtons();
  }

  function bindMarkButtons() {
    document.querySelectorAll('.markBtn').forEach(btn => {
      btn.addEventListener('click', async () => {
        const popup = window.open('', '_blank');
        try {
          const response = await fetch(`${baseUrl}/mark_presentController.php`, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id_invite=${btn.dataset.id}&statut=present&validate=1&event_id=${eventSelect.value || ''}`
          });
          const data = await response.json();
          if (data.notify && data.notify.whatsapp_url) {
            if (popup) {
              popup.location.href = data.notify.whatsapp_url;
            } else {
              window.open(data.notify.whatsapp_url, '_blank');
            }
          } else {
            if (popup) popup.close();
            if (data.notify && data.notify.whatsapp === 'missing_phone') {
              alert('Numéro de téléphone manquant pour cet invité.');
            } else {
              alert('Impossible d\'ouvrir WhatsApp pour cet invité.');
            }
          }
        } catch (error) {
          if (popup) popup.close();
          console.error('Erreur lors de la mise à jour du statut :', error);
          alert('Erreur lors de l\'envoi du message WhatsApp.');
        }
        loadGuests(eventSelect.value);
      });
    });
  }

  const baseUrl = '/welcomy/Backend/controllers';

  async function loadEvents() {
    try {
      const res = await fetch(`${baseUrl}/get_eventsController.php`, { credentials: 'same-origin' });
      const events = await res.json();
      eventSelect.innerHTML = '<option value="" disabled selected>-- Sélectionner un événement --</option>';
      (Array.isArray(events) ? events : []).forEach(event => {
        const option = document.createElement('option');
        option.value = event.id_even;
        option.text = event.title + (event.event_date ? ' — ' + event.event_date : '');
        eventSelect.appendChild(option);
      });
      guestList.innerHTML = '';
      hostessMessage.innerText = 'Sélectionnez un événement pour afficher les invités.';
      hostessMessage.classList.remove('hidden');
    } catch (error) {
      console.error('Erreur chargement événements :', error);
      hostessMessage.innerText = 'Impossible de charger les événements.';
      hostessMessage.classList.remove('hidden');
    }
  }

  async function loadGuests(eventId) {
    if (!eventId) {
      guestList.innerHTML = '';
      hostessMessage.innerText = 'Sélectionnez un événement pour afficher les invités.';
      hostessMessage.classList.remove('hidden');
      return;
    }

    try {
      const res = await fetch(`${baseUrl}/get_invitesController.php?event_id=${eventId}`, { credentials: 'same-origin' });
      const data = await res.json();
      guests = Array.isArray(data) ? data : [];
      renderGuests(guests);
    } catch (error) {
      console.error('Erreur lors de la récupération des invités :', error);
      hostessMessage.innerText = 'Erreur lors de la récupération des invités.';
      hostessMessage.classList.remove('hidden');
    }
  }

  function filterGuests() {
    const query = searchInput.value.trim().toLowerCase();
    const filtered = guests.filter(invite => invite.nom.toLowerCase().includes(query));
    renderGuests(filtered);
  }

  eventSelect.addEventListener('change', () => loadGuests(eventSelect.value));
  searchInput.addEventListener('input', filterGuests);

  loadEvents();
});
  