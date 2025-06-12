<div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="card rounded-4 p-5" style="max-width: 500px; width: 100%;">
        <h2 class="text-center text-primary fw-bold mb-2">New Password</h2>
        <p class="text-center text-muted mb-4">Create a strong new password for your account.</p>

        <div id="successMessage" class="alert alert-success d-none" role="alert"></div>
        <div id="errorMessage" class="alert alert-danger d-none" role="alert"></div>

        <form id="resetForm">
            <input type="hidden" name="token" id="token" value="<?= htmlspecialchars($token) ?>">

            <div class="mb-4">
                <label for="password" class="form-label">New Password</label>
                <div class="input-group">
                    <input type="password" name="password" id="password" class="form-control" required placeholder="Enter your new password">
                    <button class="btn btn-outline-secondary" type="button">👁️</button>
                </div>
                <div class="password-strength mt-2" id="passwordStrength">
                    <div style="height: 8px; background-color: #e9ecef; border-radius: 4px; overflow: hidden;">
                        <div class="strength-bar" style="height: 100%; width: 0%; transition: all 0.3s ease; border-radius: 4px;"></div>
                    </div>
                    <small class="strength-text text-muted mt-1 d-block"></small>
                </div>
                <div class="mt-3">
                    <ul class="m-0" style="list-style: none; padding-left: 0;">
                        <li id="lengthReq" class="requirement-item"><small><span class="requirement-icon me-2 text-danger">✗</span>At least 8 characters</small></li>
                        <li id="uppercaseReq" class="requirement-item"><small><span class="requirement-icon me-2 text-danger">✗</span>One uppercase letter</small></li>
                        <li id="lowercaseReq" class="requirement-item"><small><span class="requirement-icon me-2 text-danger">✗</span>One lowercase letter</small></li>
                        <li id="numberReq" class="requirement-item"><small><span class="requirement-icon me-2 text-danger">✗</span>One number</small></li>
                        <li id="specialReq" class="requirement-item"><small><span class="requirement-icon me-2 text-danger">✗</span>One special character</small></li>
                    </ul>
                </div>
            </div>

            <div class="mb-4">
                <label for="conf" class="form-label">Confirm Password</label>
                <div class="input-group">
                    <input type="password" name="password_confirmation" id="conf" class="form-control" required placeholder="Confirm your new password">
                    <button class="btn btn-outline-secondary" type="button">👁️</button>
                </div>
            </div>

            <button type="submit" class="btn btn-success w-100 fw-semibold">
                RESET PASSWORD
            </button>
        </form>

        <div class="text-center mt-4">
            <a href="<?= base_url('login') ?>" class="text-primary text-decoration-none">&larr; Back to Login</a>
        </div>
    </div>
</div>


