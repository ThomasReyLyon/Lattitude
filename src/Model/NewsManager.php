<?php

namespace App\Model;

/**
 * Manage interaction with user table in the database
 */
class NewsManager extends AbstractManager
{
    /**
     *
     */
    const TABLE = 'news'; // NOM DE LA TABLE A COMPLETER !!!!!!!!!
    /**
     * Initializes this class.
     */
    public function __construct()//
    {
        parent::__construct(self::TABLE);
    }
    /**
    4 / 5SimpleMVC.md
    4/11/2019
     * Insère une news par rapport au tableau associatif $news
     * passé en paramètre.
     * @param array $news
    tableau associatif des champs d'une news
     * @return int
    renvoie la news
     */
    public function insert(array $news): int
    {
        // prepared request
        // pdo est hérité de la classe parente, allez y jeter un oeil ;)
        $statement = $this->pdo->prepare("INSERT INTO $this->table(`date`, titre, description, image) VALUES (CURDATE(), :titre, :description, :image)");
        $statement->bindValue('titre', $news['titre'], \PDO::PARAM_STR);
        $statement->bindValue('description', $news['description'], \PDO::PARAM_STR);
        $statement->bindValue('image', $news['image'], \PDO::PARAM_STR);
        if ($statement->execute()) {
            return (int)$this->pdo->lastInsertId();
        }
    }
    public function delete(int $id): void
    {
        // prepared request
        $statement = $this->pdo->prepare("DELETE FROM $this->table WHERE id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();
    }
    public function edit($edit, $id)
    {
        $statement = $this->pdo->prepare("UPDATE $this->table SET titre=:titre, description=:description WHERE id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->bindValue('titre', $edit['titre'], \PDO::PARAM_STR);
        $statement->bindValue('description', $edit['description'], \PDO::PARAM_STR);
        $statement->execute();
    }

}
