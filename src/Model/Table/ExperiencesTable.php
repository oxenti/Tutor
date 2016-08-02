<?php
namespace Tutor\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Tutor\Model\Entity\Experience;

/**
 * Experiences Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Tutors
 */
class ExperiencesTable extends AppTable
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

        $this->table('experiences');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Tutors', [
            'foreignKey' => 'tutor_id',
            'joinType' => 'INNER',
            'className' => 'Tutor.Tutors'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->add('id', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('id', 'create')
            ->add('id', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->requirePresence('company', 'create')
            ->notEmpty('company')
            ->add('company', [
                'length' => [
                    'rule' => ['maxLength', 45],
                    'message' => 'company need to be up to 45 characters long',
                ]
            ]);

        $validator
            ->add('tutor_id', 'valid', ['rule' => 'numeric'])
            ->requirePresence('tutor_id', 'create');

        $validator
            ->requirePresence('position', 'create')
            ->notEmpty('position')
            ->add('position', [
                'length' => [
                    'rule' => ['maxLength', 45],
                    'message' => 'position need to be up to 45 characters long',
                ]
            ]);

        $validator
            ->add('start', 'valid', ['rule' => 'date'])
            ->requirePresence('start', 'create')
            ->notEmpty('start');

        $validator
            ->add('end', 'valid', ['rule' => 'date'])
            ->notEmpty('end');

        $validator
            ->add('current', 'valid', ['rule' => 'boolean'])
            ->allowEmpty('current');

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
        $rules->add($rules->existsIn(['tutor_id'], 'Tutors'));
        return $rules;
    }

    /**
     * verifyExists
     * Verify wheter a tutor already had the given experience recorded
     * @param int $tutorId tutor id
     * @param array $experienceData experience info
     * @return bool
     */
    public function verifyExists($tutorId, $exprienceData)
    {
        $experience = $this->find('all', ['conditions' => ['tutor_id' => $tutorId,
            'position' => $exprienceData['title'],
            'start' => $exprienceData['startDate'],
            'company' => $exprienceData['company']['name']]])->first();
        if (empty($experience)) {
            return false;
        }
        return true;
    }

    /**
     *
     */
    public function saveExperiences($experiencesData)
    {
        $saved = 0;
        $experiences = $this->newEntities($experiencesData);
        foreach ($experiences as $experience) {
            if ($this->save($experience)) {
                $saved++;
            }
        }
        if ($saved == count($experiences)) {
            return true;
        } else {
            return false;
        }
    }
}
