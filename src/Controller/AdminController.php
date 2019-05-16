<?php
/**
 * Created by PhpStorm.
 * User: aurelwcs
 * Date: 08/04/19
 * Time: 18:40
 */

namespace App\Controller;

use App\Model\PurchaseManager;
use App\Model\VideoGameManager;

class AdminController extends AbstractController
{

    /**
     * Display home page
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function index($limitOrders)
    {

        $ordersList = new PurchaseManager();

        // I set a $limitOrders variable that comes from the link above the latest order table.
        // The parameter returns a figure (1,2,3, 4 etc...) that i multiply by 10. It sets a LIMIT in the SQL query.
        $limitOrders = (int) $limitOrders*10;

        //Returns 10 orders + users Email, starting by the line $limitOrders.
        $allOrders = $ordersList->selectAllWithUsers($limitOrders);

        // Returns all Orders. I need it to count the overall amount of orders, and thus create a page system in twig.
        $allOrdersCount = $ordersList->selectAll();

        // Return all single games purchase
        $gameOrdersList = $ordersList->selectGamesOrdersWithGames();
        //var_dump($gameOrdersList);

        // Return game list with name and price.
        $gameList = new VideoGameManager();
        $allGameList = $gameList->selectAll(); //returns all games with name, price description etc...

        $sales = $ordersList->selectGamesSales(); //Return game name and nbr of sales.




        return $this->twig->render('Admin/dashboard.html.twig', [
            'titre' => 'Dashboard',
            'allOrders' => $allOrders, // return 10 orders.
            'ordersCount' => $allOrdersCount, //Used to count number of orders in page system.
            'indexPage' => 0, //Use for page system in latest orders.
            'gameOrdersList' => $gameOrdersList, // Returns all games orders. Used to display all latest orders.
            'sales' => $sales,
            ]);
    }
}
