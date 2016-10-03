<?php
require_once('databases.php');
//this is the new class that extends the database and overloads the resultset method and implements the //leftJoin
class Tags extends Database {
    public function resultset(){
        $posts = parent::resultset();

        //Make sure I have results.
        if(is_array($posts) && count($posts)){
            foreach($posts as &$post){
                //Init the $tags to an empty array.
                $tags = [];

                $sql = 'SELECT
                  t.name
                FROM
                  blog_post_tags bpt
                LEFT JOIN
                  tags t
                ON
                  bpt.tag_id = t.id
                WHERE
                  bpt.blog_post_id = :blogid
                ';
// This is the query that executes the leftJoin
                parent::query($sql);
                parent::bind(':blogid', $post['id']);
                $blogTags = parent::resultset();

                foreach($blogTags as $btag){
                    array_push($tags, $btag['name']);
                }
//implode takes an array of strings and basically glues them together with a separator i.e. a comma and //a space.
                $post['tags'] = implode(', ', $tags);
            }
//this return returns the post results to the calling method and then the return [ ] is an empty array //because the calling method expects an array
            return $posts;
        }else{
            return [];
        }
    }
}
