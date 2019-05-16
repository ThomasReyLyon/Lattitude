<?php

namespace App\Controller;

use App\Model\UserManager;
use App\Model\PurchaseManager;

class UserController extends \App\Controller\AbstractController
{
	public function showForm()
	{


        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $error = $this->verify($_POST['firstname'], $_POST['lastname'], $_POST['email'], $_POST['password']);

            $objetUser = new UserManager('users');
            $data=$objetUser->selectUser($_POST['email']);


            if (empty($error) && $data == false){

                $objetUser->addUser($_POST['firstname'], $_POST['lastname'], $_POST['email'], $_POST['password']);
                return $this->twig->render('signup.html.twig', ['sucess'=>'Compte bien enregistre']);
            }

            else{

                return $this->twig->render('signup.html.twig', ['error'=>$error]);

            }
        }
        else {
            return $this->twig->render('signup.html.twig');
        }

    }



    /*vérification des champs d'inscription*/


	/**
	 * @param string $firstname
	 * @param string $lastname
	 * @param string $email
	 * @param string $password
	 * @return array
	 */
	private function verify($firstname, $lastname, $email, $password)
	{
		$error = [];

        /*firstname = vérification champs vide et correctement rempli*/
        if (empty($firstname)) {
            $error['firstname'] = "firstname is required";
        }

        if (!preg_match("/^[a-z A-Z]*$/", $firstname)) {
            $error['firstname'] = 'only letters please';
        }

        /*lastname = vérification champs vide et correctement rempli*/
        if (empty($lastname)) {
            $error['lastname'] = "lastname is required";
        }
        if (!preg_match("/^[a-z A-Z]*$/", $lastname)) {
            $error['lastname'] = 'only letters please';
        }

        /*email = vérification champs vide et correctement rempli*/
        if (empty($email)) {
            $error['email'] = 'email is required';
        }
        if (!preg_match(" /^[^\W][a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\@[a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\.[a-zA-Z]{2,4}$/ ", $email)) {
            $error['email'] = 'email address invalid';
        }

        /*password = vérification champs vide et correctement rempli*/
        if (empty($password)) {
            $error['password'] = 'A password is required';
        }
        if (strlen($password) < 8) {
            $error['password'] = 'at least 8 characters';
        }

        return $error;
    }


	/**
	 * @param $email
	 * @param $password
	 * VERIFICATION EMAIL ET PASSWORD EXISTANT DANS BDD
	 */


    public function UserLogin()
    {
        $objetUser = new UserManager();

		if(!empty($_SESSION['email'])){
			header('location: /user/userPage');
		}

        if (isset($_POST['email'])) {
            $data = $objetUser->UserOK($_POST['email']);
// Comparaison du pass envoyé via le formulaire avec la base
            $passwordIsCorrect = password_verify($_POST['password'], $data['password']);



			if (!$passwordIsCorrect) {
				echo 'Wrong email or password... Bullshit';
			} else {
				if ($passwordIsCorrect) {
					// $_SESSION['id'] = $)resultat['id'];
					$_SESSION['email'] = $_POST['email'];
					echo "You're in !";
					header("location: /Home/index");
                }
                
            }
        if ($_POST['email'] == "admin@admin.fr") {
            header("location: /Admin/index/");
        }
		}

		$loggedIn="";
		$data2="";
		if(!empty($_SESSION['email'])){
			$loggedIn=$_SESSION['email'];
			$data2=$objetUser->UserOK($loggedIn);
		}
		return $this->twig->render('login.html.twig', ['loggedIn'=>$loggedIn, 'data'=>$data2]);
	}

    public function userPage()
    {
        $userManager = new UserManager();
        $userprofil = $userManager->selectUser($_SESSION['email']);

        $purchaseManager = new PurchaseManager();
        $listOrders = $purchaseManager->selectOrder($userprofil['id']);

        $listSteamKeys = $purchaseManager->SelectKey($userprofil['id']);

        return $this->twig->render('UserPage/user_page.html.twig', ['user' => $userprofil, 'listorder' => $listOrders, 'listkey' => $listSteamKeys]);
    }

	public function userAdmin()
	{
		$userManager = new UserManager();

		$allUsers = $userManager->selectAll();

		return $this->twig->render('Admin/user_admin.html.twig', ['allUsers' => $allUsers]);
	}

}
