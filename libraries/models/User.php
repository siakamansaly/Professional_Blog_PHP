<?php

namespace Blog\Models;

class User extends Model
{

    protected $table = "user";
    /**
     * Read all rows of table
     * @return array[]
     */
    public function readAllAuthors(): array
    {
        $query = "SELECT * FROM {$this->table} WHERE userType LIKE 'admin' ORDER BY id ASC";
        return $this->rows($query);
    }

    /**
     * Search by user email
     * @return int
     */
    public function getEmail(string $email)
    {
        $query = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE email = :email");
        $query->execute(['email' => $email]);
        $item = $query->rowCount();
        return $item;
    }

    /**
     * Read all rows of table User
     * 
     * @return array[]
     */
    public function readAllUsers(?int $first = 0, ?int $last = NULL, ?int $status = NULL, ?string $type = NULL): array
    {

        switch (true) {
            case ($status === NULL && $type !== NULL):
                $query = "SELECT * FROM {$this->table} WHERE userType = '$type' ORDER BY id ASC LIMIT $first , $last ";
                break;
            case ($status !== NULL && $type === NULL):
                $query = "SELECT * FROM {$this->table} WHERE status = '$status' ORDER BY id ASC LIMIT $first , $last ";
                break;
            case ($status === NULL && $type === NULL):
                $query = "SELECT * FROM {$this->table} ORDER BY id ASC LIMIT $first , $last ";
                break;
            default:
                $query = "SELECT * FROM {$this->table} WHERE userType = '$type' AND status = '$status' ORDER BY id ASC LIMIT $first , $last ";
                break;
        }

        return $this->rows($query);
    }
}
