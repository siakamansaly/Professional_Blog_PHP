<?php

namespace Blog\Models;


class Comment extends Model
{

    protected $table = "comment";

    /**
     * Read all rows of table
     * 
     * @return array[]
     */
    public function readAllCommentsByStatus(string $status = "1", ?string $order = "comment.dateAddComment DESC", ?string $limit = ""): array
    {
        $query = "SELECT comment.id,comment.datePublishComment,comment.dateAddComment,comment.content,comment.status,comment.parentId,comment.User_id,comment.Post_id,post.title, user.firstName, user.lastName, user.picture FROM {$this->table} JOIN post on {$this->table}.Post_id = post.id JOIN user on comment.User_id = user.id WHERE comment.status IN ($status) ";
        if ($order) {
            $query .= " ORDER BY $order";
        }
        if ($limit) {
            $query .= " LIMIT $limit";
        }
        return $this->rows($query);
    }

    /**
     * Read all rows of table
     * 
     * @return array[]
     */
    public function readAllCommentsByUser(string $idUser, ?string $select = "", ?string $order = "comment.dateAddComment DESC", ?string $limit = ""): array
    {
        $query = "SELECT comment.id,comment.datePublishComment,comment.dateAddComment,comment.content,comment.status,comment.parentId,comment.User_id,comment.Post_id,post.title, user.firstName, user.lastName, user.picture FROM {$this->table} JOIN post on {$this->table}.Post_id = post.id JOIN user on comment.User_id = user.id WHERE comment.User_id = $idUser ";
        
        if ($select) {
            $query = "SELECT $select FROM {$this->table} JOIN post on {$this->table}.Post_id = post.id JOIN user on comment.User_id = user.id WHERE comment.User_id = $idUser ";
        } 
        
        if ($order) {
            $query .= " ORDER BY $order";
        }
        if ($limit) {
            $query .= " LIMIT $limit";
        }
        return $this->rows($query);
    }

    /**
     * Read all rows of table
     * 
     * @return array[]
     */
    public function readLastCommentUser(int $idUser, ?string $order = "dateAddComment DESC", ?string $limit = '10'): array
    {
        $query = "SELECT comment.id,comment.datePublishComment,comment.dateAddComment,comment.content,comment.status,comment.parentId,comment.User_id,comment.Post_id,post.title FROM {$this->table} JOIN post on {$this->table}.Post_id = post.id WHERE comment.User_id = $idUser";
        $query .= " ORDER BY $order";
        $query .= " LIMIT $limit";
        return $this->rows($query);
    }

    /**
     * Read all rows of table
     * 
     * @return array[]
     */
    public function readAllCommentsParent(?int $idPost = 0, ?string $where = "", ?string $order = "dateAddComment DESC"): array
    {
        $query = "SELECT comment.id,comment.datePublishComment,comment.dateAddComment,comment.content,comment.status,        comment.parentId,comment.User_id,comment.Post_id,post.title, user.firstName, user.lastName, user.picture FROM {$this->table} JOIN post on {$this->table}.Post_id = post.id JOIN user on comment.User_id = user.id WHERE parentId = 0 AND comment.status = 1 ";
        if ($idPost <> 0) {
            $query .= "AND  comment.Post_id=$idPost";
        }
        if ($where) {
            $query .= " " . $where;
        }
        $query .= " ORDER BY " . $order;
        return $this->rows($query);
    }

    /**
     * Read all rows of table
     * 
     * @return array[]
     */
    public function readAllCommentsChild(?int $idPost = 0, ?string $where = "", ?string $order = "parentId ASC, dateAddComment DESC"): array
    {
        $query = "SELECT comment.id,comment.datePublishComment,comment.dateAddComment,comment.content,comment.status,        comment.parentId,comment.User_id,comment.Post_id,post.title, user.firstName, user.lastName, user.picture FROM {$this->table} JOIN post on {$this->table}.Post_id = post.id JOIN user on comment.User_id = user.id WHERE parentId <>0 AND comment.status = 1 ";
        if ($idPost <> 0) {
            $query .= "AND  comment.Post_id=$idPost";
        }
        if ($where) {
            $query .= " " . $where;
        }
        $query .= " ORDER BY " . $order;
        return $this->rows($query);
    }

    /**
     * Read post by id
     * 
     * @return mixed
     */
    public function readCommentById(string $idComment)
    {
        $query = "SELECT comment.id,comment.datePublishComment,comment.dateAddComment,comment.content,comment.status,comment.parentId,comment.User_id,comment.Post_id,post.title, user.firstName, user.lastName, user.picture FROM {$this->table} JOIN post on {$this->table}.Post_id = post.id JOIN user on comment.User_id = user.id WHERE comment.id LIKE '$idComment' ";
        return $this->row($query, [$idComment]);
    }
}
