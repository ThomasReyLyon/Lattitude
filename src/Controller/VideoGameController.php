<?php


namespace App\Controller;

use App\Model\VideoGameManager;

class VideoGameController extends AbstractController
{

    /**
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function displayForm()
    {


        $data = $this->showData();
        $topGame= $this->showTopGame();

        /* Here i check whether some Post Data are available.
        If yes, the POST values are sent to the verifyField function.
        */
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $error = $this->verifyfield(
                $_POST['game_name'],
                $_POST['game_price'],
                $_POST['game_description'],
                $_POST['category'],
                $_FILES['picture']['name']
            );
        }

        /* The block below checks whether the extension of the uploaded files is OK, and if yes,
         then checks also the size. If all good, it will initiate the movefile function to upload the file.
        */

        if (isset($_FILES['picture']) && empty($error['picture'])) {
            $accepted_extension = ['jpg', 'png', 'jpeg'];
            for ($i = 0; $i <= 2; $i++) {
                $uploaded_extension[$i] = strtolower(substr(strrchr($_FILES['picture']['name'][$i], "."), 1));
                $fileTitle = $_FILES['picture']['name'][$i];

                if (in_array($uploaded_extension[$i], $accepted_extension)
                    || !empty($_FILES['picture']['tmp_name'][$i])
                    || $_FILES['picture']['size'][$i] < 8000000) {
                    $newFileName = "assets/images/games/$fileTitle";
                    $newTmp = "assets/images/games/tmp/$fileTitle";

                    $this->moveFile($_FILES['picture']['tmp_name'][$i], $newTmp, $newFileName);
                } else {
                    $errorFile = 'Echec du chargement du fichier. Veuillez rééssayer.';
                }
            }
        }


        /* That one part will load a table,
        named game and sends it as parameter to the insert function in VideoGameManager.
        Thus sending the data to the DB.
       */

        if (!empty($error['success'])) {
            $addedGame = new VideoGameManager();
            $game = [
                'name' => $_POST['game_name'], //OK
                'price' => $_POST['game_price'],//OK
                'description' => $_POST['game_description'],//OK
                'pic1' => 'assets/images/games/' . $_FILES['picture']['name'][0], //OK
                'pic2' => 'assets/images/games/' . $_FILES['picture']['name'][1],//OK
                'pic3' => 'assets/images/games/' . $_FILES['picture']['name'][2],//OK
                'category' => $_POST['category']//OK
            ];
            $addedGame->insert($game);

            return $this->twig->render(
                'Admin/game_admin.html.twig',
                [
                    'errors' => $error,
                    'data' => $data,
                    'topGame'=>$topGame,
                    'headtitle' => 'Game Mngmt',
                    'titre' => 'Game'
                ]
            );
        } elseif (!empty($error['game_name']) || !empty($error['game_price']) || !empty($error['game_desc']) || !empty($error['category']) || !empty($error['picture'])) {
            return $this->twig->render('Admin/game_admin.html.twig', [
                'data' => $data,
                'errors' => $error,
                'topGame'=> $topGame,
                'headtitle' => 'Game Mngmt',
                'titre' => 'Game'
            ]);
        } else {
            return $this->twig->render('Admin/game_admin.html.twig', [
                'data' => $data,
                'topGame'=> $topGame,
                'headtitle' => 'Game Mngmt',
                'titre' => 'Game'
            ]);
        }
    }




    public function deleteGame($id)
    {


        $deletedGame = new VideoGameManager();
        $deletedGame->delete($id);
        header('location: ../displayForm');
    }


    /**
     * The function will test whether the different input from the video game admin Form are OK.
     * If so, it will return an empty $error[].
     * If not, it will fill the array with error message
     * @param $gameName
     * @param $price
     * @param $gameDesc
     * @param $top
     * @param $category
     * @return array
     */
    private function verifyfield($gameName, $price, $gameDesc, $category, $pic)
    {


        if (empty($gameName) || strlen($gameName) > 45) {
            $error['game_name'] = "Please check the title field. Something's wrong.";
        }
        if (empty($price) || $price > 100) {
            $error['game_price'] = "Please check the title price. Price cannot exceed EUR 100";
        }
        if (strlen($gameDesc) < 50 || strlen($gameDesc) > 1000) {
            $error['game_desc'] = "Please check the game description. Something's wrong";
        }
        if (!in_array($category, [1, 2])) {
            $error['category'] = "Something's wrong with the top game fdsfdsfdsfds. Please try again.";
        }
        if (count($pic) != 3) {
            $error['picture']="You can only upload 3 pictures. And only 3 pictures. No less, no more.";
        }
        if (empty($error)) {
            $error['success'] = 'perfect! The database was uploaded properly.';
        }


        return $error;
    }

    private function moveFile($tmpFile, $newTmp, $newFile)
    {
        move_uploaded_file($tmpFile, $newTmp);
        $mimeType = mime_content_type($newTmp);
        if ($mimeType == 'image/png' || $mimeType == 'image/jpg' || $mimeType == 'image/jpeg') {
            rename($newTmp, $newFile);
        } else {
            unlink($newTmp);
        }
    }

    /**
     * Display item informations specified by $id
     *
     * @param int $id
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function showData()
    {
        $VideoGameManager = new VideoGameManager();
        $data = $VideoGameManager->selectGames();
        return $data;
    }

    public function showTopGame()
    {
        $videoGameManager = new VideoGameManager();
        $topGame= $videoGameManager->topGame();
        return $topGame;
    }

    public function modifyGameText($id)
    {
        $gameUpdate=$_POST;
        $modifiedGame = new videoGameManager();
        $modifiedGame->updateGameText($gameUpdate, $id);
        header('location: ../displayForm');
    }

    public function modifyGamePic($id)
    {

        if (!empty($_FILES['picture'])) {
            $accepted_extension = ['jpg', 'png', 'jpeg'];

            for ($i = 0; $i <= 2; $i++) {
                $uploaded_extension[$i] = strtolower(substr(strrchr($_FILES['picture']['name'][$i], "."), 1));
                $fileTitle = $_FILES['picture']['name'][$i];

                if (in_array($uploaded_extension[$i], $accepted_extension)
                    || !empty($_FILES['picture']['tmp_name'][$i])
                    || $_FILES['picture']['size'][$i] < 4000000) {
                    $newFileName = "assets/images/games/$fileTitle";
                    $newTmp = "assets/images/games/tmp/$fileTitle";

                    $this->moveFile($_FILES['picture']['tmp_name'][$i], $newTmp, $newFileName);
                } else {
                    $errorFile = 'Echec du chargement du fichier. Veuillez rééssayer.';
                }
            }
        }

                $gamePics=[
                'pic1' => 'assets/images/games/' . $_FILES['picture']['name'][0],
                'pic2' => 'assets/images/games/' . $_FILES['picture']['name'][1],
                'pic3' => 'assets/images/games/' . $_FILES['picture']['name'][2]
                ];
                $modifiedGame = new videoGameManager();
                $modifiedGame->updateGamePics($gamePics, $id);
                header('location: ../displayForm');
    }
    public function show(int $id)
    {
        $videoManager = new VideoGameManager();
        $gameId = $videoManager->selectOneById($id);

        return $this->twig->render('Games/gameId.html.twig', ['gameId' => $gameId]);
    }
}
