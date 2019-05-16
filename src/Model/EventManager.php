<?php
namespace App\Model;
/**
 * Manage interaction with user table in the database
 */
class EventManager extends AbstractManager
{
    /**
     *
     */
    const TABLE = 'event_test'; // NOM DE LA TABLE A COMPLETER !!!!!!!!
    /**
     * Initializes this class.
     */
    public function __construct()
    {
        parent::__construct(self::TABLE);
    }

    public function insert(array $event): int
    {
        // prepared request

        $statement = $this->pdo->prepare("INSERT INTO $this->table (titre, `date`, city, description, photo) VALUES (:titre, :date, :city, :description, :photo)");
        $statement->bindValue('titre', $event['titre'], \PDO::PARAM_STR);
        $statement->bindValue('date', $event['date'], \PDO::PARAM_STR);
        $statement->bindValue('city', $event['city'], \PDO::PARAM_STR);
        $statement->bindValue('description', $event['description'], \PDO::PARAM_STR);
        $statement->bindValue('photo', $event['photo'], \PDO::PARAM_STR);
        if ($statement->execute()) {
            return (int)$this->pdo->lastInsertId();
        }
    }

   //--------  DELETE (MANAGER)

    public function delete(int $id): void
    {
        // prepared request
        $statement = $this->pdo->prepare("DELETE FROM $this->table WHERE id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();
    }

    //--------  MODIFY (MANAGER)

    public function updateEventText(array $eventUpdate, $id):bool
    {

        $statement = $this->pdo->prepare("UPDATE $this->table SET titre = :titre, `date` = :date, city = :city, description = :description WHERE id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->bindValue('titre', $eventUpdate['titre'], \PDO::PARAM_STR);
        $statement->bindValue('date', $eventUpdate['date'], \PDO::PARAM_STR);
        $statement->bindValue('city', $eventUpdate['city'], \PDO::PARAM_STR);
        $statement->bindValue('description', $eventUpdate['description'], \PDO::PARAM_STR);

        return $statement->execute();
    }



}

