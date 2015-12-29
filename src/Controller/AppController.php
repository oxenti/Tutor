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

    /**
     * getProfileInfo method.
     * @param int $userId user id
     * @return Tutor
     */
    public function getProfileInfo($userId)
    {
        $tutor = $this->Tutors->findByUserId($userId)->select(['id'])->first();
        return $tutor->id;
    }
}
