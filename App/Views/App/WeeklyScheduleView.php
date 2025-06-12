<div class="container mt-5">
    <h2 class="mb-4 text-primary">Weekly Availability</h2>

    <div class="table-responsive card p-3 shadow-sm rounded-4">
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Material</th>
                    <?php foreach ($weekDays as $day): ?>
                        <th><?= $day ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($materials as $material): ?>
                    <tr>
                        <td><?= htmlspecialchars($material['name']) ?></td>
                        <?php foreach ($material['availability'] as $day): ?>
                            <td class="<?= $day['status'] === 'free' ? 'bg-success text-white' : 'bg-danger text-white' ?>">
                                <?= ucfirst($day['status']) ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>