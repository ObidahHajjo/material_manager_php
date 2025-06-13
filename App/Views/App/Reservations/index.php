<div class="container my-5">
    <h1 class="mb-4">Reservations</h1>

    <a href="/reservations/create" class="btn btn-primary mb-3">New Reservation</a>

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