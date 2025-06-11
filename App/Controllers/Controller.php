<?php

namespace App\Controllers;

use Config\Session;
use Config\ExceptionsHandler;

class Controller
{
    protected ?Session $session;

    public function __construct()
    {
        $this->boot();
    }
    private function boot(): void
    {
        $this->session = Session::getInstance();
    }


    /**
     * Handle method dispatching with error catching
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public function callAction(string $method, array $args = []): mixed
    {
        try {
            if (!method_exists($this, $method)) {
                throw new \BadMethodCallException("Method $method does not exist in " . static::class);
            }
            return call_user_func_array([$this, $method], $args);
        } catch (\Throwable $e) {
            return ExceptionsHandler::handle($e);
        }
    }



    protected function view(string $view, $data = [])
    {
        try {
            extract($data);
            require_once "../App/Views/Layouts/Header.php";
            if (isset($_SESSION['isPro'])) {
                require_once "../App/Views/Layouts/Navbar.php";
            }
            require_once "../App/Views/" . $view . ".php";
            require_once "../App/Views/Layouts/Footer.php";
        } catch (\Throwable $e) {
            return ExceptionsHandler::handle($e);
        }
    }

    /**
     * Redirect to a route (add a message if needed)
     */
    protected function redirectTo(string $url, ?string $variable = null, ?string $message = null)
    {
        try {
            if ($variable !== null && $message !== null) {
                $this->session->set($variable, $message);
            }
            header("Location: " . filter_var(base_url($url), FILTER_SANITIZE_URL));
            exit();
        } catch (\Throwable $e) {
            return ExceptionsHandler::handle($e);
        }
    }
}
