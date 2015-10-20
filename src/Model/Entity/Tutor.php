<?php
namespace Tutor\Model\Entity;

use Cake\ORM\Entity;

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
    protected $_hidden = ['created', 'user_id', 'is_active', 'modified'];
}
