<?php if (empty($filteredReservations)): ?>
    <div class="no-results">
        <div class="no-results-icon">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8" />
                <path d="m21 21-4.35-4.35" />
            </svg>
        </div>
        <h3>No results found</h3>
        <p>Try adjusting your search criteria</p>
    </div>
<?php else: ?>
    <div class="table-container">
        <div class="table-wrapper">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User Name</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Materials</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($filteredReservations as $reservation): ?>
                        <tr>
                            <td>
                                <span class="id-badge">#<?= $reservation->getId() ?></span>
                            </td>
                            <td>
                                <div class="user-info d-flex align-items-center gap-2">
                                    <div class="user-avatar">
                                        <?php if ($reservation->getUser()->getAvatar() && file_exists($_SERVER['DOCUMENT_ROOT'] . "/assets/avatars/" . $reservation->getUser()->getAvatar())): ?>
                                            <img src="assets/avatars/<?= $reservation->getUser()->getAvatar() ?>" alt="User Avatar">
                                        <?php else: ?>
                                            <?= substr($reservation->getUser()->getUserName(), 0, 2) ?>
                                        <?php endif; ?>
                                    </div>
                                    <span><?= $reservation->getUser()->getUserName() ?></span>
                                </div>
                            </td>
                            <td>
                                <div class="datetime">
                                    <div class="date"><?= $reservation->getStartDate()->format('M d, Y') ?></div>
                                    <div class="time"><?= $reservation->getStartDate()->format('H:i') ?></div>
                                </div>
                            </td>
                            <td>
                                <div class="datetime">
                                    <div class="date"><?= $reservation->getEndDate()->format('M d, Y') ?></div>
                                    <div class="time"><?= $reservation->getEndDate()->format('H:i') ?></div>
                                </div>
                            </td>
                            <td>
                                <div class="materials-list">
                                    <?php foreach ($reservation->getMaterials() as $material): ?>
                                        <div class="material-item">
                                            <div class="material-name"><?= htmlspecialchars($material->getName()) ?></div>
                                            <div class="material-details">
                                                <span class="quantity">Qty: <?= $material->getQuantity() ?></span>
                                                <span class="status status-<?= strtolower($material->getStatus()->value) ?>">
                                                    <?= $material->getStatus()->value ?>
                                                </span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <!-- For edit -->
                                    <button class="btn btn-sm btn-outline-warning"
                                        onclick='openReservationModal(<?= json_encode([
                                                                            "id" => $reservation->getId(),
                                                                            "start_date" => $reservation->getStartDate()->format("Y-m-d\TH:i"),
                                                                            "end_date" => $reservation->getEndDate()->format("Y-m-d\TH:i"),
                                                                            "materials" => $reservation->getMaterialIds() // should be an array of IDs
                                                                        ]) ?>)'>
                                        Edit
                                    </button>
                                    <form action="/reservations/delete/<?= $reservation->getId() ?>" method="POST" class="d-inline">
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteMaterial(<?= $reservation->getId() ?>); return false;">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>