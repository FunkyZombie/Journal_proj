<?php

namespace Journal\Blog;

class Like {
    public function __construct(
        private UUID $uuid,
        private UUID $post_uuid,
        private UUID $user_uuid
    ){}
    
    public function uuid(): UUID
    {
        return $this->uuid;
    }
    
    public function postUuid(): UUID
    {
        return $this->post_uuid;
    }
    
    public function userUuid(): UUID
    {
        return $this->user_uuid;
    }
}