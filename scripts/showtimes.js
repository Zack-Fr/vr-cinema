// client/scripts/showtimes.js

document.addEventListener('DOMContentLoaded', () => {
  // Elements
  const params          = new URLSearchParams(window.location.search);
  const movieId         = params.get('id');
  const movieTitleEl    = document.getElementById('movie-title');
  const showtimeSelect  = document.getElementById('showtime-select');
  const seatMapEl       = document.getElementById('seat-map');
  const proceedBtn      = document.getElementById('proceed-btn');

  let selectedSeats = [];

  // 0. Guard: ensure we have a movie ID
  if (!movieId) {
    alert('No movie specified.');
    window.location.href = 'index.html';
    return;
  }

  // 1. Fetch movie list to display the title (adjust if you have a single-movie endpoint)
  api.get('/movies')
    .then(res => {
      const movies = res.data.movies || [];
      const movie  = movies.find(m => String(m.id) === movieId);
      movieTitleEl.innerText = movie
        ? movie.title
        : 'Pick Your Showtime';
    })
    .catch(err => {
    console.error('Error loading movie title', err);
    movieTitleEl.innerText = 'Pick Your Showtime';
    });

  // 2. Load showtimes for this movie (your API now expects `id` as the query param)
api.get('/showtimes', { params: { id: movieId } })
    .then(res => {
    const showtimes = res.data.showtimes || [];
    if (!showtimes.length) {
        alert('No showtimes available.');
        return;
    }
    showtimes.forEach(st => {
        const opt = document.createElement('option');
        opt.value = st.id;
        opt.text  = new Date(st.start_time).toLocaleString();
        showtimeSelect.appendChild(opt);
    });
    })
    .catch(err => {
    console.error('Error fetching showtimes', err);
    alert('Failed to load showtimes.');
    });

  // 3. When a showtime is selected, fetch and render the seat layout
showtimeSelect.addEventListener('change', () => {
    const showtimeId = showtimeSelect.value;
    selectedSeats = [];
    seatMapEl.innerHTML = '';
    proceedBtn.disabled = true;

    if (!showtimeId) return;

    api.get('/seat-layout', { params: { showtime_id: showtimeId } })
      .then(res => {
        const seats = res.data.seats || [];
        seats.forEach(s => {
          const seatEl = document.createElement('div');
          seatEl.classList.add('seat');
          seatEl.dataset.id = s.id;

          if (s.status === 'available') {
            seatEl.classList.add('available');
            seatEl.addEventListener('click', () => {
              const id = s.id;
              const idx = selectedSeats.indexOf(id);
              if (idx > -1) {
                selectedSeats.splice(idx, 1);
                seatEl.classList.remove('selected');
              } else {
                selectedSeats.push(id);
                seatEl.classList.add('selected');
              }
              proceedBtn.disabled = selectedSeats.length === 0;
            });
          } else {
            seatEl.classList.add('occupied');
          }

          seatMapEl.appendChild(seatEl);
        });
      })
      .catch(err => {
        console.error('Error fetching seats', err);
        alert('Failed to load seats.');
      });
  });

  // 4. Proceed to booking: stash state and redirect
  proceedBtn.addEventListener('click', () => {
    const showtimeId = showtimeSelect.value;
    sessionStorage.setItem('showtimeId', showtimeId);
    sessionStorage.setItem('selectedSeats', JSON.stringify(selectedSeats));
    window.location.href = 'booking.html';
  });
});
