<?php

namespace App\Controllers;

use App\Eloquents\ReservationEloquent;

class ReservationController extends Controller
{
    private ReservationEloquent $reservationEloquent;

    public function __construct()
    {
        $this->reservationEloquent = new ReservationEloquent();
        parent::__construct();
    }

    public function create()
    {
        $this->view('App/Reservations/CreateReservationView', [
            'title' => 'Create Reservation',
            'active' => 'newReservation'
        ]);
    }

    public function show(int $id)
    {
        $this->reservationEloquent->findById($id);

        $this->view('App/Reservations/ReservationView', [
            'title' => 'My Reservations',
            'active' => 'reseravtions',
            'reservation' => $this->reservationEloquent->findById($id)
        ]);
    }

    public function index()
    {
        $this->view('App/Reservations/index', [
            'title' => 'My Reservations',
            'active' => 'reseravtions',
            'reservations' => $this->reservationEloquent->all()
        ], [
            'reservationTable'
        ]);
    }

    public function search(string $searchTerm)
    {
        // Load all reservations in memory
        $reservations = $this->reservationEloquent->all(); // or wherever you load full list

        // Check for AJAX
        if ($_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {

            $filteredReservations = $filteredReservations = array_filter($reservations, function ($reservation) use ($searchTerm) {
                if ($searchTerm === 'null'  || $searchTerm === '') return true;

                if (
                    stripos((string) $reservation->getId(), $searchTerm) !== false ||
                    stripos((string) $reservation->getUser()->getId(), $searchTerm) !== false ||
                    stripos((string) $reservation->getUser()->getUserName(), $searchTerm) !== false ||
                    stripos($reservation->getStartDate()->format('Y-m-d H:i'), $searchTerm) !== false ||
                    stripos($reservation->getEndDate()->format('Y-m-d H:i'), $searchTerm) !== false
                ) {
                    return true;
                }

                foreach ($reservation->getMaterials() as $material) {
                    if (
                        stripos($material->getName(), $searchTerm) !== false ||
                        stripos((string) $material->getQuantity(), $searchTerm) !== false ||
                        stripos($material->getStatus()->value, $searchTerm) !== false
                    ) {
                        return true;
                    }
                }

                return false;
            });;

            // Render only the table partial
            require __DIR__ . '/../Views/App/Reservations/ReservationTable.php';
            exit;
        }

        // Fallback if accessed directly (optional)
        header('Location: /reservations');
        exit;
    }
}
