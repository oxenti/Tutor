<?php
namespace Tutor\Controller;

use Cake\Event\Event;
use Cake\Network\Exception\MethodNotAllowedException;
use Cake\Network\Exception\NotFoundException;
use Cake\Network\Exception\UnauthorizedException;
use Tutor\Controller\AppController;

/**
 * Tutors Controller
 *
 * @property \Tutor\Model\Table\TutorsTable $Tutors
 */
class TutorsController extends AppController
{
    /**
     * Basic authorize function
     */
    public function isAuthorized($user)
    {
        if ($user['usertype_id'] === 100 || $this->request->action === 'view') {
            return true;
        } elseif ($user['usertype_id'] === 4) {
            if (isset($this->request->params['pass'][0])) {
                return ($this->request->params['pass'][0] == $this->getProfileInfo($user['id']));
            }
            if ($this->request->action === 'index') {
                throw new UnauthorizedException('You dont have access to this action ');
                return false;
            }
            return true;
        } else {
            throw new UnauthorizedException('You dont have access to this action ');
            return false;
        }
        parent::isAuthorized($user);
    }
    
    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $finder = !isset($this->request->query['finder'])?'All': $this->request->query['finder'];
        $this->paginate = [
           'finder' => $finder,
           'contain' => ['Users', 'Users.Personalinformations'],
           'order' => ['Personalinformations.first_name'],
        ];
        $this->set('tutors', $this->paginate($this->Tutors));
        $this->set('_serialize', ['tutors']);
    }

    /**
     * View method
     *
     * @param string|null $tutorId Tutor id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($tutorId = null)
    {
        $tutor = $this->Tutors->get($tutorId, [
           'contain' => ['Users', 'Users.Personalinformations', 'Experiences'],
        ]);
        if (is_null($tutor)) {
            throw new NotFoundException(__('The tutor could not be finded'));
        }
        $this->set('tutor', $tutor);
        $this->set('_serialize', ['tutor']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record could not be saved.
     */
    public function add()
    {
        $tutor = $this->Tutors->newEntity();
        if ($this->request->is('post')) {
            $this->request->data['user_id'] = $this->Auth->user('id');
            $user = $this->Tutors->Users->get($this->Auth->user('id'));
            $this->Tutors->Users->patchEntity($user, $this->request->data['user']);
            unset($this->request->data['user']);
            $tutor = $this->Tutors->patchEntity($tutor, $this->request->data);
            $tutor->user = $user;
            if ($this->Tutors->save($tutor)) {
                $message = 'The tutor has been saved.';
                $this->set([
                    'success' => true,
                    'message' => $message,
                    '_serialize' => ['success', 'message']
                ]);
            } else {
                throw new NotFoundException('The tutor could not be saved. Please, try again.');
            }
        }
    }

    /**
     * Edit method
     * @param string|null $tutorId Tutor id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($tutorId = null)
    {
        if ($this->request->is(['patch', 'post', 'put'])) {
            $this->request->data['user']['id'] = $this->Auth->user('id');
            $tutor = $this->Tutors->editHandler($this->request->data, $tutorId);
            if (!$this->Tutors->save($tutor)) {
                throw new NotFoundException('The tutor could not be saved. Please, try again.');
            }
            $message = 'The tutor has been saved.';
            $this->set([
                'success' => true,
                'message' => $message,
                '_serialize' => ['success', 'message']
            ]);
        }
    }

    /**
     * Delete method
     *
     * @param string|null $tutorId Tutor id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($tutorId = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $tutor = $this->Tutors->get($tutorId);
        if ($this->Tutors->delete($tutor)) {
            $message = 'The tutor has been deleted.';
            $this->set([
                'success' => true,
                'message' => $message,
                '_serialize' => ['success', 'message']
            ]);
        } else {
            throw new NotFoundException('The tutor could not be deleted. Please, try again.');
        }
    }


    /**
     * import_experiences_linkedin
     * make the request to linkedin api to get the historic of prositions and
     * handle the data to add to experiences of the tutor.
     */
    public function import_experiences_linkedin()
    {
        $this->request->allowMethod(['post']);
        if ($this->request->data) {
            $userId = $this->Auth->user('id');
            $tutorId = $this->getProfile($userId);
            // caso a requisição seja feita utilizando o mesmo plugin do ws em versoes futuras
            // $token = $this->request->data['usersocialdata']['linkedin_token'];
            // $linkedinData = $this->Linkedin->linkedinget('v1/people/~/positions:(title,company,start-date,end-date)', $token);

            if ($this->Tutors->importLinkedinExperience($tutorId, $linkedinData)) {
                $this->set([
                    'success' => true,
                        'data' => [
                            'message' => __('Experiences imported from linkedin with success')
                        ],
                        '_serialize' => ['success', 'data']
                    ]);
            } else {
                throw new NotFoundException('The Experiences could not be imported. Please, try again.');
            }
        }
    }
}
