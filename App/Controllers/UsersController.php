<?php

namespace App\Controllers;

use \App\Models\User;
use \Framework\View;

/**
 * Users controller
 */
class UsersController extends \Framework\Controller
{

    /**
     * User list
     *
     * @return void
     */
    public function indexAction()
    {
        $model=new User;
        $users=$model->findAll();

        View::renderTemplate(
            'Users/index.twig.html', [
            'users'=>$users,
            ]
        );
    }

    /**
     * Show user model
     *
     * @param  integer $id model id
     * @return void
     */
    public function showAction($id)
    {
        $model=new User;
        $model=$model->findByPk($id);

        View::renderTemplate(
            'Users/show.twig.html', [
            'model'=>$model,
            ]
        );
    }

    /**
     * Creates new record
     *
     * @return void
     */
    public function createAction()
    {
        $model=new User;
        $errors=[];
        if ($_POST && $_POST['user']) {
            if ($model->create($_POST['user'])) {
                $this->redirect('/users');
            }
        }

        View::renderTemplate(
            'Users/create.twig.html', [
            'model'=>$model,
            'errors'=>$model->getErrors(),
            ]
        );
    }

    /**
     * Updates user model
     *
     * @param  integer $id model id
     * @return void
     */
    public function updateAction($id)
    {
        $model = new User;
        $model = $model->findByPk($id);

        $errors=[];
        if ($_POST && $_POST['user']) {
            if ($model->update($_POST['user'])) {
                $this->redirect('/users');
            }
        }

        View::renderTemplate(
            'Users/create.twig.html', [
            'model'=>$model,
            'errors'=>$model->getErrors(),
            ]
        );
    }

    /**
     * Destroys user model
     *
     * @param  integer $id model id
     * @return void
     */
    public function destroyAction($id)
    {
        $model=new User;
        $model=$model->findByPk($id);
        $model->delete();
        $this->redirect('/users');
    }
}

