<?php
namespace Tutor\Model\Entity;

use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Routing\Router;

/**
 * Tutor Entity.
 *
 * @property int $id
 * @property int $user_id
 * @property \Tutor\Model\Entity\User $user
 * @property string $cpf
 * @property bool $is_active
 * @property string $description
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 * @property \Tutor\Model\Entity\Experience[] $experiences
 * @property \Tutor\Model\Entity\Request[] $requests
 * @property \Tutor\Model\Entity\Tutorquestion[] $tutorquestions
 * @property \Tutor\Model\Entity\Disciplin[] $disciplins
 * @property \Tutor\Model\Entity\Student[] $students
 */
class Tutor extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];

    protected $_hidden = ['cpf', 'created', 'is_active', 'modified'];

    protected $_virtual = ['name', 'avatar'];

    /**
     * virtual field full name
     */
    protected function _getName()
    {
        $Personalinformations = TableRegistry::get('User.Personalinformations');
        $userId = $this->_properties['user_id'];
        $personalinformations = $Personalinformations->find()
            ->join(['Users' => [
                'table' => 'users',
                'type' => 'INNER',
                'conditions' => 'Personalinformations.id = Users.personalinformation_id'
            ]
            ])
            ->select(['first_name', 'last_name'])
            ->where(['Users.id' => $userId])
            ->first();
        return (is_null($personalinformations))?' ':$personalinformations->first_name . ' ' . $personalinformations->last_name;
    }

    /**
     * virtual field avatar
     */
    protected function _getAvatar()
    {
        $Users = TableRegistry::get('Users');
        $userId = $this->_properties['user_id'];
        $user = $Users->find()
            ->select(['avatar_path'])
            ->where(['id' => $userId])
            ->first();
        $avatarPath = (is_null($user->avatar_path))? null : $user->avatar_path;
        // return $avatarPath;
        $path = '';
        if ($avatarPath) {
            if ($avatarPath) {
                $baseUrl = empty(Configure::read('static_base_url')) ? Router::url('/', true) : Configure::read('static_base_url');
                $path = (filter_var($avatarPath, FILTER_VALIDATE_URL)) ? $avatarPath : $baseUrl . $avatarPath;
            }
        }
        return $path;
    }
}
