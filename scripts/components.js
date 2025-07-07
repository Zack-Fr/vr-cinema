function movieCard(movie) {
return `<div class="movie-card">
            <img src="https://i.pravatar.cc/150?img=1" alt="movie.title" />
            <div>
            <h4>${movie.title}</h4>
            <p>${movie.cast}</p>
            </div>
        </div>`;
}