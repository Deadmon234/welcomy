const inviteForm = document.getElementById('inviteForm');
const openInviteModal = document.getElementById('openInviteModal');
const inviteModal = document.getElementById('inviteModal');
const cancelInviteModal = document.getElementById('cancelInviteModal');
const cancelInviteModalBottom = document.getElementById('cancelInviteModalBottom');

inviteForm.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
  
    fetch('../../../Backend/controllers/create_inviteController.php', {
      method: 'POST',
      credentials: 'same-origin',
      body: formData
    })
    .then(response => response.json()) // On attend du JSON
    .then(data => {
      const messageDiv = document.getElementById('message');
      if (data.status === 'success') {
        messageDiv.innerHTML = `<span class="text-green-400">${data.message}</span>`;
        setTimeout(() => {
          inviteModal.classList.add('hidden');
          window.location.href = "dashboardHotesse.php";
        }, 1000);
      } else {
        messageDiv.innerHTML = `<span class="text-red-400">${data.message}</span>`;
      }
    })
    .catch(error => {
      console.error('Erreur fetch:', error);
      document.getElementById('message').innerHTML = `<span class="text-red-400">Erreur de connexion.</span>`;
    });
});

openInviteModal.addEventListener('click', () => inviteModal.classList.remove('hidden'));
cancelInviteModal.addEventListener('click', (e) => {
  e.preventDefault();
  inviteModal.classList.add('hidden');
});

if (cancelInviteModalBottom) {
  cancelInviteModalBottom.addEventListener('click', (e) => {
    e.preventDefault();
    inviteModal.classList.add('hidden');
  });
}

// load events into the select if present
document.addEventListener('DOMContentLoaded', async () => {
  const sel = document.getElementById('eventSelectForm');
  if (!sel) return;
  try {
    const resp = await fetch('../../../Backend/controllers/get_eventsController.php');
    const data = await resp.json();
    (Array.isArray(data) ? data : []).forEach(ev => {
      const opt = document.createElement('option');
      opt.value = ev.id_even;
      opt.text = ev.title + (ev.event_date ? ' — ' + ev.event_date : '');
      sel.appendChild(opt);
    });
  } catch (e) { console.error('Erreur chargement events', e); }
});
  