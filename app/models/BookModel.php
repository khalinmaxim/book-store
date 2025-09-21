<?php
class BookModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllBooks($filters = []) {
        $query = "SELECT b.*, a.name as author_name, c.name as category_name
                 FROM books b
                 LEFT JOIN authors a ON b.author_id = a.id
                 LEFT JOIN categories c ON b.category_id = c.id
                 WHERE 1=1";

        $params = [];

        if (!empty($filters['category'])) {
            $query .= " AND c.name = :category";
            $params[':category'] = $filters['category'];
        }

        if (!empty($filters['author'])) {
            $query .= " AND a.name = :author";
            $params[':author'] = $filters['author'];
        }

        if (!empty($filters['year'])) {
            $query .= " AND b.publication_year = :year";
            $params[':year'] = $filters['year'];
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBookById($id) {
        $query = "SELECT b.*, a.name as author_name, c.name as category_name
                 FROM books b
                 LEFT JOIN authors a ON b.author_id = a.id
                 LEFT JOIN categories c ON b.category_id = c.id
                 WHERE b.id = :id";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateBook($id, $data) {
        $query = "UPDATE books SET
                 title = :title,
                 author_id = :author_id,
                 category_id = :category_id,
                 publication_year = :year,
                 price = :price,
                 is_available = :available
                 WHERE id = :id";

        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            ':title' => $data['title'],
            ':author_id' => $data['author_id'],
            ':category_id' => $data['category_id'],
            ':year' => $data['publication_year'],
            ':price' => $data['price'],
            ':available' => $data['is_available'],
            ':id' => $id
        ]);
    }
}
?>
