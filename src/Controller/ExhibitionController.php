<?php

namespace App\Controller;

use App\Model\ExhibitionManager;
use App\Verify\VerifyFileUpload;

/**
 * Class ExhibitionController
 *
 */
class ExhibitionController extends AbstractController
{
    public function index()
    {
        //model
        $exhibitionManager = new ExhibitionManager();
        $exhibitions = $exhibitionManager->selectAll();

        //view
        return $this->twig->render('Exhibition/index.html.twig', ['exhibitions' => $exhibitions]);
    }

    public function edit($id)
    {
        $exhibitionManager = new ExhibitionManager();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $destination = 'assets/images/exhibition/';
            $files = new VerifyFileUpload($_FILES);
            $data = array_map('trim', $_POST);
            $errors = $this->formControl($data);
            $upload = $files->fileControl(true);

            if (empty($errors)) {
                if (!empty($upload['image'])) {
                    $data['image'] = $upload['image']['name'];
                    $files->uploadFile($upload['image']['tmp_name'], $destination, $upload['image']['name']);
                }
                $exhibitionManager->update($id, $data);
                header('Location: /admin/exhibition/?success=Données mises à jour avec succès !!');
            }
        }

        $exhibition = $exhibitionManager->selectOneById($id);

        return $this->twig->render('Update/exhibition.html.twig', [
            'exhibition' => $exhibition,
            'errors' => $errors ?? []
        ]);
    }

    private function formControl($data): array
    {
        $errors = [];

        if (strlen($data['title']) > 150) {
            $errors['length_title'] = 'Le titre doit contenir 150 caractères au maximum';
        }

        if (empty($data['title'])) {
            $errors['empty_title']  = 'Le titre de l\'article est requis';
        }

        if (empty($data['detail'])) {
            $errors['empty_detail'] = 'Le texte de l\'article est requis';
        }
        return $errors;
    }

    public function delete(int $id)
    {
        $exhibitionManager = new ExhibitionManager();
        $exhibitionManager->delete($id);
        header('Location:admin/exhibition');
    }
}
