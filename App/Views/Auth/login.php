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
                <a href="<?= base_url('reset/password') ?>" class="forgot-link">Forgot password?</a>
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
                url: '<?= base_url('/api/captcha') ?>',
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
                        url: '<?= base_url('login') ?>',
                        data: $('#loginForm').serialize(),
                        dataType: 'json',
                        success: function(response) {
                            if (response.success == true) {
                                showMessage('success', 'Login successful! Redirecting...');
                                injectStyle('<?= base_url('assets/css/dashboard') ?>');
                                setTimeout(() => {
                                    window.location.href = response.redirectUrl || '<?= base_url('dashboard') ?>';
                                }, 1500);
                            } else {
                                showMessage('error', response.message || 'Login failed. Please try again.');
                                grecaptcha.reset();
                            }
                        },
                        error: function(xhr) {
                            try {
                                const res = JSON.parse(xhr.responseText);
                                showMessage('error', res.message || 'An error occurred1.', false);
                                grecaptcha.reset();
                            } catch {
                                showMessage('error', 'An error occurred. Please try again.', false);
                            }
                        },
                        complete: () => loginBtn.html(originalText).prop('disabled', false),
                    });
                },
                error: function(xhr) {
                    try {
                        const res = JSON.parse(xhr.responseText);
                        showMessage('error', res.message || 'An error occurred2.', false);
                        grecaptcha.reset();
                    } catch {
                        showMessage('error', 'An error occurred. Please try again.', false);
                    }
                },
                complete: () => loginBtn.html(originalText).prop('disabled', false),
            });

            function injectStyle(href) {
                const link = document.createElement('link');
                link.rel = 'stylesheet';
                link.href = href;
                document.head.appendChild(link);
            }

        });
    });
</script>