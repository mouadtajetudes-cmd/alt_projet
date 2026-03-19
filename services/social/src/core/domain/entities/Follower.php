<?php
namespace alt\core\domain\entities;

class Follower{
    protected int $id_follower;
    protected int $id_following;
    public function __construct(int $id_follower, int $id_following) {
        $this->id_follower = $id_follower;
        $this->id_following = $id_following;
    }
    public function getIdFollower(): int{
        return $this->id_follower;
    }
    public function getIdFollowing(): int
       {  
        return $this->id_following;
        }     
   
}