<?php
namespace Tutor\Test\TestCase\Model\Table;

use Cake\I18n\Time;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Tutor\Model\Table\TutorsTable;

/**
 * Tutor\Model\Table\TutorsTable Test Case
 */
class TutorsTableTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.tutor.tutors',
        'plugin.tutor.users',
        'plugin.tutor.experiences',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Tutors') ? [] : ['className' => 'Tutor\Model\Table\TutorsTable'];
        $this->Tutors = TableRegistry::get('Tutors', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Tutors);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->assertNotEmpty($this->Tutors);
        $this->assertNotEmpty($this->Tutors->Associations(), 'message');
        $this->assertNotEmpty($this->Tutors->Behaviors());
    }

    /**
     * additionProvider method
     *
     * @return array
     */
    public function casesProvider()
    {
        $cases = [
            [
                'user_id' => 1,
                'cpf' => 11122233344,
                'description' => 'Novo tutor que ainda nao foi inserido no bando de teste',
            ],
            [
                'user_id' => 1,
                'is_active' => 1,
            ],
            [
                'cpf' => '',
                'description' => '',
                'is_active' => 1,
            ],
            [
                'user_id' => 'id invalido',
                'cpf' => 'Arrlzcxz',
                'description' => 12123,
                'is_active' => 'asd',
            ],
        ];
        return [[$cases]];
    }

    /**
     * Test validationDefault method
     * @dataProvider casesProvider
     * @return void
     */
    public function testValidationDefault($cases)
    {
        $case1 = $this->Tutors->validator()->errors($cases[0]);
        $this->assertEmpty($case1, 'Case1 é valido mas retornou erro:' . json_encode($case1));

        $case = $this->Tutors->validator()->errors($cases[1]);
        $this->assertNotEmpty($case, 'required não retornou erro');
        $expected = [
            'cpf' => ['_required' => 'This field is required'],
            'description' => ['_required' => 'This field is required'],
        ];
        $this->assertEquals($expected, $case, '');

        $case = $this->Tutors->validator()->errors($cases[2]);
        $this->assertNotEmpty($case, 'not empty não retornou erro');
        $expected = [
            'cpf' => ['_empty' => 'This field cannot be left empty'],
            'description' => ['_empty' => 'This field cannot be left empty'],
        ];
        $this->assertEquals($expected, $case, '');

        $case = $this->Tutors->validator()->errors($cases[3]);
        $this->assertNotEmpty($case, 'not empty não retornou erro');
        $expected = [
            'cpf' => ['valid' => 'The provided value is invalid'],
            'is_active' => ['valid' => 'The provided value is invalid'],
            'user_id' => ['valid' => 'The provided value is invalid'],
        ];
        $this->assertEquals($expected, $case, '');
    }

    /**
     * Test buildRules method
     * @dataProvider casesProvider
     * @return void
     */
    public function testBuildRules($cases)
    {
        $case1 = $this->Tutors->newEntity($cases[0]);
        $result = $this->Tutors->save($case1);
        $this->assertInstanceOf('Tutor\Model\Entity\Tutor', $result, 'Caso valido não gerou obejeto esperado');

        $cases[0]['cpf'] = 23123123;
        $case1 = $this->Tutors->newEntity($cases[0]);
        $result = $this->Tutors->save($case1);
        $errors = $case1->errors();
        $expected = ['_isUnique' => 'This value is already in use'];
        $this->assertEquals($expected, $errors['user_id'], 'não foi retornado erro para user_id invalido');

        $case2 = $this->Tutors->newEntity($cases[1]);
        $result = $this->Tutors->save($case2);
        $this->assertFalse($result, 'caso invalido n retornou false');
        $expected = ['_required' => 'This field is required' ];
        $errors = $case2->errors();
        $this->assertEquals($expected, $errors['cpf'], 'não foi retornado erro para usertype invalido');
        $this->assertEquals($expected, $errors['description'], 'não foi retornado erro para gender invalido');
    }

    public function testEditHandler()
    {
        $postData = [
            'cpf' => '99988877766',
            'description' => 'Tutor de teste que ainda nao esta no banco!',
            'experiences' => [
                0 => [
                    'tutor_id' => 1,
                    'company' => 'nome da empresa que nao existe no banco',
                    'position' => 'testador',
                    'start' => Time::now(),
                    'end' => Time::now(),
                ],
                1 => [
                    'id' => 1,
                    'tutor_id' => 1,
                    'company' => 'Trocando o nome da empresa',
                    'position' => 'testador',
                    'start' => Time::now(),
                    'end' => Time::now(),
                ],
            ]
        ];

        $tutor = $this->Tutors->editHandler($postData, 1);
        $this->assertInstanceOf('Tutor\Model\Entity\Tutor', $tutor, 'Caso valido não gerou obejeto esperado');
        $this->assertEmpty($tutor->errors(), 'error should be empty');
    }
}