<script>
    const form = document.getElementById('resetForm');
    const tokenInput = document.getElementById('token');
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('conf');
    const successMessage = document.getElementById('successMessage');
    const errorMessage = document.getElementById('errorMessage');
    const resetButton = document.querySelector('button[type="submit"]');

    const strengthBar = document.querySelector('.strength-bar');
    const strengthText = document.querySelector('.strength-text');

    const requirements = {
        length: document.getElementById('lengthReq'),
        uppercase: document.getElementById('uppercaseReq'),
        lowercase: document.getElementById('lowercaseReq'),
        number: document.getElementById('numberReq'),
        special: document.getElementById('specialReq')
    };

    function togglePassword(id) {
        const input = document.getElementById(id);
        const btn = input.nextElementSibling;
        const isVisible = input.type === 'text';
        input.type = isVisible ? 'password' : 'text';
        btn.textContent = isVisible ? '👁️' : '🙈';
    }

    function showMessage(el, msg, isSuccess = true) {
        const other = isSuccess ? errorMessage : successMessage;
        other.classList.remove('show');
        other.classList.add('d-none');
        el.textContent = msg;
        el.classList.remove('d-none');

        setTimeout(() => el.classList.add('show'), 10);
        setTimeout(() => {
            el.classList.remove('show');
            setTimeout(() => el.classList.add('d-none'), 300);
        }, 5000);
    }

    function setButtonLoading(isLoading) {
        resetButton.disabled = isLoading;
        resetButton.innerHTML = isLoading ?
            '<span class="spinner-border spinner-border-sm me-2"></span>Resetting...' :
            'Reset Password';
    }

    function checkPasswordStrength(pw) {
        if (typeof pw !== 'string') return false;

        const checks = {
            length: pw.length >= 8,
            uppercase: /[A-Z]/.test(pw),
            lowercase: /[a-z]/.test(pw),
            number: /\d/.test(pw),
            special: /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(pw)
        };

        let score = 0;
        for (const key in checks) {
            const el = requirements[key];
            const icon = el.querySelector('.requirement-icon');
            const valid = checks[key];

            if (valid) {
                icon.textContent = '✓';
                icon.classList.replace('text-danger', 'text-success');
                el.classList.add('completed');
                score++;
            } else {
                icon.textContent = '✗';
                icon.classList.replace('text-success', 'text-danger');
                el.classList.remove('completed');
            }
        }

        // Update bar visual
        const strengthLevels = ['Weak', 'Fair', 'Good', 'Strong', 'Very Strong'];
        const barColors = ['bg-danger', 'bg-warning', 'bg-info', 'bg-success', 'bg-primary'];
        const widthValues = [20, 40, 60, 80, 100];

        let index;
        if (score === 5) {
            index = 4;
        } else if (score === 4) {
            index = 3;
        } else if (score === 3) {
            index = 2;
        } else if (score === 2) {
            index = 1;
        } else {
            index = 0;
        }

        const level = strengthLevels[index];
        const barColor = barColors[index];
        const width = widthValues[index];

        // Reset bar classes
        // Reset bar classes
        strengthBar.className = 'strength-bar';
        barColors.forEach(c => strengthBar.classList.remove(c));
        strengthBar.classList.add(barColor);

        strengthBar.style.width = width + '%';
        strengthText.textContent = level;


        return score >= 4;
    }

    function validateMatch() {
        const pw = passwordInput.value;
        const conf = confirmInput.value;

        if (!conf) {
            confirmInput.classList.remove('is-valid', 'is-invalid');
            return;
        }

        if (pw === conf) {
            confirmInput.classList.add('is-valid');
            confirmInput.classList.remove('is-invalid');
        } else {
            confirmInput.classList.add('is-invalid');
            confirmInput.classList.remove('is-valid');
        }
    }

    passwordInput.addEventListener('input', () => {
        const pw = passwordInput.value;
        const strong = checkPasswordStrength(pw);

        passwordInput.classList.toggle('is-valid', strong);
        passwordInput.classList.toggle('is-invalid', !strong && pw.length > 0);
        validateMatch();
    });

    confirmInput.addEventListener('input', validateMatch);

    document.querySelectorAll('.btn-outline-secondary').forEach(btn => {
        btn.addEventListener('click', () => togglePassword(btn.previousElementSibling.id));
    });

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const token = tokenInput.value.trim();
        const password = passwordInput.value.trim();
        const passwordConfirmation = confirmInput.value.trim();

        if (!token || !password || !passwordConfirmation) {
            showMessage(errorMessage, 'All fields are required.', false);
            return;
        }

        if (password !== passwordConfirmation) {
            showMessage(errorMessage, 'Passwords do not match.', false);
            return;
        }

        if (!checkPasswordStrength(password)) {
            showMessage(errorMessage, 'Please ensure your password meets all requirements.', false);
            return;
        }

        setButtonLoading(true);

        $.ajax({
            type: 'POST',
            url: '<?= base_url('reset-password') ?>',
            data: {
                token,
                password,
                password_confirmation: passwordConfirmation
            },
            dataType: 'json',
            success: function(response) {
                if (response.success == true) {
                    showMessage(successMessage, response.message || 'Password reset successful.', true);
                    setTimeout(() => (window.location.href = '<?= base_url('login') ?>'), 5000);
                } else {
                    showMessage(errorMessage, response.message || 'Reset failed.', false);
                }
            },
            error: function(xhr) {
                try {
                    const res = JSON.parse(xhr.responseText);
                    showMessage(errorMessage, res.message || 'An error occurred.', false);
                } catch {
                    showMessage(errorMessage, 'An error occurred. Please try again.', false);
                }
            },
            complete: () => setButtonLoading(false)
        });
    });
</script>