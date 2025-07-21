// JavaScript untuk form validation di halaman auth

document.addEventListener('DOMContentLoaded', function() {
    // Form validation for login and register forms
    const loginForm = document.getElementById('login-form');
    const registerForm = document.getElementById('register-form');

    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            const username = this.querySelector('input[name="username"]').value.trim();
            const password = this.querySelector('input[name="password"]').value.trim();
            
            if (!username || !password) {
                e.preventDefault();
                alert('Username dan password harus diisi!');
                return;
            }
            
            // Proceed with login logic
            console.log('Login attempt:', username);
        });
    }

    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            const username = this.querySelector('input[name="username"]').value.trim();
            const password = this.querySelector('input[name="password"]').value.trim();
            const confirmPassword = this.querySelector('input[name="confirm_password"]').value.trim();
            
            if (!username || !password || !confirmPassword) {
                e.preventDefault();
                alert('Semua field harus diisi!');
                return;
            }
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Password dan konfirmasi password tidak cocok!');
                return;
            }
            
            if (password.length < 6) {
                e.preventDefault();
                alert('Password harus minimal 6 karakter!');
                return;
            }
            
            // Proceed with registration logic
            console.log('Register attempt:', username);
        });
    }

    // Tab switching functionality
    window.switchTab = function(tab) {
        document.querySelectorAll('.auth-tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.auth-form').forEach(f => f.classList.remove('active'));
        
        if (tab === 'login') {
            document.querySelector('.auth-tab:first-child').classList.add('active');
            document.getElementById('login-form').classList.add('active');
        } else {
            document.querySelector('.auth-tab:last-child').classList.add('active');
            document.getElementById('register-form').classList.add('active');
        }
    };
}); 