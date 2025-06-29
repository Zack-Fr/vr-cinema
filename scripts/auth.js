// client/scripts/auth.js

document.addEventListener('DOMContentLoaded', () => {
  const token = localStorage.getItem('token');

  // Identify forms
  const registerForm = document.getElementById('register-form');
  const loginForm    = document.getElementById('login-form');

  // 0. Redirect logged‐in users away from auth pages
if ((registerForm || loginForm) && token) {
    window.location.href = 'index.html';
    return;
}

  // 1. REGISTER
if (registerForm) {
    registerForm.addEventListener('submit', async e => {
    e.preventDefault();
    const name  = registerForm.name.value.trim();
    const email = registerForm.email.value.trim();
    const pw    = registerForm.password.value;
    const pw2   = registerForm.confirm_password.value;
// console.log(name);
    if (pw !== pw2) {
        alert('Passwords do not match.');
        return;
    }

    try {
        const res = await api.post('/register', { name, email, password: pw });
        localStorage.setItem('token', res.data.token);
        window.location.href = 'index.html';
    } catch (err) {
        console.error(err);
        alert(err.response?.data?.error || 'Registration failed.');
    }
    });
}

  // 2. LOGIN
if (loginForm) {
    loginForm.addEventListener('submit', async e => {
    e.preventDefault();
    const email = loginForm.email.value.trim();
    const pw    = loginForm.password.value;

      try {
        const res = await api.post('/login', { email, password: pw });
        localStorage.setItem('token', res.data.token);
        window.location.href = 'index.html';
      } catch (err) {
        console.error(err);
        alert(err.response?.data?.error || 'Login failed.');
      }
    });
  }

  // 3. LOGOUT (any page with logout-btn or logout-link)
  const logoutBtn = document.getElementById('logout-btn') || document.getElementById('logout-link');
  if (logoutBtn) {
    logoutBtn.addEventListener('click', async e => {
      e.preventDefault();
      try {
        await api.post('/logout');
      } catch (_) {
        // ignore errors
      } finally {
        localStorage.removeItem('token');
        window.location.href = 'login.html';
      }
    });
  }

  // 4. Protect other pages (if no auth form present)
  if (!registerForm && !loginForm && !token) {
    window.location.href = 'login.html';
  }
});
