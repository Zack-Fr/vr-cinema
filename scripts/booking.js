// client/scripts/booking.js

document.addEventListener('DOMContentLoaded', () => {
  // 1. Pull booking data from sessionStorage
  const showtimeId = sessionStorage.getItem('showtimeId');
  const seatsJson  = sessionStorage.getItem('selectedSeats');
  const seats      = seatsJson ? JSON.parse(seatsJson) : [];

  if (!showtimeId || seats.length === 0) {
    alert('No booking data found. Please select seats first.');
    window.location.href = 'index.html';
    return;
  }

  // 2. Render summary fields
  document.getElementById('booking-showtime').innerText = showtimeId;
  document.getElementById('booking-seats').innerText    = seats.join(', ');

  // 3. Price calculation (static per‐seat price for now)
  const pricePerSeat = 10.00;
  document.getElementById('booking-price').innerText = pricePerSeat.toFixed(2);

  let total = seats.length * pricePerSeat;
  document.getElementById('booking-total').innerText = total.toFixed(2);

  // 4. Coupon application
  document.getElementById('apply-coupon').addEventListener('click', async () => {
    const code = document.getElementById('coupon').value.trim();
    if (!code) {
      alert('Please enter a coupon code.');
      return;
    }
    try {
      const res = await api.post('/apply-discount', {
        showtime_id: showtimeId,
        seats,
        code
      });
      // assume response: { discountAmount: number }
      const discount = res.data.discountAmount || 0;
      total = total - discount;
      document.getElementById('booking-total').innerText = total.toFixed(2);
      alert(`Coupon applied! You saved $${discount.toFixed(2)}.`);
    } catch (err) {
      alert(err.response?.data?.error || 'Failed to apply coupon.');
    }
  });

  // 5. Confirm & Pay
  document.getElementById('booking-form').addEventListener('submit', async e => {
    e.preventDefault();

    try {
      // 5a. Final availability check
      await api.post('/check-seats', {
        showtime_id: showtimeId,
        seats
      });

      // 5b. Create booking
      const res = await api.post('/book', {
        showtime_id: showtimeId,
        seats
      });

      alert(`Booking confirmed! Your booking ID is ${res.data.booking_id}.`);
      // clear stored state
      sessionStorage.removeItem('showtimeId');
      sessionStorage.removeItem('selectedSeats');
      // redirect to profile to view booking
      window.location.href = 'profile.html';

    } catch (err) {
      if (err.response?.status === 409) {
        const taken = err.response.data.seats || [];
        alert(`Sorry, these seats are no longer available: ${taken.join(', ')}. Please choose again.`);
        // go back to seat selection
        window.location.href = `showtimes.html?id=${sessionStorage.getItem('movieId')}`;
      } else {
        alert(err.response?.data?.error || 'Booking failed. Please try again.');
      }
    }
  });
});
