<div class="container my-5">
    <h1 class="mb-4 text-">Reservations</h1>

    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#reservationModal" onclick="openReservationModal()">
        <i class="fas fa-plus me-2"></i>New Reservation
    </button>

    <!-- Search Form -->
    <form id="searchForm" class="mb-4" style="max-width: 450px;">
        <input type="text" name="search" id="searchInput" class="form-control"
            placeholder="Search by ID, User ID, Material Name or Date">
    </form>

    <!-- Placeholder for dynamic table content -->
    <div id="reservationTable">
        <?php
        // Initial load: no filtering, display all
        $filteredReservations = $reservations;
        include __DIR__ . '/ReservationTable.php';
        ?>
    </div>
</div>

<!-- Reservation Modal -->
<div class="modal fade" id="reservationModal" tabindex="-1" aria-labelledby="reservationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="reservationForm" method="POST">
            <input type="hidden" id="reservation-id" name="id">
            <div class="modal-content rounded-4">
                <div class="modal-header">
                    <h5 class="modal-title" id="reservationModalLabel">Add Reservation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="alert d-none" role="alert" id="errorMessage"></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="start-date" class="form-label">Start Date</label>
                        <input type="datetime-local" class="form-control" id="start-date" name="start_date">
                    </div>

                    <div class="mb-3">
                        <label for="end-date" class="form-label">End Date</label>
                        <input type="datetime-local" class="form-control" id="end-date" name="end_date">
                    </div>

                    <div class="mb-3">
                        <label for="materials" class="form-label">Select Materials</label>
                        <select multiple class="form-select" id="materials" name="materials[]">
                            <?php foreach ($materials as $material): ?>
                                <option value="<?= $material->getId() ?>"><?= $material->getName() ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">Hold Ctrl (Cmd on Mac) to select multiple materials.</small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" id="reservation-submit-btn" class="btn btn-success">
                        <i class="fas fa-save me-2"></i>Save Reservation
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancelReservation">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Add margin for fixed-top -->
<div style="margin-top: 26rem;"></div>


<!-- AJAX Script -->
<script>
    document.getElementById('searchInput').addEventListener('input', function() {
        const query = this.value;
        const searchTerm = encodeURIComponent(query) ? encodeURIComponent(query) : 'null';
        fetch(`/reservations/search/${searchTerm}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                document.getElementById('reservationTable').innerHTML = html;
            })
            .catch(err => console.error('Search error:', err));
    });
</script>

<script>
    const $form = $('#reservationForm');
    const $title = $('#reservationModalLabel');
    const $submitBtn = $('#reservation-submit-btn');
    const $cancel = $('#cancelReservation');

    function openReservationModal(data = null) {
        $form.trigger('reset');
        $('#reservation-id').val('');
        $('#materials option').prop('selected', false);
        $form.attr('action', '/reservations/create');
        $title.text('New Reservation');
        $submitBtn.html('<i class="fas fa-save me-2"></i>Save Reservation');

        if (data) {
            $title.text('Edit Reservation');
            $form.attr('action', '/reservations/update/' + data.id);
            $submitBtn.html('<i class="fas fa-edit me-2"></i>Update Reservation');

            $('#reservation-id').val(data.id);
            $('#start-date').val(data.start_date.replace(' ', 'T'));
            $('#end-date').val(data.end_date.replace(' ', 'T'));

            if (Array.isArray(data.materials)) {
                data.materials.forEach(id => {
                    $(`#materials option[value="${id}"]`).prop('selected', true);
                });
            }
        }

        $('#reservationModal').modal('show');
    }

    $form.on('submit', function(e) {
        e.preventDefault();
        const $action = $form.attr('action');
        const $isUpdate = $action.includes('/update/');

        const $start_date = $('#start-date').val();
        const $end_date = $('#end-date').val();
        const $materials = $('#materials').val();
        if (!verifiyFields()) return;
        if ($isUpdate) {
            const $id = $('#reservation-id').val();
            const data = {
                id: $id,
                start_date: $start_date,
                end_date: $end_date,
                materials: $materials,
            };

            $.ajax({
                contentType: 'application/json',
                url: `<?= base_url('reservations/update/') ?>${$id}`,
                type: 'post',
                dataType: 'json',
                data: JSON.stringify(data),
                success: function(response) {
                    showMessage("reservation updated successfully.", false);
                    $submitBtn.attr('disabled', true);
                    $cancel.attr('disabled', true);
                    setTimeout(function() {
                        $('#reservationModal').modal('hide');
                        location.reload();
                    }, 3000);
                },
                error: function(xhr, error) {
                    let errorText = 'An error occurred.';

                    try {
                        const json = JSON.parse(xhr.responseText);
                        if (json && json.message) {
                            errorText = json.message;
                        }
                    } catch (e) {
                        errorText = xhr.responseText || error.message || 'An error occurred.';
                    }

                    showMessage(errorText, true);
                }

            });
            return;
        }


        const data = {
            start_date: $start_date,
            end_date: $end_date,
            materials: $materials,
        };

        let $isValid = verifiyFields(data);
        $.ajax({
            contentType: 'application/json',
            url: '<?= base_url('reservations/create') ?>',
            type: 'post',
            dataType: 'json',
            data: JSON.stringify(data),
            success: function(response) {
                showMessage("reservation saved successfully.", false);
                $submitBtn.attr('disabled', true);
                $cancel.attr('disabled', true);
                setTimeout(function() {
                    $('#materialModal').modal('hide');
                    location.reload();
                }, 3000);
            },
            error: function(xhr, error) {
                let errorText = 'An error occurred.';

                try {
                    const json = JSON.parse(xhr.responseText);
                    if (json && json.message) {
                        errorText = json.message;
                    }
                } catch (e) {
                    errorText = xhr.responseText || error.message || 'An error occurred.';
                }

                showMessage(errorText, true);
            }

        });
    });

    function verifiyFields(fields) {
        const startDate = $('#start-date').val();
        const endDate = $('#end-date').val();
        const selectedMaterials = $('#materials').val();

        let isValid = true;
        let message = '';

        if (!startDate || startDate == '') {
            isValid = false;
            message = 'Start date is required.';
        } else if (!endDate) {
            isValid = false;
            message = 'End date is required.';
        } else if (new Date(startDate) >= new Date(endDate)) {
            isValid = false;
            message = 'Start date must be before end date.';
        } else if (!selectedMaterials || selectedMaterials.length === 0) {
            isValid = false;
            message = 'Please select at least one material.';
        }

        if (!isValid) {
            showMessage(message, true);
        }

        return isValid;
    }

    function showMessage($message, $isError) {
        const $messageLabel = $('#errorMessage');

        if (typeof $message !== 'string') return;
        if (typeof $isError !== 'boolean') return;

        $messageLabel
            .removeClass('d-none alert-success alert-danger')
            .addClass($isError ? 'alert-danger' : 'alert-success')
            .text($message)
            .addClass('show');

        setTimeout(() => {
            $messageLabel.removeClass('show');
            setTimeout(() => {
                $messageLabel.addClass('d-none');
            }, 300);
        }, 5000);
    }
</script>

<script>
    function deleteMaterial(id) {
        if (!confirm("Delete this material?")) return;
        $.ajax({
            type: 'POST',
            url: '/reservations/delete/' + id,
            success: function(response) {
                alert("Material deleted successfully!");
                setTimeout(() => location.reload(), 1000);
            },
            error: function(xhr) {
                alert(xhr.responseText || "Failed to delete material.");
            }
        });
    }
</script>