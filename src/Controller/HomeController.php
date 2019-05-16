<?php
/**
 * Created by PhpStorm.
 * User: aurelwcs
 * Date: 08/04/19
 * Time: 18:40
 */

namespace App\Controller;

use App\Model\NewsManager;
use App\Model\VideoGameManager;
use App\Model\EventManager;

class HomeController extends AbstractController
{
    /**
     * Display home page
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function index()
    {

        //display video games cards.
        $games= new VideoGameManager();
        $gamesData=$games->selectGames();
        $newsManager = new NewsManager();
        $news = $newsManager->selectAll();
        $eventManager = new EventManager();
        $event = $eventManager->selectAll();


        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $addedToCartGameName=array_keys($_POST);
            $addedToCartGameName=$addedToCartGameName[0];

            $_SESSION['article'][] = $addedToCartGameName;
        }

        $sessionInfo=$_SESSION;

        return $this->twig->render('Home/index.html.twig', ['gamesData'=> $gamesData,
            'news'=> $news, 'session'=>$sessionInfo, 'event'=>$event]);
    }

    public function deconnect()
    {
        session_start();
        session_destroy();
        header('location:/Home/index');
    }


    public function deconnectFromCart()
    {
        session_start();
        session_destroy();
        header('location:/Purchase/cart');
    }

    public function deconnectFromLogin()
    {
        session_start();
        session_destroy();
        header('location:/Purchase/login');
    }


    /**
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function news()
    {
        $newsManager = new NewsManager();
        $news = $newsManager->selectAll();
        return $this->twig->render('Home/news.html.twig', ['news' =>$news]);
    }

    /**
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function games()
    {
        $gameManager = new VideoGameManager();
        $games = $gameManager->selectAll();
        return $this->twig->render('Home/games.html.twig', ['games' =>$games]);
    }
}
