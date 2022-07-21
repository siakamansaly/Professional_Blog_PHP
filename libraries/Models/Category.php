<?php
namespace Blog\Models;


class Category extends Model {

    protected $table = "postcategory";

    /**
     * Read all rows of table Category
     * @return array[]
     */
    public function readAllCategory(?int $first=0, ?int $last=NULL): array
    {
        $query = "SELECT * FROM {$this->table} ORDER BY id ASC LIMIT $first , $last ";  
        return $this->rows($query);
    }

}
