<?php
namespace Tutor\Test\TestCase\Controller;

use Cake\I18n\Time;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;
use Tutor\Controller\TutorsController;

/**
 * Tutor\Controller\TutorsController Test Case
 */
class TutorsControllerTest extends IntegrationTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.tutor.tutors',
        'plugin.tutor.experiences',
        'plugin.tutor.users',
    ];

    /**
     * additionProvider method
     *
     * @return array
     */
    public function sessionProvider()
    {
        $sessionAdmin = [
            'Auth' => [
                'User' => [
                    'id' => 5,
                    'usertype_id' => 100,
                    'username' => 'Admin',
                ]
            ]
        ];
        $sessionTutor = [
            'Auth' => [
                'User' => [
                    'id' => 2,
                    'usertype_id' => 4,
                    'username' => 'Tutor',
                ]
            ]
        ];
        return [[$sessionAdmin], [$sessionTutor]];
    }

    /**
     * Test index method
     * @dataProvider sessionProvider
     */
    public function testIndex($session)
    {
        $this->session($session);
        $this->configRequest([
            'headers' => ['Accept' => 'application/json']
        ]);
        $this->get('/tutor/tutors');
        if ($session['Auth']['User']['usertype_id'] == 4) {
            $this->assertResponseError();
        } else {
            $this->assertResponseOk();
            $respondeData = json_decode($this->_response->body());
            $count = count($respondeData->tutors);

            $Tutors = TableRegistry::get('Tutor.Tutors');
            $query = $Tutors->find('all')->contain(['Users']);
            $tutorsJson = json_encode(['tutors' => $query], JSON_PRETTY_PRINT);
            
            $this->assertEquals($count, $query->count());
            $this->assertEquals($tutorsJson, $this->_response->body());
        }
    }

    /**
     * Test view method
     * @dataProvider sessionProvider
     */
    public function testView($session)
    {
        $this->session($session);

        $this->configRequest([
            'headers' => ['Accept' => 'application/json']
        ]);
        $this->get('/tutor/tutors/1');

        $Tutors = TableRegistry::get('Tutor.Tutors');
        $query = $Tutors->find('all')->contain(['Users'])->first();
        $tutorsJson = json_encode(['tutor' => $query], JSON_PRETTY_PRINT);

        $this->assertResponseOk();
        $this->assertEquals($tutorsJson, $this->_response->body());
    }

    /**
     * Test add method
     * @dataProvider sessionProvider
     */
    public function testAdd($session)
    {
        $this->session($session);
        $this->configRequest([
            'headers' => ['Accept' => 'application/json']
        ]);

        $postData = [
            'cpf' => '11122233388',
            'description' => 'Tutor de teste que ainda nao esta no banco!',
            'user' => [
                'usertype_id' => 1,
                'gender_id' => 1,
                'first_name' => 'usuario',
                'last_name' => 'teste ',
                'email' => 'emailnovosemuso@root.com',
                'password' => 'qwe123',
            ],
            'experiences' => [
                0 => [
                    'tutor_id' => 1,
                    'company' => 'nome da empresa que nao existe no banco',
                    'position' => 'testador',
                    'start' => Time::now(),
                    'end' => Time::now(),
                ]
            ]
        ];
        $this->post('/tutor/tutors', $postData);

        if ($session['Auth']['User']['id'] == 2) {
            $this->assertResponseError();// erro because this id is already associated with a tutor record
        } else {
            $Tutors = TableRegistry::get('Tutor.Tutors');
            $this->assertResponseSuccess();

            $query = $Tutors->find()->where(['Tutors.cpf' => $postData['cpf']]);
            $this->assertEquals(1, $query->count());

            $postData = [
                'cpf' => '11122233399',
                'description' => 'Tutor de teste que ainda nao esta no banco!',
                'user_id' => 1,
            ];

            $this->configRequest([
                'headers' => ['Accept' => 'application/json']
            ]);

            $this->post('/tutor/tutors', $postData);
            $this->assertResponseSuccess();
            $Tutors = TableRegistry::get('Tutor.Tutors');
            $query = $Tutors->find()->where(['Tutors.cpf' => $postData['cpf']]);
            $this->assertEquals(1, $query->count());
        }
    }

    /**
     * Test edit method
     * @dataProvider sessionProvider
     * @return void
     */
    public function testEdit($session)
    {
        $this->session($session);
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
        $this->configRequest([
            'headers' => ['Accept' => 'application/json']
        ]);

        $this->put('/tutor/tutors/1', $postData);
        $this->assertResponseSuccess();
        $Tutors = TableRegistry::get('Tutor.Tutors');
        $query = $Tutors->find()->where(['Tutors.cpf' => $postData['cpf']]);
        $Experiences = TableRegistry::get('Tutor.Experiences');
        $query = $Experiences->find()->where(['Experiences.company' => $postData['experiences'][0]['company']])->first();
        $this->assertNotNull($query, 'new inserted experience shouldnt be null');
        $query = $Experiences->find()->where(['Experiences.id' => $postData['experiences'][1]['id']])->first();
        $this->assertNotNull($query, 'edited experience shouldnt be null');
        $this->assertEquals($postData['experiences'][1]['company'], $query->company, 'validate equals edited company');
    }

    /**
     * Test delete method
     * @dataProvider sessionProvider
     * @return void
     */
    public function testDelete($session)
    {
        $this->session($session);
        $this->configRequest([
            'headers' => ['Accept' => 'application/json']
        ]);
        $Tutors = TableRegistry::get('Tutor.Tutors');
        $tutor = $Tutors->find('all', ['withDeleted'])
            ->where(['Tutors.id' => 1])
            ->first();
        $this->assertTrue($tutor->is_active, 'is_active should be true');

        $this->delete('/tutor/tutors/1');
        $this->assertResponseSuccess();
         
        $tutor = $Tutors->find('all', ['withDeleted'])
            ->where(['Tutors.id' => 1])
            ->first();
        $this->assertNotEmpty($tutor, 'message');
        $this->assertFalse($tutor->is_active, 'is_active should be false');
    }
}
