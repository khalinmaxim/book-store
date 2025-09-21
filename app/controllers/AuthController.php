<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/UserModel.php';

class AuthController extends Controller {
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new UserModel($this->db);
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];

            $user = $this->userModel->getUserByUsername($username);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                if ($user['role'] === 'admin') {
                    $this->redirect('/admin');
                } else {
                    $this->redirect('/books');
                }
            } else {
                $this->render('auth/login', ['error' => 'Invalid credentials']);
            }
        } else {
            $this->render('auth/login');
        }
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $email = $_POST['email'];

            if ($this->userModel->createUser($username, $password, $email)) {
                $this->redirect('/login');
            } else {
                $this->render('auth/register', ['error' => 'Registration failed']);
            }
        } else {
            $this->render('auth/register');
        }
    }

    public function logout() {
        session_destroy();
        $this->redirect('/login');
    }
}
?>
