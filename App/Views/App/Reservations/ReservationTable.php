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
                                        <?php if ($reservation->getUser()->getAvatar() && file_exists("/public" . $reservation->getUser()->getAvatar())): ?>
                                            <img src="<?= $reservation->getUser()->getAvatar() ?>" alt="User Avatar">
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
                                    <a href="/reservations/view?id=<?= $reservation->getId() ?>" class="btn btn-view" title="View Details">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                            <circle cx="12" cy="12" r="3" />
                                        </svg>
                                        View
                                    </a>
                                    <a href="/reservations/edit?id=<?= $reservation->getId() ?>" class="btn btn-edit" title="Edit Reservation">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                            <path d="m18.5 2.5-8 8v4h4l8-8a2 2 0 0 0 0-3L20 1" />
                                        </svg>
                                        Edit
                                    </a>
                                    <a href="/reservations/delete?id=<?= $reservation->getId() ?>" class="btn btn-delete" title="Delete Reservation"
                                        onclick="return confirm('Are you sure you want to delete this reservation?')">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="3,6 5,6 21,6" />
                                            <path d="m19,6v14a2,2 0 0,1-2,2H7a2,2 0 0,1-2-2V6m3,0V4a2,2 0 0,1,2-2h4a2,2 0 0,1,2,2v2" />
                                        </svg>
                                        Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>