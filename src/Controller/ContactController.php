<?php
namespace App\Controller;
use App\Model\ContactManager;

class ContactController extends AbstractController {
	
	public function form (){
		$errors = [];

		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$contact = [
				'firstname' => $_POST['firstname'],
				'lastname'  => $_POST['lastname'],
				'username'  => $_POST['username'],
				'city'      => $_POST['city'],
				'text'      => $_POST['text']
			];

			$errors = $this->checkContact($contact);
			if (empty($errors)) { 
				$contactManager = new ContactManager();
				$contactManager->insert($contact);
			}
		}

	 	return $this->twig->render('Contact/form.html.twig', [ 'errors' => $errors ]);	
	}

	private function checkContact($contact) {
		$errors = [];
	
		if (!isset($contact['firstname']) || empty($contact['firstname'])) {
			$errors['firstname'] = "Le prénom est obligatoire"; 
        }

		elseif ( preg_match ("/^[a-zA-Z0-9_]$/" , $contact['firstname'] ) )
		
		if (!isset($contact['lastname']) || empty($contact['lastname'])) {
			$errors['lastname'] = "Le nom de famille est obligatoire"; 
        }

		elseif ( preg_match ("/^[a-zA-Z0-9_]{3,16}$/" , $contact['lastname'] ) )
		if (!isset($contact['username']) || empty($contact['username'])) {
			$errors['username'] = "Le nom d'utilisateur est obligatoire";
        }

		elseif	( preg_match ("/^[^\W][a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\@[a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\.[a-zA-Z]{2,4}$/" , $contact['username'] ) )
		if (!isset($contact['city']) || empty($contact['city'])) {
			$errors['city'] = "La ville est obligatoire"; 
		}

		elseif ( preg_match ( " /^[a-zA-Z0-9_]$/ " , $contact['city'] ) )

		if (!isset($contact['text']) || empty($contact['text'])) {
			$errors['text'] = "Le text est obligatoire"; 
        }

		elseif ( preg_match ("/^[a-zA-Z0-9_]$/" , $contact['text'] ) )

		return $errors;
	}
}