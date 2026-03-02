<?php
namespace alt\core\application\ports\spi;

use alt\core\application\dto\CreatePostDTO;
use alt\core\domain\entities\Post;

interface PostRepositoryInterface{
      
/**
 * @param int $page
 * @param int $limit
 * @return Post[]
 */
    public function findAll(int $page, int $limit): array;    
    public function findById(int $idPost):Post;
    public function findByIdWithStats(int $idPost):array;
    public  function create(CreatePostDTO $post):CreatePostDTO;

}