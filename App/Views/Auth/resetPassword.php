<div class="container">
    <h2>Forgot Password</h2>
    <p class="subtitle">Enter your email address and we'll send you a link to reset your password.</p>

    <div id="successMessage" class="alert alert-success" style="display:none;"></div>
    <div id="errorMessage" class="alert alert-danger" style="display:none;"></div>

    <form id="resetForm">
        <div class="form-group">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" name="email" id="email" class="form-control" required placeholder="Enter your email address">
        </div>

        <button type="submit" class="btn btn-primary" id="resetButton">
            Send Reset Link
        </button>
    </form>

    <div class="back-link">
        <a href="/login"> &larr; Back to Login</a>
    </div>
</div>

<script>
    const form = document.getElementById('resetForm');
    const resetButton = document.getElementById('resetButton');
    const successMessage = document.getElementById('successMessage');
    const errorMessage = document.getElementById('errorMessage');
    const emailInput = document.getElementById('email');

    function showMessage(element, message, isSuccess = true) {
        // Hide other message
        const otherMessage = isSuccess ? errorMessage : successMessage;
        otherMessage.style.display = 'none';
        otherMessage.classList.remove('show');

        // Show current message
        element.textContent = message;
        element.style.display = 'block';

        // Trigger animation
        setTimeout(() => {
            element.classList.add('show');
        }, 10);

        // Auto hide after 5 seconds
        setTimeout(() => {
            element.classList.remove('show');
            setTimeout(() => {
                element.style.display = 'none';
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
            url: '/forgot-password',
            data: {
                email
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showMessage(successMessage, `Reset link sent to ${email}. Please check your inbox and spam folder. You will be redirect to login page`, true);
                    setTimeout(() => {
                        window.location.href = '/login';
                    }, 5000);
                } else {
                    showMessage(errorMessage, response || 'An error occurred. Please try again.', false);
                }
            },
            error: function(xhr, error) {
                showMessage(errorMessage, error || 'An error occurred. Please try again.', false);
                console.error(xhr.responseText);
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