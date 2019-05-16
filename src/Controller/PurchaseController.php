<?php

namespace App\Controller;

use App\Model\UserManager;
use App\Model\VideoGameManager;
use App\Model\PurchaseManager;

class PurchaseController extends AbstractController
{

    public function cart()
    {

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $addedToCartGameName=array_keys($_POST);

            $_SESSION['article'][] = $addedToCartGameName[0];
        }


        $gamesInCartData=[];
        if (!empty($_SESSION['article'])) {
            $sessionInfo = $_SESSION['article'];
            $gamesInQuestion = new VideoGameManager();
            foreach ($sessionInfo as $key => $value) {
                $value = str_replace("_", " ", $value);
                $gamesInCartData[$key] = $gamesInQuestion->selectGamebyName($value);
            }
        }
        $totalPrice = 0;


        foreach ($gamesInCartData as $value) {
            $totalPrice += $value['prix'];
        }

        $_SESSION['total']=$totalPrice;


        return $this->twig->render('Home/purchases/cart.html.twig', ['GamesInCart'=>$gamesInCartData, 'totalPrice'=>$totalPrice]);
    }

    public function login()
    {
        $objetUser = new UserManager();


        if (isset($_POST['email'])) {
            $data = $objetUser->UserOK($_POST['email']);
// Comparaison du pass envoyé via le formulaire avec la base
            $passwordIsCorrect = password_verify($_POST['password'], $data['password']);

            if (!$passwordIsCorrect) {
                echo 'Wrong email or password... Bullshit';
            } else {
                if ($passwordIsCorrect) {
                    $_SESSION['email'] = $data['email'];
                    header('location: /purchase/payment');
                }
            }
        }
        $sessionEmail="";
        if (!empty($_SESSION['email'])) {
            $sessionEmail=$_SESSION['email'];
        }
        return $this->twig->render('Home/purchases/login.html.twig', ['sessionEmail'=>$sessionEmail]);
    }

    public function payment()
    {

        // Set your secret key: remember to change this to your live secret key in production
// See your keys here: https://dashboard.stripe.com/account/apikeys
        \Stripe\Stripe::setApiKey("sk_test_85mLoja0yWvD8ONQRfcsCvf2002HnILfkM");

        $stripedata=\Stripe\Checkout\Session::create([
            'success_url' => 'http://localhost:8000/purchase/confirmation',
            'cancel_url' => 'http://www.stripe.com/', // Mettre page erreur
            'payment_method_types' => ['card'],
            'line_items' => [[
                'amount' => $_SESSION['total']*100, // ICI 5 EUR. c'est X 100. Prix en CENTIMES.
                'currency' => 'eur',
                'name' => 'L\'Attitude Video Game',
                'description' => 'The finest video Games from WCS',
                'quantity' => 1,
            ]]
        ]);

        //$stripedata=json_encode($stripedata);

        return $this->twig->render('Home/purchases/payment.html.twig', ['stripe'=>$stripedata]);
    }

    public function confirmation()
    {
        $purchasedGames['key']=$this->generateKey(count($_SESSION['article']));
        $purchasedGames['article'] = $_SESSION['article'];

        $clientEmail = $_SESSION['email'];

        $total = $_SESSION['total'];


        // Create a new Object CLient Data that 1/ collect Usersdata into the Users table 2/ use the collected data (id) and sends it to the InsertOrders method to update the order table.
        $clientData = new PurchaseManager();
        $dataForInsert = $clientData->userData($clientEmail);

        $clientData->insertOrders($dataForInsert['id'], $total);

        $ordersData = $clientData->selectOrder($dataForInsert['id']);
        $ordersData = end($ordersData);

        foreach ($purchasedGames['article'] as $index => $value) {
            $gameDataForInsert = $clientData->selectGamebyName($value);
            $clientData->insertGameOrder($purchasedGames['key'][$index], $gameDataForInsert['id'], $ordersData['id']);
        }



        // START EMAIL SENDING SECTION

        // Create the Transport

        $transport = (new \Swift_SmtpTransport('smtp.gmail.com', 587, 'tls'))
            ->setUsername('lattitudepublishing@gmail.com')
            ->setPassword('Lattitude69!')
        ;

        // Create the Mailer using your created Transport
        $mailer = new \Swift_Mailer($transport);

        // Create a message
        $message = (new \Swift_Message('Order Confirmed!'))
            ->setFrom(['lattitudepublishing@gmail.com' => "L'Attitude Corp."])
            ->setTo($clientEmail)
            ->setBody($this->twig->render(
                'email/confirm.html.twig',
                ['clientEmail'=>$clientEmail,
                'purchasedGames'=>$purchasedGames,
                'total'=>$total
                ]
            ), 'text/html');


        // Send the message
        $result = $mailer->send($message);

        // END EMAIL SENDING SECTION

        return $this->twig->render(
            'Home/purchases/confirmation.html.twig',
            ['clientEmail'=>$clientEmail,
            'purchasedGames'=>$purchasedGames,
            'total'=>$total,
            'orderId'=>$ordersData['id']
            ]
        );
    }

    private function generateKey($numberOfGames)
    {
        $keyLenght = 20;
        $allowed_characters = ['0','1','2','3','4','5','6','7','8','9',
            'a','z','e','r','t','y','u','i','o','p','q','s','d','f','g','h',
            'j','k','l','m','w','x','c','v','b','n'];

        for ($n=0; $n < $numberOfGames; $n++) {
            $steamKey[$n]="";
            for ($i = 1; $i < $keyLenght; $i++) {
                if ($i % 5 == 0) {
                    $steamKey[$n] .= "-";
                } else {
                    $steamKey[$n] .= strtoupper($allowed_characters[array_rand($allowed_characters)]);
                }
            }
        }
        return $steamKey;
    }
}
