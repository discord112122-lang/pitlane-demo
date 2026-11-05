const DEMO = true;

const form = document.querySelector('#lead');
const note = document.querySelector('#note');

form.addEventListener('submit', async (e) => {
  e.preventDefault();

  const name = form.name.value.trim();
  const phone = form.phone.value.trim();
  if (!name || phone.length < 6) {
    note.textContent = 'Please enter your name and phone number.';
    return;
  }

  if (DEMO) {
    note.textContent = 'Demo mode: on the live site this request is emailed to the shop.';
    form.reset();
    return;
  }

  note.textContent = 'Sending…';
  try {
    const res = await fetch('send.php', { method: 'POST', body: new FormData(form) });
    const data = await res.json();
    note.textContent = data.message;
    if (data.ok) form.reset();
  } catch (err) {
    note.textContent = 'Could not send. Please call us: +000 000 000 000.';
  }
});