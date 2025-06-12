<div class="container mt-5">
    <h2 class="mb-4 text-primary">Reserve Equipment</h2>

    <form method="post" action="/reservations/store" class="card p-4 shadow-sm rounded-4">
        <div class="mb-3">
            <label for="material_id" class="form-label">Select Material</label>
            <select name="material_id" id="material_id" class="form-select" required>
                <?php foreach ($materials as $material): ?>
                    <option value="<?= $material->getId() ?>"><?= htmlspecialchars($material->getName()) ?> (<?= $material->getCategory() ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="row mb-3">
            <div class="col">
                <label for="start_time" class="form-label">Start Date & Time</label>
                <input type="datetime-local" name="start_time" id="start_time" class="form-control" required>
            </div>
            <div class="col">
                <label for="end_time" class="form-label">End Date & Time</label>
                <input type="datetime-local" name="end_time" id="end_time" class="form-control" required>
            </div>
        </div>

        <button type="submit" class="btn btn-success"><i class="fas fa-save me-2"></i>Reserve</button>
    </form>
</div>
<!-- Add margin for fixed-top -->
<div style="margin-top: 30rem;"></div>