// client/scripts/movies.js

document.addEventListener('DOMContentLoaded', async () => {
const grid = document.getElementById("movies-grid");

// 1. Fetch the list of movies
try {
  const res = await api.get('/get_movies');
  console.log(`Raw response:`, res.data);

  const movies = Array.isArray(res.data.movies_array) 
  ? res.data.movies_array
  : Array.isArray(res.data.payload)
  ? res.data.payload
  : [];
  // console.log('Movies array',movies);
  // 2. Clear any placeholder content
  grid.innerHTML = '';
  
  // 3. Render each movie as a card
    movies.forEach(movie => {
    const card = document.createElement('div');
    card.className = 'movie-card';

      // Poster (fallback to placeholder if none provided)
    const img = document.createElement('img');
    // img.src = movie.poster_url || 
    //     `https://via.placeholder.com/300x450?text=${encodeURIComponent(movie.title)}`;
    img.alt = movie.title;
    card.appendChild(img);

    //   Title
    const title = document.createElement('h3');
    title.innerText = movie.title;
    card.appendChild(title);

      // “View Showtimes” button
      const btn = document.createElement('button');
      btn.className = 'button';
      btn.innerText = 'View Showtimes';
      btn.addEventListener('click', () => {
        // Navigate to showtimes page with movie ID in query string
        window.location.href = `showtimes.html?id=${movie.id}`;
    });
    card.appendChild(btn);

    grid.appendChild(card);
    });

    // 4. Handle case: no movies returned
    if (movies.length === 0) {
    grid.innerHTML = '<p class="text-center">No movies are currently available.</p>';
    }

} catch (err) {
    console.error('Error fetching movies:', err);
    grid.innerHTML = '<p class="text-center">Failed to load movies. Please try again later.</p>';
}
});
