function switchTab(tab) {
    const registerForm = document.getElementById('register-form');
    const loginForm = document.getElementById('login-form');
    const btnRegister = document.getElementById('btn-register');
    const btnLogin = document.getElementById('btn-login');

    if (tab === 'login') {
        // Show Login, Hide Register
        registerForm.classList.add('hidden');
        loginForm.classList.remove('hidden');
        
        // Update Tab Classes
        btnLogin.classList.add('active');
        btnLogin.classList.remove('inactive');
        
        btnRegister.classList.add('inactive');
        btnRegister.classList.remove('active');
    } else {
        // Show Register, Hide Login
        loginForm.classList.add('hidden');
        registerForm.classList.remove('hidden');
        
        // Update Tab Classes
        btnRegister.classList.add('active');
        btnRegister.classList.remove('inactive');
        
        btnLogin.classList.add('inactive');
        btnLogin.classList.remove('active');
    }
}

// Check the URL hash when the page finishes loading
document.addEventListener("DOMContentLoaded", () => {
    if (window.location.hash === "#login") {
        switchTab('login');
    } else if (window.location.hash === "#register") {
        switchTab('register');
    }
});