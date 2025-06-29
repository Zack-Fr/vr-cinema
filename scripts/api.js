

// 1. Create an Axios instance
const api = axios.create({
  // ↓ Change this to wherever your PHP is served:
  //    e.g. 'http://localhost:8000' or 'http://localhost/cinema-vr/backend/public'
baseURL: 'http://localhost:1111/cinema-vr/backend/public/index.php',

  // send cookies/session headers if your PHP uses them
withCredentials: true,

  // default to JSON
headers: {
    'Content-Type': 'application/json'
}
});

// 2. Attach auth token (if stored in localStorage) to every request
api.interceptors.request.use(config => {
const token = localStorage.getItem('token');
if (token) {
    config.headers.Authorization = `Bearer ${token}`;
}
return config;
}, error => Promise.reject(error));

// 3. Expose globally so other scripts can call `api.get(...)`, `api.post(...)`
window.api = api;
