function switchTab(tab) {
    // Forms
    const registerForm = document.getElementById('register-form');
    const loginForm = document.getElementById('login-form');
    const adminForm = document.getElementById('admin-form');

    // Tab Buttons
    const btnRegister = document.getElementById('btn-register');
    const btnLogin = document.getElementById('btn-login');
    const btnAdmin = document.getElementById('btn-admin');

    // 1. සියලු Forms සඟවන්න (Hide all forms)
    registerForm.classList.add('hidden');
    loginForm.classList.add('hidden');
    if (adminForm) adminForm.classList.add('hidden');

    // 2. සියලු Buttons Inactive කරන්න
    [btnRegister, btnLogin, btnAdmin].forEach(btn => {
        if (btn) {
            btn.classList.remove('active');
            btn.classList.add('inactive');
        }
    });

    // 3. තෝරාගත් Tab එක සහ Form එක පමණක් පෙන්වන්න
    if (tab === 'login') {
        loginForm.classList.remove('hidden');
        btnLogin.classList.add('active');
        btnLogin.classList.remove('inactive');
    } else if (tab === 'admin') {
        if (adminForm) adminForm.classList.remove('hidden');
        if (btnAdmin) {
            btnAdmin.classList.add('active');
            btnAdmin.classList.remove('inactive');
        }
    } else { // register tab
        registerForm.classList.remove('hidden');
        btnRegister.classList.add('active');
        btnRegister.classList.remove('inactive');
    }
}

// Check the URL hash when the page finishes loading
document.addEventListener("DOMContentLoaded", () => {
    if (window.location.hash === "#login") {
        switchTab('login');
    } else if (window.location.hash === "#admin") {
        switchTab('admin');
    } else if (window.location.hash === "#register") {
        switchTab('register');
    }
});

// Registration Form Validation
document.addEventListener("DOMContentLoaded", () => {
    const registerForm = document.getElementById('register-form');
    const dobInput = document.querySelector('input[name="dob"]');

    // Restrict date input to age 18+
    if (dobInput) {
        const today = new Date();
        const maxDate = new Date(today.getFullYear() - 18, today.getMonth(), today.getDate());
        dobInput.max = maxDate.toISOString().split("T")[0];
    }

    if (registerForm) {
        registerForm.addEventListener('submit', (e) => {
            const password = registerForm.querySelector('input[name="password"]').value;
            const confirmPassword = registerForm.querySelector('input[name="confirm_password"]').value;
            const phone = registerForm.querySelector('input[name="phone"]').value.trim();
            const dob = new Date(dobInput.value);
            const today = new Date();

            // 1. Password length check
            if (password.length < 6) {
                alert("Password must be at least 6 characters long.");
                e.preventDefault();
                return;
            }

            // 2. Password matching check
            if (password !== confirmPassword) {
                alert("Passwords do not match.");
                e.preventDefault();
                return;
            }

            // 3. Phone number format check (10 digits starting with 0)
            const phoneRegex = /^0\d{9}$/;
            if (!phoneRegex.test(phone)) {
                alert("Please enter a valid 10-digit phone number starting with 0.");
                e.preventDefault();
                return;
            }

            // 4. Age 18+ check
            let age = today.getFullYear() - dob.getFullYear();
            const monthDiff = today.getMonth() - dob.getMonth();
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
                age--;
            }

            if (age < 18) {
                alert("You must be at least 18 years old to register as a donor.");
                e.preventDefault();
                return;
            }
        });
    }
});