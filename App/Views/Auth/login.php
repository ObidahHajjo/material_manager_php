<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Material Manager - Login</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>

    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-card">
            <div class="brand-section">
                <div class="brand-icon">
                    <i class="fas fa-boxes-stacked"></i>
                </div>
                <h1 class="brand-title">Material Manager</h1>
                <p class="brand-subtitle">Employee Portal</p>
            </div>

            <div class="error-message" id="errorMessage">
                <i class="fas fa-exclamation-circle me-2"></i>
                <span id="errorText"></span>
            </div>

            <div class="success-message" id="successMessage">
                <i class="fas fa-check-circle me-2"></i>
                <span id="successText"></span>
            </div>

            <form id="loginForm">
                <div class="form-floating">
                    <input type="email" name="email" class="form-control" id="email" placeholder="name@company.com" required>
                    <label for="email">Email Address</label>
                </div>

                <div class="form-floating password-container">
                    <input type="password" name="password" class="form-control" id="password" placeholder="Password" required>
                    <label for="password">Password</label>
                    <button type="button" class="password-toggle" id="togglePassword">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>

                <div class="remember-forgot">
                    <a href="/reset/password" class="forgot-link">Forgot password?</a>
                </div>

                <div class="mt-5 d-flex justify-content-center">
                    <div class="g-recaptcha" data-sitekey="6LdJ2VwrAAAAALSHFCCsqGMQyE0DeB2rORaDhQbi"></div>
                </div>

                <button type="submit" class="btn login-btn" id="loginButton">
                    <i class="fas fa-sign-in-alt me-2"></i>
                    Sign In
                </button>
            </form>

            <div class="footer-text">
                For internal use only. Contact IT support if you need assistance.
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Password toggle functionality
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');

            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);

                const icon = this.querySelector('i');
                icon.classList.toggle('fa-eye');
                icon.classList.toggle('fa-eye-slash');
            });

            // Show/hide messages
            function showMessage(type, message) {
                const errorDiv = document.getElementById('errorMessage');
                const successDiv = document.getElementById('successMessage');

                // Hide both messages first
                errorDiv.style.display = 'none';
                successDiv.style.display = 'none';

                if (type === 'error') {
                    document.getElementById('errorText').textContent = message;
                    errorDiv.style.display = 'block';
                } else if (type === 'success') {
                    document.getElementById('successText').textContent = message;
                    successDiv.style.display = 'block';
                }
            }

            // Form validation
            function validateForm(email, password) {
                if (!email || !password) {
                    showMessage('error', 'Please fill in all fields.');
                    return false;
                }

                if (!email.includes('@')) {
                    showMessage('error', 'Please enter a valid email address.');
                    return false;
                }

                if (password.length < 6) {
                    showMessage('error', 'Password must be at least 6 characters long.');
                    return false;
                }

                return true;
            }

            // Form submission

            $('#loginForm').on('submit', function(e) {
                e.preventDefault();

                const email = $('#email').val().trim();
                const password = $('#password').val();
                const loginBtn = $('#loginButton');

                if (!validateForm(email, password)) {
                    return;
                }

                $('#errorMessage').hide();
                $('#successMessage').hide();

                const originalText = loginBtn.html();
                loginBtn.html('<i class="fas fa-spinner fa-spin me-2"></i>Signing in...');
                loginBtn.prop('disabled', true);

                $.ajax({
                    type: 'POST',
                    url: '/api/captcha',
                    data: $('#loginForm').serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (!response.success) {
                            showMessage('error', response.message || 'CAPTCHA failed.');
                            loginBtn.html(originalText).prop('disabled', false);
                            return;
                        }
                        $.ajax({
                            type: 'POST',
                            url: '/login',
                            data: $('#loginForm').serialize(),
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    showMessage('success', 'Login successful! Redirecting...');
                                    setTimeout(() => {
                                        window.location.href = response.redirectUrl || '/dashboard';
                                    }, 1500);
                                } else {
                                    showMessage('error', response.message || 'Login failed. Please try again.');
                                    loginBtn.html(originalText).prop('disabled', false);
                                }
                            },
                            error: function(xhr, status, error) {
                                showMessage('error', 'Erreur de communication avec le serveur.');
                                console.error("AJAX Error", xhr.responseText, status, error);
                                loginBtn.html(originalText).prop('disabled', false);
                            }
                        });
                    },
                    error: function(xhr, status, error) {
                        showMessage('error', 'Erreur de communication avec le serveur.');
                        console.error("AJAX Error", xhr.responseText, status, error);
                        loginBtn.html(originalText).prop('disabled', false);
                    }
                });
            });
        });
    </script>
</body>

</html>