<?php

namespace Tutor\Controller;

use App\Controller\AppController as BaseController;

class AppController extends BaseController
{
    /**
     * isAuthorized method.
     * @return void
     */
    public function isAuthorized($user)
    {
        parent::isAuthorized($user);
    }

    //criar funcao pra pegar o profile do cara reusavel
    /**
     * getProfileInfo method.
     * @param int $userId user id
     * @return Tutor
     */
    public function getProfileInfo($userId)
    {
        $tutor = $this->Tutors->findByUserId($userId)->first();
        return $tutor;
    }
}
