<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/BookModel.php';

class BookController extends Controller {
    private $bookModel;

    public function __construct() {
        parent::__construct();
        $this->bookModel = new BookModel($this->db);
    }

    public function index() {
        $filters = [
            'category' => $_GET['category'] ?? '',
            'author' => $_GET['author'] ?? '',
            'year' => $_GET['year'] ?? ''
        ];

        $books = $this->bookModel->getAllBooks($filters);
        $this->render('books/index', ['books' => $books, 'filters' => $filters]);
    }

    public function show($id) {
        $book = $this->bookModel->getBookById($id);
        $this->render('books/show', ['book' => $book]);
    }
}
?>
