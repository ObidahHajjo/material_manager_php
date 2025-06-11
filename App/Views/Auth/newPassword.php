<h2>New password</h2>

<form id="resetForm">
    <input type="hidden" name="token" id="token" value="<?= htmlspecialchars($token) ?>">

    <div class="mb-3">
        <label>Nouveau mot de passe</label>
        <input type="password" name="password" id="password" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Confirmez le mot de passe</label>
        <input type="password" name="password_confirmation" id="conf" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-success" id="resetButton">Reset</button>
</form>


<script>
    $('#resetForm').on('submit', function(e) {
        e.preventDefault();

        const token = $('#token').val().trim();
        const password = $('#password').val().trim();
        const password_confirmation = $('#conf').val().trim();
        const resetButton = $('#resetButton');
        const originalText = resetButton.html();

        $('#errorMessage').hide();
        $('#successMessage').hide();

        if (!email) {
            $('#errorMessage').text('Please enter a valid email address.').show();
            return;
        }

        resetButton.html('<i class="fas fa-spinner fa-spin me-2"></i>Sending...');
        resetButton.prop('disabled', true);

        $.ajax({
            type: 'POST',
            url: '/reset-password',
            data: {
                token: token,
                password: password,
                password_confirmation: confirm
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#successMessage').text(response.message).show();
                } else {
                    $('#errorMessage').text(response.message).show();
                }
                resetButton.html(originalText).prop('disabled', false);
            },
            error: function(xhr) {
                $('#errorMessage').text('Request failed. Please try again later.').show();
                console.error(xhr.responseText);
                resetButton.html(originalText).prop('disabled', false);
            }
        });
    });
</script>



