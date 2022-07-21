<?php

namespace Blog\Models;

class Comment extends Model
{

    protected $table = "comment";

    /**
     * Read all comments by status
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
     * Read all comments by user
     * @return array[]
     */
    public function readAllCommentsByUser(int $idUser, ?string $select = "", ?string $limit = ""): array
    {
        $query = "SELECT comment.id,comment.datePublishComment,comment.dateAddComment,comment.content,comment.status,comment.parentId,comment.User_id,comment.Post_id,post.title, user.firstName, user.lastName, user.picture FROM {$this->table} JOIN post on {$this->table}.Post_id = post.id JOIN user on comment.User_id = user.id WHERE comment.User_id = $idUser ";

        if ($select) {
            $query = "SELECT $select FROM {$this->table} JOIN post on {$this->table}.Post_id = post.id JOIN user on comment.User_id = user.id WHERE comment.User_id = $idUser ";
        }

        
        $query .= " ORDER BY comment.dateAddComment DESC";
        
        if ($limit) {
            $query .= " LIMIT $limit";
        }
        return $this->rows($query);
    }

    /**
     * Read lasts comments by user
     * @return array[]
     */
    public function readLastCommentUser(int $idUser, ?string $limit = '10'): array
    {
        $query = "SELECT comment.id,comment.datePublishComment,comment.dateAddComment,comment.content,comment.status,comment.parentId,comment.User_id,comment.Post_id,post.title FROM {$this->table} JOIN post on {$this->table}.Post_id = post.id WHERE comment.User_id = $idUser";
        $query .= " ORDER BY dateAddComment DESC";
        $query .= " LIMIT $limit";
        return $this->rows($query);
    }

    /**
     * Read parent comments
     * @return array[]
     */
    public function readAllCommentsParent(?int $idPost = 0): array
    {
        $query = "SELECT comment.id,comment.datePublishComment,comment.dateAddComment,comment.content,comment.status,        comment.parentId,comment.User_id,comment.Post_id,post.title, user.firstName, user.lastName, user.picture FROM {$this->table} JOIN post on {$this->table}.Post_id = post.id JOIN user on comment.User_id = user.id WHERE parentId = 0 AND comment.status = 1";

        if ($idPost <> 0) {
            $query .= " AND  comment.Post_id=$idPost ORDER BY dateAddComment DESC";
            return $this->rows($query);
        }
        $query .= " ORDER BY dateAddComment DESC";
        return $this->rows($query);
    }

    /**
     * Read child comments
     * @return array[]
     */
    public function readAllCommentsChild(?int $idPost = 0): array
    {
        $query = "SELECT comment.id,comment.datePublishComment,comment.dateAddComment,comment.content,comment.status,        comment.parentId,comment.User_id,comment.Post_id,post.title, user.firstName, user.lastName, user.picture FROM {$this->table} JOIN post on {$this->table}.Post_id = post.id JOIN user on comment.User_id = user.id WHERE parentId <>0 AND comment.status = 1 ";
        if ($idPost <> 0) {
            $query .= "AND  comment.Post_id=$idPost ORDER BY parentId ASC, dateAddComment DESC";
            return $this->rows($query);
        }
        $query .= " ORDER BY parentId ASC, dateAddComment DESC";
        return $this->rows($query);
    }

    /**
     * Read comment by id
     * @return mixed
     */
    public function readCommentById(int $idComment)
    {
        $query = "SELECT comment.id,comment.datePublishComment,comment.dateAddComment,comment.content,comment.status,comment.parentId,comment.User_id,comment.Post_id,post.title, user.firstName, user.lastName, user.picture 
        FROM {$this->table} 
        JOIN post on {$this->table}.Post_id = post.id 
        JOIN user on comment.User_id = user.id 
        WHERE comment.id LIKE $idComment";

        return $this->row($query, [$idComment]);
    }
}
