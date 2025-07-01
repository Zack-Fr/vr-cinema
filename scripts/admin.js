// client/scripts/admin.js

document.addEventListener('DOMContentLoaded', () => {
  const contentEl  = document.getElementById('admin-content');
  const btnAddFilm = document.getElementById('btn-add-film');
  const btnImportMovies = document.getElementById('btn-import-movies');
  const btnConfigureLayout = document.getElementById('btn-configure-seat-layout');
  
  btnAddFilm.addEventListener('click', renderAddFilmForm);
  btnImportMovies.addEventListener('click',renderImportMoviesForm)
  btnConfigureLayout.addEventListener('Click', renderConfigureSeatLayoutForm);
  
  function clearContent() {
    contentEl.innerHTML = '';
  }
  
  function renderImportMoviesForm() {
    clearContent();
    const tpl   = document.getElementById('tmpl-import-movies-form');
    const clone = tpl.content.cloneNode(true);
    contentEl.appendChild(clone);

    const form       = document.getElementById('import-movies-form');
    form.addEventListener('submit', handleImportMoviesSubmit);
  }

  function renderAddFilmForm() {
    clearContent();
    const tpl   = document.getElementById('tmpl-add-film-form');
    const clone = tpl.content.cloneNode(true);
    contentEl.appendChild(clone);

    const form = contentEl.querySelector('#add-film-form');
    form.addEventListener('submit', handleAddFilmSubmit);
  }
  function renderConfigureSeatLayoutForm() {
  clearContent();
  const tpl   = document.getElementById('tmpl-configure-seat-layout-form');
  const clone = tpl.content.cloneNode(true);
  contentEl.appendChild(clone);
  
  // Attach submit handler
  const form = contentEl.querySelector('#configure-seat-layout-form');
  form.addEventListener('submit', handleConfigureSeatLayoutSubmit);
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
  async function handleImportMoviesSubmit(event) {
    event.preventDefault();
    const feedbackEl = document.getElementById('import-movies-feedback');
    feedbackEl.innerText = '';

    const raw = document.getElementById('movies-json').value;
    let movies;
    try {
      movies = JSON.parse(raw);
      if (!Array.isArray(movies)) throw new Error();
    } catch {
      feedbackEl.style.color = 'var(--failed)';
      feedbackEl.innerText = 'Invalid JSON—must be an array of movie objects.';
      return;
    }

    try {
      const res = await api.post('/admin/import-movies', { movies });
      feedbackEl.style.color = 'var(--success)';
      feedbackEl.innerText = 
        `Imported ${res.data.imported.length} movies successfully.`;
    } catch (err) {
      console.error('Import Movies error', err);
      feedbackEl.style.color = 'var(--failed)';
      feedbackEl.innerText = err.response?.data?.error || 'Import failed.';
    }
  }
});
