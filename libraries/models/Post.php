<?php

namespace Blog\Models;


class Post extends Model
{

    protected $table = "post";

    /**
     * Read all rows of table
     * 
     * @return array[]
     */
    public function readAllPosts(string $status = "1", ?string $order = "post.dateAddPost DESC", ?string $limit = ""): array
    {
        $query = "SELECT post.id, post.dateAddPost, post.dateModifyPost, post.title, post.chapo, post.slug, post.content, post.picture, post.status, post.User_id, user.firstName, user.lastName FROM {$this->table} JOIN user on {$this->table}.User_id = user.id WHERE post.status IN  ($status)";
        if($order)
        {
            $query .= " ORDER BY $order";
        }
        if($limit)
        {
            $query .= " LIMIT $limit";
        }
        return $this->rows($query);
    }

    /**
     * Read all rows of table
     * 
     * @return array[]
     */
    public function readAllPostsByCategory(string $status = "1", int $category = 0, ?string $order = "post.dateAddPost DESC", ?string $limit = ""): array
    {
        $query = "SELECT post.id, post.dateAddPost, post.dateModifyPost, post.title, post.chapo, post.slug, post.content, post.picture, post.status, post.User_id, user.firstName, user.lastName FROM {$this->table}, user, post_postcategory WHERE {$this->table}.User_id = user.id  AND post.id = post_postcategory.Post_id  AND post.status IN  ($status) ";
        if ($category<>0)
        {
            $query .= " AND post_postcategory.PostCategory_id=$category";
        }
        
        if($order)
        {
            $query .= " ORDER BY $order";
        }
        if($limit)
        {
            $query .= " LIMIT $limit";
        }
        return $this->rows($query);
    }
    /**
     * Read post by slug
     * 
     * @return mixed
     */
    public function readPostBySlug(string $slug)
    {
        $query = "SELECT post.id, post.dateAddPost, post.dateModifyPost, post.title, post.chapo, post.slug, post.content, post.picture, post.status, post.User_id, user.firstName, user.lastName FROM {$this->table} JOIN user on {$this->table}.User_id = user.id WHERE post.slug LIKE '$slug' ";
        return $this->row($query, [$slug]);
    }

    /**
     * Read post by id
     * 
     * @return mixed
     */
    public function readPostById(string $idPost)
    {
        $query = "SELECT post.id, post.dateAddPost, post.dateModifyPost, post.title, post.chapo, post.slug, post.content, post.picture, post.status, post.User_id, user.firstName, user.lastName FROM {$this->table} JOIN user on {$this->table}.User_id = user.id WHERE post.id LIKE '$idPost' ";
        return $this->row($query, [$idPost]);
    }

    /**
     * Read all rows of table
     * 
     * @return array[]
     */
    public function readPostsRecent(string $idPost = "", ?int $status = 1, ?string $order="post.dateAddPost DESC", ?int $limit = 3): array
    {
        $query = "SELECT post.id, post.dateAddPost, post.dateModifyPost, post.title, post.chapo, post.slug, post.content, post.picture, post.status, post.User_id, user.firstName, user.lastName FROM {$this->table} JOIN user on {$this->table}.User_id = user.id WHERE post.status = $status ";

        if ($idPost) {
            $query .= " AND post.id <>  $idPost";
        }
        $query .= " ORDER BY $order";
        $query .= " LIMIT $limit";

        return $this->rows($query);
    }

    public function lastInsertIdPDO()
    {
        $query = "SELECT MAX(id) as lastid FROM {$this->table}";
        $data = $this->pdo->query($query);
        $data = $this->row($query);
        return $data;
    }
}
