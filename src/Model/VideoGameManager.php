<?php


namespace App\Model;

class VideoGameManager extends AbstractManager
{
    const TABLE = 'games';

    /**
     *  Initializes this class.
     */
    public function __construct()
    {
        parent::__construct(self::TABLE);
    }


    /**
     * @param array $item
     * @return int
     */
    public function insert(array $game): int
    {
        
        // prepared request
        $statement = $this->pdo->prepare("INSERT INTO $this->table (`name`, prix, description, photo1, photo2, photo3, games_categories_id) 
										VALUES (:game_name, :price, :description, :pic1, :pic2, :pic3, :category)");
        $statement->bindValue('game_name', $game['name'], \PDO::PARAM_STR);
        $statement->bindValue('price', $game['price'], \PDO::PARAM_INT);
        $statement->bindValue('description', $game['description'], \PDO::PARAM_STR);
        $statement->bindValue('pic1', $game['pic1'], \PDO::PARAM_STR);
        $statement->bindValue('pic2', $game['pic2'], \PDO::PARAM_STR);
        $statement->bindValue('pic3', $game['pic3'], \PDO::PARAM_STR);
        $statement->bindValue('category', $game['category'], \PDO::PARAM_INT);

        if ($statement->execute()) {
            return (int)$this->pdo->lastInsertId();
        }
    }

    public function selectGames(): array
    {
        return $this->pdo->query("SELECT * FROM $this->table LIMIT 0,12")->fetchAll();
    }



    public function delete(int $id): void
    {
        // prepared request
        $statement = $this->pdo->prepare("DELETE FROM $this->table WHERE id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();
    }
        /**
     * @return array
     */
    public function topGame():array
    {
        return $this->pdo->query("SELECT * FROM $this->table WHERE top_game = 2")->fetch();
    }

    public function updateGameText(array $gameUpdate, $id):bool
    {

        $statement = $this->pdo->prepare("UPDATE $this->table SET name = :name, description =:description, prix =:prix, games_categories_id=:cat  WHERE id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->bindValue('name', $gameUpdate['game_name'], \PDO::PARAM_STR);
        $statement->bindValue('description', $gameUpdate['game_description'], \PDO::PARAM_STR);
        $statement->bindValue('prix', $gameUpdate['game_price'], \PDO::PARAM_INT);
        $statement->bindValue('cat', $gameUpdate['category'], \PDO::PARAM_INT);

        return $statement->execute();
    }

    public function updateGamePics(array $gamePics, $id):bool
    {

        $statement = $this->pdo->prepare("UPDATE $this->table SET photo1 = :pic1, photo2 = :pic2, photo3 = :pic3  WHERE id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->bindValue('pic1', $gamePics['pic1'], \PDO::PARAM_STR);
        $statement->bindValue('pic2', $gamePics['pic2'], \PDO::PARAM_STR);
        $statement->bindValue('pic3', $gamePics['pic3'], \PDO::PARAM_INT);


        return $statement->execute();
    }


    public function selectGamebyName($name):array
    {
        $statement = $this->pdo->prepare("SELECT * FROM $this->table WHERE `name`=:gamename");
        $statement->bindValue('gamename', $name, \PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetch();
    }
}
