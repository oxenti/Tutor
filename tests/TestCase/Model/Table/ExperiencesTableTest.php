<?php
namespace Tutor\Test\TestCase\Model\Table;

use Cake\I18n\Time;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Tutor\Model\Table\ExperiencesTable;

/**
 * Tutor\Model\Table\ExperiencesTable Test Case
 */
class ExperiencesTableTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.tutor.experiences',
        'plugin.tutor.tutors',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Experiences') ? [] : ['className' => 'Tutor\Model\Table\ExperiencesTable'];
        $this->Experiences = TableRegistry::get('Experiences', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Experiences);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->assertNotEmpty($this->Experiences);
        $this->assertNotEmpty($this->Experiences->Associations(), 'message');
        $this->assertNotEmpty($this->Experiences->Behaviors());
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
                'tutor_id' => 1,
                'company' => 'empresa de teste',
                'position' => 'testador',
                'start' => Time::now(),
                'end' => Time::now(),
            ],
            [
                'is_active' => 1,
            ],
            [
                'cpf' => '',
                'description' => '',
                'is_active' => 1,
            ],
            [
                'tutor_id' => 'asd',
                'company' => '123123123123123123123123123123123123123123123123123123',
                'position' => '123123123123123123123123123123123123123123123123123123',
                'start' => 123,
                'end' => 123,
                'is_active' => 'asd',
            ],
            [
                'tutor_id' => 99999,
                'company' => 'empresa de teste',
                'position' => 'testador',
                'start' => Time::now(),
                'end' => Time::now(),
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
        $case1 = $this->Experiences->validator()->errors($cases[0]);
        $this->assertEmpty($case1, 'Case1 é valido mas retornou erro:' . json_encode($case1));

        $case = $this->Experiences->validator()->errors($cases[1]);
        $this->assertNotEmpty($case, 'required não retornou erro');
        $expected = [
            'company' => ['_required' => 'This field is required'],
            'tutor_id' => ['_required' => 'This field is required'],
            'position' => ['_required' => 'This field is required'],
            'start' => ['_required' => 'This field is required'],
            'end' => ['_required' => 'This field is required'],
        ];
        $this->assertEquals($expected, $case, '');

        $case = $this->Experiences->validator()->errors($cases[2]);
        $this->assertNotEmpty($case, 'not empty não retornou erro');
        $expected = [
            'company' => ['_required' => 'This field is required'],
            'tutor_id' => ['_required' => 'This field is required'],
            'position' => ['_required' => 'This field is required'],
            'start' => ['_required' => 'This field is required'],
            'end' => ['_required' => 'This field is required'],
        ];
        $this->assertEquals($expected, $case, '');

        $case = $this->Experiences->validator()->errors($cases[3]);
        $this->assertNotEmpty($case, 'not empty não retornou erro');
        $expected = [
            'is_active' => ['valid' => 'The provided value is invalid'],
            'company' => ['length' => 'company need to be up to 45 characters long'],
            'tutor_id' => ['valid' => 'The provided value is invalid'],
            'position' => ['length' => 'position need to be up to 45 characters long'],
            'start' => ['valid' => 'The provided value is invalid'],
            'end' => ['valid' => 'The provided value is invalid'],
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
        $case1 = $this->Experiences->newEntity($cases[0]);
        $result = $this->Experiences->save($case1);
        $this->assertInstanceOf('Tutor\Model\Entity\Experience', $result, 'Caso valido não gerou obejeto esperado');

        $case1 = $this->Experiences->newEntity($cases[4]);
        $result = $this->Experiences->save($case1);
        $errors = $case1->errors();
        $expected = ['_existsIn' => 'This value does not exist'];
        $this->assertEquals($expected, $errors['tutor_id'], 'não foi retornado erro para tutor_id invalido');
    }
}
