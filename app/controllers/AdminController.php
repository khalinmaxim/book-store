<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/BookModel.php';
require_once __DIR__ . '/../models/UserModel.php';

class AdminController extends Controller {
    private $bookModel;
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->requireAdmin();
        $this->bookModel = new BookModel($this->db);
        $this->userModel = new UserModel($this->db);
    }

    public function index() {
        $books = $this->bookModel->getAllBooks();
        $this->render('admin/index', ['books' => $books]);
    }

    public function editBook($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'title' => $_POST['title'],
                'author_id' => $_POST['author_id'],
                'category_id' => $_POST['category_id'],
                'publication_year' => $_POST['publication_year'],
                'price' => $_POST['price'],
                'is_available' => isset($_POST['is_available']) ? 1 : 0
            ];

            if ($this->bookModel->updateBook($id, $data)) {
                $this->redirect('/admin');
            } else {
                $book = $this->bookModel->getBookById($id);
                $this->render('admin/edit_book', [
                    'book' => $book,
                    'error' => 'Ошибка обновления книги'
                ]);
            }
        } else {
            $book = $this->bookModel->getBookById($id);
            $this->render('admin/edit_book', ['book' => $book]);
        }
    }
}
?>
