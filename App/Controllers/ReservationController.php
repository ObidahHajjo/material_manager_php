<?php

namespace App\Controllers;

use App\Eloquents\MaterialEloquent;
use App\Eloquents\ReservationEloquent;
use App\Exceptions\UnauthorizedException;
use App\Requests\Reservations\CreateReservationRequest;
use Exception;

class ReservationController extends Controller
{
    private ReservationEloquent $reservationEloquent;
    private MaterialEloquent $materialEloquent;

    public function __construct()
    {
        parent::__construct();
        $this->reservationEloquent = new ReservationEloquent();
        $this->materialEloquent = new MaterialEloquent();
    }

    public function index()
    {
        if (!$this->session->isset('user')) throw new UnauthorizedException();
        $user =  $this->session->get('user');
        try {
            $this->view('App/Reservations/index', [
                'title' => 'My Reservations',
                'active' => 'reseravtions',
                'reservations' => $this->getReservations(),
                'materials' => $this->materialEloquent->all()
            ], [
                'reservationTable'
            ]);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function search(string $searchTerm)
    {
        if (!$this->session->isset('user')) throw new UnauthorizedException();
        $reservations = $this->getReservations();

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

            require __DIR__ . '/../Views/App/Reservations/ReservationTable.php';
            exit;
        }

        header('Location: /reservations');
        exit;
    }

    public function create()
    {
        $user = $this->session->get('user');
        try {
            $request = new CreateReservationRequest();
            $data = $request->validated();
            if (!$data) throw new Exception("Fields are required !", 400);
            if ($this->reservationEloquent->hasConflict($data['start_date'], $data['end_date'], $data['materials']))
                throw new Exception("Materials are already reserved at this time!");
            if (!$this->reservationEloquent->create([...$data, "user_id" => $user->getId()]))  throw new Exception("Error while creating a new reservation", 500);
            http_response_code(200);
            echo json_encode(['status' => 'success', 'message' => 'Reservation created']);
        } catch (Exception $e) {
            $this->jsonError($e);
        }
        exit();
    }

    public function update(int $id)
    {
        if (!$this->session->isset('user')) throw new UnauthorizedException();
        try {
            $request = new CreateReservationRequest();
            $data = $request->validated();
            if (!$data) throw new Exception("Fields are required !", 400);
            if ($this->reservationEloquent->hasConflict($data['start_date'], $data['end_date'], $data['materials']))
                throw new Exception("Materials are already reserved at this time!");
            if (!$this->reservationEloquent->update([...$data, "id" => $id]))  throw new Exception("Error while updating a reservation", 500);
            http_response_code(200);
            echo json_encode(['status' => 'success', 'message' => 'Reservation updated']);
        } catch (Exception $e) {
            $this->jsonError($e);
        }
        exit();
    }

    public function delete(int $id)
    {
        if (!$this->session->isset('user') || $this->session->get('user')->getRole() !== 'admin') throw new UnauthorizedException();
        try {
            if (!$this->reservationEloquent->delete($id)) throw new Exception("Error while deleting a material", 500);
            http_response_code(200);
            echo json_encode(['status' => 'success', 'message' => 'Material deleted']);
        } catch (Exception $e) {
            $this->jsonError($e);
        }
        exit();
    }

    private function getReservations(): array
    {
        if (!$this->session->isset('user')) throw new UnauthorizedException();
        $user =  $this->session->get('user');
        return $user->getRole() === 'admin' ? $this->reservationEloquent->all() : $this->reservationEloquent->allByUserId($user->getId());
    }

    private function jsonError(Exception $e): void
    {
        http_response_code($e->getCode() ?: 500);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        exit;
    }
}
