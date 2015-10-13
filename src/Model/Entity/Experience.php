<?php
namespace Tutor\Model\Entity;

use Cake\ORM\Entity;

/**
 * Experience Entity.
 *
 * @property int $id
 * @property int $tutor_id
 * @property \Tutor\Model\Entity\Tutor $tutor
 * @property string $company
 * @property string $position
 * @property \Cake\I18n\Time $start
 * @property \Cake\I18n\Time $end
 * @property int $current
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 * @property int $is_active
 */
class Experience extends Entity
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

    protected $_hidden = ['created', 'is_active', 'modified'];
}
