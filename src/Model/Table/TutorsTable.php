<?php
namespace Tutor\Model\Table;

use Cake\Core\Configure;
use Cake\I18n\Time;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Tutor\Model\Entity\Tutor;
use Tutor\Model\Table\AppTable;

/**
 * Tutors Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Users
 * @property \Cake\ORM\Association\HasMany $Experiences
 * @property \Cake\ORM\Association\HasMany $Requests
 * @property \Cake\ORM\Association\HasMany $Tutorquestions
 * @property \Cake\ORM\Association\BelongsToMany $Disciplins
 * @property \Cake\ORM\Association\BelongsToMany $Students
 */
class TutorsTable extends AppTable
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('tutors');
        $this->displayField('user_id');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('Experiences', [
            'foreignKey' => 'tutor_id',
            'className' => 'Tutor.Experiences'
        ]);

        $this->_setAppRelations(Configure::read('tutor_plugin.relations'));
    }

    /**
     * Default validation rules.AppTable
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->add('id', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('id', 'create');

        $validator
            ->requirePresence('cpf', 'create')
            ->notEmpty('cpf')
            ->add('cpf', [
                'length' => [
                    'rule' => ['maxLength', 11],
                    'message' => 'cpf need to be up to 11 characters long',
                ]
            ])
            ->add('cpf', 'valid', ['rule' => 'numeric'])
            ->add('cpf', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);
        
        $validator
            ->add('user_id', 'valid', ['rule' => 'numeric']);

        $validator
            ->add('is_active', 'valid', ['rule' => 'boolean']);

        $validator
            ->requirePresence('description', 'create')
            ->notEmpty('description');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique(['user_id']));
        return $this->_setExtraBuildRules($rules, Configure::read('tutor_plugin.rules'));
    }

    /**
     * handles the form data for edit a tutor.
     *
     * @param array $data raw form data;
     * @param int $tutorId Student id
     * @return Student;
     */
    public function editHandler($postData, $tutorId)
    {
        $tutor = $this->get($tutorId, [
            'contain' => ['Experiences']
        ]);
        $userData = $postData['user'];
        unset($postData['user']);
        $experienceIds = [];
        $experiences = [];
        if (isset($postData['experiences'])) {
            foreach ($postData['experiences'] as $experience) {
                if (isset($experience['id'])) {
                    $experienceIds[] = $experience['id'];
                }
            }
            foreach ($tutor->experiences as $experience) {
                if (!in_array($experience->id, $experienceIds) || empty($experienceIds)) {
                    $experience->is_active = 0;
                    $experience->dirty('is_active', true);
                    $experiences[] = $experience;
                }
            }
        }

        $tutor = $this->patchEntity($tutor, $postData);
        $user = $this->Users->get($userData['id'], ['contain' => ['Personalinformations']]);
        $this->Users->patchEntity($user, $userData);
        $tutor->user = $user;

        if (!empty($experiences)) {
            foreach ($experiences as $experience) {
                $tutor->experiences[] = $experience;
            }
        }
        return $tutor;
    }

    /**
     * importLinkedinExperience method
     */
    public function importLinkedinExperience($tutorId, $linkedinPositions)
    {
        unset($linkedinPositions['@total']);
        $experiencesData = [];
        $index = 0;
        foreach ($linkedinPositions as $position) {
            $position['startDate'] = new Time($position['start-date']['year'] . '-' . $position['start-date']['month'] . '-1');
            unset($position['start-date']);
            if (!$this->Experiences->verifyExists($tutorId, $position)) {
                $current = 1;
                if (isset($position['endDate'])) {
                    $current = 0;
                    $endDate = new Time($position['end-date']['year'] . '-' . $position['end-date']['month'] . '-1');
                    $experiencesData[$index]['end'] = $endDate;
                }
                $experiencesData[$index] = ['tutor_id' => $tutorId,
                    'position' => $position['title'],
                    'company' => $position['company']['name'],
                    'current' => $current,
                    'start' => $position['startDate'],
                ];
            }
            $index++;
        }
        if (!empty($experiencesData)) {
            return $this->Experiences->saveExperiences($experiencesData);
        }
        //Utilizando o plugin do linkedin do WS
        // unset($linkedinPositions['_total']);
        // $experiencesData = [];
        // $index = 0;
        // foreach ($linkedinPositions['values'] as $position) {
        //     $position['startDate'] = new Time($position['startDate']['year'] . '-' . $position['startDate']['month'] . '-1');
        //     if (!$this->Experiences->verifyExists($tutorId, $position)) {
        //         $current = 1;
        //         if (isset($position['endDate'])) {
        //             $current = 0;
        //             $endDate = new Time($position['endDate']['year'] . '-' . $position['endDate']['month'] . '-1');
        //             $experiencesData[$index]['end'] = $endDate;
        //         }
        //         $experiencesData[$index] = ['tutor_id' => $tutorId,
        //             'position' => $position['title'],
        //             'company' => $position['company']['name'],
        //             'current' => $current,
        //             'start' => $position['startDate'],
        //         ];
        //     }
        //     $index++;
        // }
        // if (!empty($experiencesData)) {
        //     return $this->Experiences->saveExperiences($experiencesData);
        // }
        return true;
    }
}
