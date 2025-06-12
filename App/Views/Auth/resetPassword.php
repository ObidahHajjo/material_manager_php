<div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="card rounded-4 p-5" style="max-width: 500px; width: 100%;">
        <h2 class="text-center text-primary fw-bold mb-2">Forgot Password</h2>
        <p class="text-center text-muted mb-4">Enter your email address and we'll send you a link to reset your password.</p>

        <div id="successMessage" class="alert alert-success d-none" role="alert"></div>
        <div id="errorMessage" class="alert alert-danger d-none" role="alert"></div>

        <form id="resetForm">
            <div class="mb-4">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" name="email" id="email" class="form-control" required placeholder="Enter your email">
            </div>

            <button type="submit" class="btn btn-primary w-100 fw-semibold" id="resetButton">
                SEND RESET LINK
            </button>
        </form>

        <div class="text-center mt-4">
            <a href="<?= base_url('login') ?>" class="text-decoration-none text-primary">&larr; Back to Login</a>
        </div>
    </div>
</div>



<script>
    const form = document.getElementById('resetForm');
    const resetButton = document.getElementById('resetButton');
    const successMessage = document.getElementById('successMessage');
    const errorMessage = document.getElementById('errorMessage');
    const emailInput = document.getElementById('email');

    function showMessage(element, message, isSuccess = true) {
        const otherElement = isSuccess ? errorMessage : successMessage;
        element = isSuccess ? successMessage : errorMessage;

        // Hide the other message
        otherElement.classList.remove('show');
        otherElement.classList.add('d-none');

        // Show current message
        element.textContent = message;
        element.classList.remove('d-none');

        // Trigger fade-in animation
        setTimeout(() => {
            element.classList.add('show');
        }, 10);

        // Auto-hide after 5 seconds
        setTimeout(() => {
            element.classList.remove('show');
            setTimeout(() => {
                element.classList.add('d-none');
            }, 300);
        }, 5000);
    }


    $('#resetForm').on('submit', function(e) {
        e.preventDefault();

        const email = emailInput.value.trim();

        if (!email) {
            showMessage(errorMessage, 'Please enter your email address.', false);
            return;
        }

        if (!isValidEmail(email)) {
            showMessage(errorMessage, 'Please enter a valid email address.', false);
            return;
        }

        setButtonLoading(true);
        $.ajax({
            type: 'POST',
            url: '<?= base_url('forgot-password') ?>',
            data: {
                email
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showMessage(successMessage, `Reset link sent to ${email}. Please check your inbox and spam folder. You will be redirect to login page`, true);
                    setTimeout(() => {
                        window.location.href = '<?= base_url('/login') ?>';
                    }, 5000);
                } else {
                    showMessage(errorMessage, response.message || 'An error occurred. Please try again.', false);
                }
            },
            error: function(xhr, status, error) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    const message = response.message || 'An unknown error occurred.';
                    showMessage(errorMessage, message, false);
                } catch (e) {
                    showMessage(errorMessage, 'An error occurred. Please try again.', false);
                }
            }
        });
        setButtonLoading(false);

    });

    function setButtonLoading(loading) {
        if (loading) {
            resetButton.disabled = true;
            resetButton.innerHTML = '<span class="loading"></span>Sending...';
        } else {
            resetButton.disabled = false;
            resetButton.innerHTML = 'Send Reset Link';
        }
    }

    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    // Add input validation feedback
    emailInput.addEventListener('input', function() {
        const email = this.value.trim();
        if (email && !isValidEmail(email)) {
            this.style.borderColor = '#e74c3c';
        } else {
            this.style.borderColor = '#e1e5e9';
        }
    });

    // Add enter key support
    emailInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            form.dispatchEvent(new Event('submit'));
        }
    });
</script>