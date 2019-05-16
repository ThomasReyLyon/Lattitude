<?php


namespace App\Model;

use App\Controller\AbstractController;

class PurchaseManager extends AbstractManager
{
    const TABLE = 'orders';

    public function __construct()
    {
        parent::__construct(self::TABLE);
    }


    /**
     * @param $email
     * @return mixed
     */
    public function userData($email)
    {
        //  Récupération de l'utilisateur et de son pass hashé


        // prepared request
        $statement = $this->pdo->prepare("SELECT * FROM users WHERE email=:email");
        $statement->bindValue('email', $email, \PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetch();
    }

    public function insertOrders($userId, $amount)
    {
        $statement = $this->pdo->prepare("INSERT INTO $this->table(date_order, amount, fk_user_id) VALUES (NOW(),:amount, :fk_user_id)");
        $statement->bindValue('amount', $amount, \PDO::PARAM_INT);
        $statement->bindValue('fk_user_id', $userId, \PDO::PARAM_INT);


        if ($statement->execute()) {
            return (int)$this->pdo->lastInsertId();
        }
    }

    public function insertGameOrder($steamKey, $gameId, $ordersId): int
    {
        // prepared request
        $statement = $this->pdo->prepare("INSERT INTO order_games (steam_key, fk_game_id, fk_orders_id) VALUES (:steamKey, :gameId, :ordersId)");
        $statement->bindValue('steamKey', $steamKey, \PDO::PARAM_STR);
        $statement->bindValue('gameId', $gameId, \PDO::PARAM_INT);
        $statement->bindValue('ordersId', $ordersId, \PDO::PARAM_INT);

        if ($statement->execute()) {
            return (int)$this->pdo->lastInsertId();
        }
    }

    public function selectGamebyName($name):array
    {
        $statement = $this->pdo->prepare("SELECT * FROM games WHERE `name`=:gamename");
        $statement->bindValue('gamename', $name, \PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetch();
    }

    public function selectOrder($user_id)
    {
        $statement = $this->pdo->prepare("SELECT * FROM orders WHERE `fk_user_id`=:user_id");
        $statement->bindValue('user_id', $user_id, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function selectKey($order_id)
    {
        $statement = $this->pdo->prepare("SELECT orders.*, order_games.*, games.* FROM orders JOIN order_games ON orders.id = order_games.fk_orders_id JOIN games ON order_games.fk_game_id = games.id WHERE fk_user_id=:order_id");
        $statement->bindValue('order_id', $order_id, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function selectAllWithUsers($limit) :array
    {
         return $this->pdo->query("SELECT orders.*, users.email FROM users JOIN orders ON orders.fk_user_id = users.id ORDER BY orders.id DESC LIMIT $limit, 10")->fetchAll();
    }

    public function selectGamesOrdersWithGames():array
    {
        return $this->pdo->query('SELECT order_games.*, games.name, games.prix as nbr_sales FROM order_games JOIN games ON games.id = order_games.fk_game_id')->fetchAll();
    }

	public function selectGamesSales():array
	{
		return $this->pdo->query('SELECT games.name, COUNT(*) as nbr_sales, MIN(games.prix) AS price FROM order_games JOIN games ON games.id = order_games.fk_game_id GROUP BY games.name')->fetchAll();
	}



}
