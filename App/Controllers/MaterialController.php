<?php

namespace App\Controllers;

use App\Eloquents\MaterialEloquent;
use App\Exceptions\UnauthorizedException;
use App\Requests\Materials\CreateMaterialRequest;
use Exception;

class MaterialController extends Controller
{

    private MaterialEloquent $materialEloquent;

    public function __construct()
    {
        parent::__construct();
        $this->materialEloquent = new MaterialEloquent();
    }

    public function show()
    {
        if (!$this->session->isset('user') || $this->session->get('user')->getRole() !== 'admin') throw new UnauthorizedException();
        $materials = $this->materialEloquent->all();
        $this->view('App/Materials/ManageMaterialsView', [
            'title' => 'Materials Manager',
            'materials' => $materials,
            'active' => 'materials'
        ]);
    }

    public function create()
    {
        if (!$this->session->isset('user') || $this->session->get('user')->getRole() !== 'admin') throw new UnauthorizedException();
        try {
            $request = new CreateMaterialRequest();
            $data = $request->validated();
            if (!$data) throw new Exception("Fields are required !", 400);
            if (!$this->materialEloquent->create($data))  throw new Exception("Error while creating a new material", 500);

            http_response_code(200);
            echo json_encode(['status' => 'success', 'message' => 'Material created']);
        } catch (Exception $e) {
            $this->jsonError($e);
        }
        exit();
    }

    public function update(int $id)
    {
        if (!$this->session->isset('user') || $this->session->get('user')->getRole() !== 'admin') throw new UnauthorizedException();
        try {
            $request = new CreateMaterialRequest();
            $data = $request->validated();
            if (!$data) throw new Exception("Fields are required !", 400);
            if (!$this->materialEloquent->update([...$data, "id" => $id]))  throw new Exception("Error while updating a material", 500);
            http_response_code(200);
            echo json_encode(['status' => 'success', 'message' => 'Material updated']);
        } catch (Exception $e) {
            $this->jsonError($e);
        }
        exit();
    }

    public function delete(int $id)
    {
        if (!$this->session->isset('user') || $this->session->get('user')->getRole() !== 'admin') throw new UnauthorizedException();
        try {
            if (!$this->materialEloquent->delete($id)) throw new Exception("Error while deleting a material", 500);
            http_response_code(200);
            echo json_encode(['status' => 'success', 'message' => 'Material deleted']);
        } catch (Exception $e) {
            $this->jsonError($e);
        }
        exit();
    }

    private function jsonError(Exception $e): void
    {
        http_response_code($e->getCode() ?: 500);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        exit;
    }
}
