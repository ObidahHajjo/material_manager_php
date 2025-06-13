<div class="container my-5">
    <h1 class="mb-4">Reservations</h1>

    <a href="/reservations/create" class="btn btn-primary mb-3">New Reservation</a>

    <?php if (empty($reservations)): ?>
        <div class="alert alert-info">No reservations found.</div>
    <?php else: ?>
        <!-- Search Form -->
        <form method="get" class="mb-4" id="searchForm" style="max-width: 450px;">
            <input type="text" name="search"
                value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                class="form-control"
                placeholder="Search by ID, User ID, Material Name or Date (YYYY-MM-DD)">
        </form>

        <?php
        $searchTerm = trim(strtolower($_GET['search'] ?? ''));
        $filtered = array_filter($reservations, function ($reservation) use ($searchTerm) {
            if ($searchTerm === '') return true;

            $id = (string) $reservation->getId();
            $userId = (string) $reservation->getUserId();
            $start = $reservation->getStartDate()->format('Y-m-d H:i');
            $end = $reservation->getEndDate()->format('Y-m-d H:i');

            if (
                str_contains($id, $searchTerm) ||
                str_contains($userId, $searchTerm) ||
                str_contains(strtolower($start), $searchTerm) ||
                str_contains(strtolower($end), $searchTerm)
            ) {
                return true;
            }

            foreach ($reservation->getMaterials() as $material) {
                if (str_contains(strtolower($material->getName()), $searchTerm)) {
                    return true;
                }
            }

            return false;
        });
        ?>

        <?php if (empty($filtered)): ?>
            <div class="alert alert-warning">No results match your search.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover bg-white">
                    <thead class="table-dark">
                        <tr>
                            <th>#ID</th>
                            <th>User ID</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Materials</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($filtered as $reservation): ?>
                            <?php $reservationId = $reservation->getId(); ?>
                            <tr>
                                <td><?= htmlspecialchars($reservationId) ?></td>
                                <td><?= htmlspecialchars($reservation->getUserId()) ?></td>
                                <td><?= $reservation->getStartDate()->format('Y-m-d H:i') ?></td>
                                <td><?= $reservation->getEndDate()->format('Y-m-d H:i') ?></td>
                                <td>
                                    <?php if (!empty($reservation->getMaterials())): ?>
                                        <ul class="mb-0">
                                            <?php foreach ($reservation->getMaterials() as $material): ?>
                                                <li>
                                                    Material: <?= htmlspecialchars($material->getName()) ?>,
                                                    Quantity: <?= $material->getQuantity() ?>,
                                                    Status: <?= $material->getStatus()->value ?>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php else: ?>
                                        <em>No materials</em>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="/reservations/view?id=<?= $reservationId ?>" class="btn btn-sm btn-info">View</a>
                                    <a href="/reservations/edit?id=<?= $reservationId ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <a href="/reservations/delete?id=<?= $reservationId ?>" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Are you sure?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>