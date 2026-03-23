<?php
namespace alt\core\infra\repositories;
use alt\core\application\ports\api\CredentialDTO;
use alt\core\application\ports\api\ProfileDTO;
use alt\core\domain\entities\User;
use alt\core\repositories\UserRepository;
use PDO;
class PdoUserRepository implements  UserRepository{
    private PDO $pdo;

    public function __construct(PDO $pdo){
        $this->pdo=$pdo;
    } 
        public function byCredentials(CredentialDTO $credentials):ProfileDTO{
        $stmt=$this->pdo->prepare('select * from utilisateurs where email=:email');
        $stmt->execute(['email'=>$credentials->email]);
        $row=$stmt->fetch(PDO::FETCH_ASSOC);
        if(!$row){
            throw new \Exception('email introuvable');
        }
        if(!password_verify($credentials->password,$row['password'])){
            throw  new \Exception('mot de passe incorrect');
        }
        return new ProfileDTO(
            $row['id'],
            $row['email'],
            $row['isAdmin']
        );
    }

    public function byEmail(string $email): User{
        $stmt=$this->pdo->prepare('select * from utilisateurs where email=:email');
        $stmt->execute(['email'=>$email]);
        $row=$stmt->fetch(PDO::FETCH_ASSOC);
        if(!$row){
            throw new \Exception('email introuvable');
        }
        return new User(
        $row['id'],
        $row['nom'],
        $row['prenom'],
        $row['email'],
        $row['password'],
        $row['isAdmin']
    );
    }
 public function findById(int $id):User
    {
        $stmt = $this->pdo->prepare('SELECT * FROM utilisateurs WHERE id_utilisateur = :id');
        $stmt->execute(['id_utilisateur' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);


        return new User(
            $row['id'],
            $row['nom'],
            $row['prenom'],
            $row['email'],
            $row['password'],
            $row['isAdmin']
        );
    }    

}