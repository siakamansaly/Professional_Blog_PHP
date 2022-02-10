<?php
namespace Blog\Models;


class User extends Model {

    protected $table = "user";
    /**
     * Read all rows of table
     * 
     * @return array[]
     */
    public function readAllAuthors(?string $where = "", ?string $order = "id ASC"): array
    {
        $query = "SELECT * FROM {$this->table} WHERE userType LIKE 'admin' ";
        if ($where) {
            $query .= " " . $where;
        }
        $query .= " ORDER BY " . $order;
        return $this->rows($query);
    }

    public function getEmail(string $email)
    {
        $query = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE email = :email");
        $query->execute(['email' => $email]);
        $item = $query->rowCount();
        return $item;
    }

}
