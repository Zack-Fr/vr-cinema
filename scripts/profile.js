// client/scripts/profile.js

document.addEventListener('DOMContentLoaded', async () => {
  try {
    // 1. Fetch profile info
    const profileRes = await api.get('/profile');
    const profile    = profileRes.data;

    // Populate profile fields
    document.getElementById('profile-name').innerText   = profile.name || '—';
    document.getElementById('profile-email').innerText  = profile.email || '—';
    document.getElementById('profile-genre').innerText  = profile.genre || '—';
    document.getElementById('profile-points').innerText = profile.points ?? '0';
    document.getElementById('profile-age').innerText    = profile.age ?? '—';

    // 2. Determine bookings list
    let bookings = profile.bookings;
    if (!Array.isArray(bookings)) {
      // Fallback to separate endpoint if needed
    const bookRes = await api.get('/bookings');
    bookings      = Array.isArray(bookRes.data.bookings)
                    ? bookRes.data.bookings
                    : bookRes.data;
    }

    // 3. Render each booking
    const list = document.getElementById('booking-list');
    list.innerHTML = ''; // clear placeholder

    bookings.forEach(b => {
    const li = document.createElement('li');
    li.className = 'booking-card';

      // Left: details
    const details = document.createElement('div');
    details.className = 'booking-details';
    details.innerHTML = `
        <span><strong>Movie:</strong> ${b.movie_title}</span>
        <span><strong>Showtime:</strong> ${new Date(b.showtime).toLocaleString()}</span>
        <span><strong>Seats:</strong> ${Array.isArray(b.seats) ? b.seats.join(', ') : b.seats}</span>
    `;

      // Right: status badge
    const status = document.createElement('div');
    status.className = `booking-status ${b.status.toLowerCase()}`;
    status.innerText = b.status.charAt(0).toUpperCase() + b.status.slice(1);

    li.appendChild(details);
    li.appendChild(status);
    list.appendChild(li);
    });

} catch (err) {
    console.error('Error loading profile or bookings:', err);
    alert(err.response?.data?.error || 'Failed to load profile data.');
}
});
