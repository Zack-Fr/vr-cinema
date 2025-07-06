// client/scripts/admin.js

document.addEventListener('DOMContentLoaded', () => {
  const contentEl  = document.getElementById('admin-content');
  const btnAddFilm = document.getElementById('btn-add-film');
  const btnImportMovies = document.getElementById('btn-import-movies');
  const btnConfigureLayout = document.getElementById('btn-configure-seat-layout');
  
  btnAddFilm.addEventListener('click', renderAddFilmForm);
  btnImportMovies.addEventListener('click',renderImportMoviesForm);
  btnConfigureLayout.addEventListener('click', renderConfigureSeatLayoutForm);
  
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
  
  function renderImportMoviesForm() {
    clearContent();
    const tpl   = document.getElementById('tmpl-import-movies-form');
    const clone = tpl.content.cloneNode(true);
    contentEl.appendChild(clone);

    const form = document.getElementById('import-movies-form');
    form.addEventListener('submit', handleImportMoviesSubmit);
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
  
  function renderConfigureSeatLayoutForm() {
  clearContent();
  const tpl   = document.getElementById('tmpl-configure-seat-layout-form');
  const clone = tpl.content.cloneNode(true);
  contentEl.appendChild(clone);
  
  // Attach submit handler
  const form = document.getElementById('configure-seat-layout-form');
  form.addEventListener('submit', handleConfigureSeatLayoutSubmit);
  }
  async function handleConfigureSeatLayoutSubmit(event) {
  event.preventDefault();
  const feedbackEl = document.getElementById('configure-seat-feedback');
  feedbackEl.innerText = '';

  // Gather and validate inputs
  const showtimeId = parseInt(event.target.showtime_id.value, 10);
  let seats;
  /* This code snippet is attempting to parse the value of the `seats_json` input field as JSON data.
  If the parsing is successful and the parsed data is not an array, it throws an error. */
  try {
    seats = JSON.parse(event.target.seats_json.value);
    if (!Array.isArray(seats)) throw new Error();
  } catch {
    feedbackEl.style.color = 'var(--failed)';
    feedbackEl.innerText = 'Invalid JSON for seats—it must be an array of {row,number} objects.';
    return;
  }

  try {
    const res = await api.post('/admin/configure-seat-layout', {
      showtime_id: showtimeId,
      seats
    });
    /* `feedbackEl` is a variable that references an element in the HTML document where feedback
    messages are displayed to the user. In the code provided, `feedbackEl` is used to show feedback
    messages related to the actions performed by the user, such as adding a film, importing movies,
    or configuring seat layout. The feedback messages can indicate the success or failure of these
    actions and provide additional information to the user. */
    feedbackEl.style.color = 'var(--success)';
    feedbackEl.innerText = `Successfully created ${res.data.count} seats.`;
  } catch (err) {
    console.error('Configure Seat Layout error', err);
    feedbackEl.style.color = 'var(--failed)';
    feedbackEl.innerText = err.response?.data?.error || 'Failed to configure seat layout.';
  }
}
});
