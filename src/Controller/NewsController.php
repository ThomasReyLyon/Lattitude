<?php

namespace App\Controller;

use App\Model\NewsManager; // Les use se font TOUJOURS en début de fichier

class NewsController extends AbstractController
{
    /**
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function index()
    {
        $errors=[];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Création d'un tableau associatif local pour plus
            // de simplicité d'utilisation
            $news = [
                'titre' => $_POST['titre'],
                'description' => $_POST['description'],
                'image' => '/assets/images/News/' . $_FILES['image']['name']

            ];
            // Vérification des champs rentrés dans le formulaire
            // checkUser est une méthode privée définie plus bas
            $errors = $this->checkNews($news);
            if (empty($errors)) { // S'il n'y a pas d'erreurs
                // Pas d'erreurs, on insère l'utilisateur
                // dans la base de données en utilisant le manager que nous
                // allons créer à l'étape suivante
                // La liaison de la DERNIERE ETAPE se fera ici
                $newsManager = new NewsManager();
                $newsManager->insert($news);
            }
        }
        /* The block below checks whether the extension of the uploaded files is OK, and if yes,
         then checks also the size. If all good, it will initiate the movefile function to upload the file.
        */
        if (!empty($_FILES)) {
            $accepted_extension = ['jpg', 'png', 'jpeg'];
            foreach ($_FILES as $key => $values) {
                $uploaded_extension[$key] = strtolower(substr(strrchr($values['name'], "."), 1));
                $fileTitle = $values['name'];

                if (in_array($uploaded_extension[$key], $accepted_extension)
                    || !empty($values['tmp_name'])
                    || $values['size'] < 10000000) {
                    $newFileName = "assets/images/News/$fileTitle";
                    $newTmp = "assets/images/News/tmp/$fileTitle";

                    $this->moveFile($values['tmp_name'], $newTmp, $newFileName);
                } else {
                    $errorFile = 'Echec du chargement du fichier. Veuillez rééssayer.';
                }
            }
        }
        $newsManager = new NewsManager();
        $news = $newsManager->selectAll();
        return $this->twig->render('Admin/news_admin.html.twig', ['news' =>$news, 'errors' => $errors]);
    }

    /**
     * Return an errors array. Check each fields.
     * @param array $news informations d'un utilisateur
     * @return array
     * tableau associatif des erreurs
     */
    private function checkNews($news)
    {
        $errors = [];
        if (!isset($news['titre']) || empty($news['titre'])) {
            $errors['titre'] = "Le titre n'est pas valide";
        }
        if (!isset($news['description']) || empty($news['description'])) {
            $errors['description'] = "La description n'est pas valide";
        }
        if (!isset($news['image']) || empty($news['image'])) {
            $errors['image'] = "L'image n'est pas valide";
        }
        return $errors;
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
    public function deleteNews($id)
    {

        $deletedNews= new NewsManager();
        $deletedNews->delete($id);
        header('location:../index');
    }
    public function editNews($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $edit = $_GET;
            $editNews = new NewsManager();
            $editNews->edit($edit, $id);
            header('Location: ../index');
        }
    }
}
