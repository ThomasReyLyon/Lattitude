<?php

namespace App\Model;

class ContactManager extends AbstractManager
{
    	const TABLE = 'users_test'; 

	public function __construct()
	{
		parent::__construct(self::TABLE);
	}

	public function insert(array $contact): int
	{

		$statement = $this->pdo->prepare("INSERT INTO $this->table
		(firstname, lastname, username, city, `text`) VALUES (:firstname, :lastname,
		:username, :city, :text)");

		$statement->bindValue('firstname', $contact['firstname'],\PDO::PARAM_STR);

		$statement->bindValue('lastname', $contact['lastname'],\PDO::PARAM_STR);

		$statement->bindValue('username', $contact['username'],\PDO::PARAM_STR);

		$statement->bindValue('city', $contact['city'],\PDO::PARAM_STR);

		$statement->bindValue('text', $contact['text'],\PDO::PARAM_STR);

		if ($statement->execute())
	 	{
			return (int)$this->pdo->lastInsertId();
		}
	}
}