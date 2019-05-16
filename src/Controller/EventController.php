<?php

namespace App\Controller;

use App\Model\EventManager; // Les use se font TOUJOURS en début de fichier

class EventController extends AbstractController
{



    public function index()
    {

        if (isset($_POST['submit'])) {
//Définition variables :
            $fileName = $_FILES['image']['name'];
            $fileTmpName = $_FILES['image']['tmp_name'];
            $fileSize = $_FILES['image']['size'];
            $fileError = $_FILES['image']['error'];
            $fileType = $_FILES['image']['type'];


//Type de formats acceptés :
            $fileExt = explode('.', $fileName); //récupération de l'extension
            $fileActualExt = strtolower(end($fileExt)); //minuscule
            $allowed = array('jpg', 'png', 'gif'); //liste formats autorisés

            if (in_array($fileActualExt, $allowed)) {
                if ($fileError === 0) {
                    if ($fileSize < 1000000) {
                        $fileNameNew = 'image' . "-" . uniqid() . "." . $fileActualExt;
                        $fileDestination = 'assets/images/event/' . $fileNameNew;
                        move_uploaded_file($fileTmpName, $fileDestination);

                        //   header("Location: index");
                    } else {
                        echo 'Your file is too heavy.';
                    }
                } else {
                    echo 'There was an error uploading your file';
                }
            } else {
                echo 'You cannot upload such a file';
            }
        }


        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Création d'un tableau associatif local pour plus
            // de simplicité d'utilisation

            $event = [
                'titre' => $_POST['titre'],
                'date' => $_POST['date'],
                'city' => $_POST['city'],
                'description' => $_POST['description'],
                'photo' => '/assets/images/event/' . $fileNameNew
            ];

            // Vérification des champs rentrés dans le formulaire
            // checkUser est une méthode privée définie plus bas
            $errors = $this->checkEvent($event);
            if (empty($errors)) {
                $eventManager = new EventManager();
                $eventManager->insert($event);
            }
        }


        $eventManager = new EventManager();
        $events = $eventManager->selectAll();

        return $this->twig->render('Admin/event_admin.html.twig', ['event' => $events, 'errors' => $errors]);
    }


    /**
     * @param $event
     * @return array
     */
    private function checkEvent($event)
    {
        $errors = [];
        if (!isset($event['titre']) || empty($event['titre'])) {
            $errors['titre'] = "Le titre n'est pas valide";
        }
        if (!isset($event['city']) || empty($event['city'])) {
            $errors['city'] = "La ville n'est pas valide";
        }
        if (!isset($event['description']) || empty($event['description'])) {
            $errors['description'] = "La description n'est pas valide";
        }
        if (!isset($event['photo']) || empty($event['photo'])) {
            $errors['photo'] = "L'image n'est pas valide";
        }
        return $errors;
    }

//-------------------------------------
// DELETE

    public function delete($id)
    {
        $deleteEvent = new EventManager();
        $deleteEvent -> delete($id);
        header('Location: ../../Event/index');
    }


//-------------------------------------
// MODIFY

    public function modifyEventText($id)
    {
        $eventManager = new EventManager();
        $events = $eventManager->selectOneById($id);

        if ($_SERVER['REQUEST_METHOD']=='POST') {
            $eventUpdate=$_POST;
            $modifiedEvent = new EventManager();
            $modifiedEvent -> updateEventText($eventUpdate, $id);
            header('location: ../index');
        } else {
            return $this->twig->render('Admin/event_admin.html.twig', ['event'=>$events]);
        }
    }


    public function showevent()
    {
        $eventManager = new EventManager();
        $events = $eventManager->selectAll();
        return $this->twig->render('Event/event_page.html.twig', ['event' => $events]);
    }
}
