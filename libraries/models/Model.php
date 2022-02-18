<?php

namespace Blog\Models;

use Blog\Database;

abstract class Model
{
    protected $pdo;
    protected $table;

    public function __construct()
    {
        $this->pdo = new Database;
        $this->pdo = $this->pdo->getPdo();
    }


    /**
     * Return one row data
     * 
     * @return mixed
     */
    public function row(string $query, ?array $params = [])
    {
        $data = $this->pdo->prepare($query);
        $data->execute($params);
        return $data->fetch();
    }

    public function count(?string $key = "", ?string $value = ""): int
    {
        $query = "SELECT * FROM {$this->table}";
        if ($key) {
            $query = "SELECT * FROM {$this->table} WHERE $key = '" . $value . "'";
        }

        $data = $this->pdo->query($query);
        $data = $data->rowCount();
        return $data;
    }

    public function lastInsertIdPDO()
    {
        $query = "SELECT MAX(id) as lastid FROM {$this->table}";
        $data = $this->pdo->query($query);
        $data = $this->row($query);
        return $data;
    }



    /**
     * Return all rows datas
     * 
     * @return array
     */
    public function rows(string $query, array $params = []): array
    {
        $datas = $this->pdo->query($query);
        $datas->execute($params);
        return $datas->fetchAll();
    }


    /**
     * Prepare and execute query
     * 
     * @return void
     */
    public function flush(string $query, array $params = [])
    {
        $datas = $this->pdo->prepare($query);

        switch (isset($params)) {
            case true:
                $datas->execute($params);
                break;
            default:
                $datas->execute();
                break;
        }
    }

    /**
     * Insert in database
     * @return void
     */
    public function insert(array $data)
    {
        $keys   = implode(', ', array_keys($data));
        $values = implode('", "', $data);
        $query  = "INSERT INTO {$this->table} (" . $keys . ") VALUES (\"" . $values . "\")";
        $this->flush($query, $data);
        return $this;
    }

    /**
     * Read one row of table
     * 
     * @return mixed
     */
    public function read(string $value, string $key = null, $select = "")
    {
        if (isset($key)) {
            switch ($select <> "") {
                case true:
                    $query = "SELECT " . $select . " FROM {$this->table} WHERE " . $key . " = ?";
                    return $this->row($query, [$value]);
                    break;
                default:
                    $query = "SELECT * FROM {$this->table} WHERE " . $key . " = ?";
                    return $this->row($query, [$value]);
                    break;
            }
        }

        $query = "SELECT * FROM {$this->table} WHERE id = ?";
        return $this->row($query, [$value]);
    }


    /**
     * Read all rows of table
     * 
     * @return array[]
     */
    public function readAll(?string $where = "", ?string $order = ""): array
    {
        $query = "SELECT * FROM {$this->table}";
        if ($where) {
            $query .= " WHERE " . $where;
        }
        if ($order) {
            $query .= " ORDER BY " . $order;
        }
        return $this->rows($query);
    }

    /**
     * Updates Data with id or  key
     */
    public function update($value, array $data, string $key = null)
    {
        $set = null;


        foreach ($data as $dataKey => $dataValue) {
            $set .= $dataKey . ' = "' . $dataValue . '", ';
        }

        $set = substr_replace($set, '', -2);

        switch (isset($key)) {
            case true:
                $query = "UPDATE {$this->table} SET " . $set . " WHERE " . $key . " = ?";
                break;

            default:
                $query = "UPDATE {$this->table} SET " . $set . " WHERE id = ?";
                break;
        }

        return $this->flush($query, [$value]);
    }


    /**
     * Delete a row
     * 
     * @return void
     */
    public function delete(int $idItem, ?string $key = ""): void
    {
        switch (isset($key)) {
            case true:
                $query = "DELETE FROM {$this->table} WHERE $key = :val";
                break;

            default:
                $query = "DELETE FROM {$this->table} WHERE id = :val";
                break;
        }
        $this->flush($query, ['val' => $idItem]);
    }
}
