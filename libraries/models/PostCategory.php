<?php
namespace Blog\Models;


class PostCategory extends Model {

    protected $table = "post_postcategory";

    /**
     * Read post category by post
     * @return array
     */
    public function readAllCategoriesByPost(int $postid) : array
    {
        $query = "SELECT Post_id, postcategory.id, postcategory.name FROM {$this->table} JOIN postcategory on {$this->table}.PostCategory_id = postcategory.id WHERE Post_id = $postid ";
        return $this->rows($query);
    }

}
