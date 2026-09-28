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