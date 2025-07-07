document.addEventListener('DOMContentLoaded', getMovies);

//helper function
function renderMovies(movies) {
    const moviesSection = document.getElementById("movies-grid");
    moviesSection.innerHTML = "";

    movies.forEach((movie) => {
        moviesSection.innerHTML += movieCard(movie);
        // console.log(movie);
});
}

async function getMovies() {
try {
    const res = await api.get('/get_movies');
    console.log("GET Success:", res.data);
    renderMovies(res.data);
    

} catch (err) {
    console.error("GET Error:", err);
    alert("Failed to load movies.");
}
// getMovies();
}   


