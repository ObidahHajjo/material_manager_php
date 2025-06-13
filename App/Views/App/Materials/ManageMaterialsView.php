<div class="container mt-5">
    <h2 class="mb-4 text-warning">Manage Materials</h2>

    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#materialModal" onclick="openMaterialModal()">
        <i class="fas fa-plus me-2"></i>Add Material
    </button>


    <div class="card p-3 shadow-sm rounded-4">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php

                use App\Enums\MaterialStatus;

                foreach ($materials as $m): ?>
                    <tr>
                        <td><?= htmlspecialchars($m->getName()) ?></td>
                        <td><?= htmlspecialchars($m->getCategory()) ?></td>
                        <td><?= $m->getQuantity() ?></td>
                        <td><?= ucfirst($m->getStatus()->value) ?></td>
                        <td>
                            <a href="#" class="btn btn-sm btn-outline-secondary"
                                data-bs-toggle="modal"
                                data-bs-target="#materialModal"
                                onclick='openMaterialModal(<?= json_encode([
                                                                "id" => $m->getId(),
                                                                "name" => $m->getName(),
                                                                "category" => $m->getCategory(),
                                                                "quantity" => $m->getQuantity(),
                                                                "status" => $m->getStatus()->value
                                                            ]) ?>)'>
                                Edit
                            </a>

                            <form action="/admin/materials/delete/<?= $m->getId() ?>" method="POST" class="d-inline">
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteMaterial(<?= $m->getId() ?>); return false;">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Material Modal -->
<div class="modal fade" id="materialModal" tabindex="-1" aria-labelledby="materialModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="materialForm" method="POST">
            <input type="hidden" name="id" id="material-id">
            <div class="modal-content rounded-4">
                <div class="modal-header">
                    <h5 class="modal-title" id="materialModalLabel">Add Material</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="alert d-none" role="alert" id="errorMessage"></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="material-name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="material-name" name="name">
                    </div>
                    <div class="mb-3">
                        <label for="material-category" class="form-label">Category</label>
                        <input type="text" class="form-control" id="material-category" name="category">
                    </div>
                    <div class="mb-3">
                        <label for="material-quantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="material-quantity" name="quantity">
                    </div>
                    <div class="mb-3">
                        <label for="material-status" class="form-label">Status</label>
                        <select class="form-select" id="material-status" name="status">
                            <?php foreach (MaterialStatus::cases() as $case) : ?>
                                <option value="<?= $case->value ?>"><?= $case->name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success" id="submitModal">Save Material</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancelModal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>


<script>
    const $form = $('#materialForm');
    const $modalLabel = $('#materialModalLabel');
    const $submit = $('#submitModal');
    const $cancel = $('#cancelModal');

    function openMaterialModal(data = null) {

        // Reset form
        $form.trigger('reset');
        $form.attr('action', '/admin/materials/store');
        $submit.text('Create');
        $modalLabel.text('Add Material');
        $('#material-id').val('');

        if (data) {
            $modalLabel.text('Edit Material');
            $form.attr('action', '/admin/materials/update/' + data.id);
            $submit.text('Update');
            $submit.removeClass('btn-success');
            $submit.addClass('btn-warning');
            $('#material-id').val(data.id);
            $('#material-name').val(data.name);
            $('#material-category').val(data.category);
            $('#material-quantity').val(data.quantity);
            $('#material-status').val(data.status);
        }
    }

    $form.on('submit', function(e) {
        e.preventDefault();
        const $action = $form.attr('action');
        const $isUpdate = $action.includes('/update/');
        const $name = $('#material-name').val();
        const $category = $('#material-category').val();
        const $quantity = $('#material-quantity').val();
        const $status = $('#material-status').val();
        if ($isUpdate) {
            const $id = $('#material-id').val();
            const data = {
                id: $id,
                name: $name,
                category: $category,
                quantity: $quantity,
                status: $status
            };
            let $isValid = verifiyFields(data);
            if (!$isValid) return;
            $.ajax({
                contentType: 'application/json',
                url: `<?= base_url('materials/update/') ?>${$id}`,
                type: 'post',
                dataType: 'json',
                data: JSON.stringify(data),
                success: function(response) {
                    showMessage("Material saved successfully.", false);
                    $submit.attr('disabled', true);
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
            return;
        }

        const data = {
            name: $name,
            category: $category,
            quantity: $quantity,
            status: $status
        };

        let $isValid = verifiyFields(data);
        $.ajax({
            contentType: 'application/json',
            url: '<?= base_url('materials/create') ?>',
            type: 'post',
            dataType: 'json',
            data: JSON.stringify(data),
            success: function(response) {
                showMessage("Material saved successfully.", false);
                $submit.attr('disabled', true);
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
        let isValid = true;
        for (const [key, value] of Object.entries(fields)) {
            const trimmed = typeof value === 'string' ? value.trim() : value;

            if (trimmed === '' || trimmed === null || trimmed === undefined) {
                isValid = false;
                // Optional: highlight the invalid field
                $(`#material-${key}`).addClass('is-invalid');
                showMessage(`Field ${key} is invalid or empty.`, true);
            } else {
                $(`#material-${key}`).removeClass('is-invalid');
            }
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
            url: '/materials/delete/' + id,
            success: function(response) {
                alert("Material deleted successfully!");
                setTimeout(() => location.reload(), 1000); // Refresh after 2s
            },
            error: function(xhr) {
                alert(xhr.responseText || "Failed to delete material.");
            }
        });
    }
</script>