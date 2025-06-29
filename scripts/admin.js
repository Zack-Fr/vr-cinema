// client/scripts/admin.js

document.addEventListener('DOMContentLoaded', () => {
  const contentEl  = document.getElementById('admin-content');
  const btnAddFilm = document.getElementById('btn-add-film');

  btnAddFilm.addEventListener('click', renderAddFilmForm);

  function clearContent() {
    contentEl.innerHTML = '';
  }

  function renderAddFilmForm() {
    clearContent();
    const tpl   = document.getElementById('tmpl-add-film-form');
    const clone = tpl.content.cloneNode(true);
    contentEl.appendChild(clone);

    const form = contentEl.querySelector('#add-film-form');
    form.addEventListener('submit', handleAddFilmSubmit);
  }

  async function handleAddFilmSubmit(event) {
    event.preventDefault();
    const form       = event.target;
    const feedbackEl = document.getElementById('add-film-feedback');
    feedbackEl.innerText = '';

    // Gather form data
    const data = {
      title       : form.title.value.trim(),
      cast        : form.cast.value.trim(),
      genre       : form.genre.value.trim(),
      rating      : form.rating.value.trim(),
      is_upcoming : form.is_upcoming.checked ? 1 : 0,
      release_date: form.release_date.value,
      description : form.description.value.trim()
    };

    // Basic validation
    if (!data.title || !data.cast || !data.genre) {
      feedbackEl.innerText = 'Please fill in all required fields.';
      return;
    }

    try {
      const res = await api.post('/admin/add-film', data);
      const id  = res.data.movie_id;
      feedbackEl.style.color = 'var(--success)';
      feedbackEl.innerText    = `Film added! New movie ID: ${id}.`;
      form.reset();
    } catch (err) {
      console.error('Add Film error', err);
      feedbackEl.style.color = 'var(--failed)';
      feedbackEl.innerText    = err.response?.data?.error || 'Failed to add film.';
    }
  }
});
