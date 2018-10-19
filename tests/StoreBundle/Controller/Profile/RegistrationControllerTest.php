<?php

namespace StoreBundle\Controller\Profile;

use StoreBundle\Form\DataTransformer\Base64Transformer;
use StoreBundle\Service\Uploader\DocumentStorage;
use Tests\DataFixtures\Document\UserDocumentFixture;
use Tests\DataFixtures\Document\UserDocumentTypeFixture;
use Tests\DataFixtures\Logistic\CdekCityFixture;
use Tests\StoreBundle\StoreWebTestCase;

class RegistrationControllerTest extends StoreWebTestCase
{
  public function testRegisterJuridical()
  {
    $this->markTestSkipped('Регистрация без js не работает');
    $this->appendFixture(new CdekCityFixture());
    $ekb = $this->getReference('city-ekb');
    $crawler = $this->getClient()->request('GET', '/registration');
    $this->assertSame(200, $this->getClient()->getResponse()->getStatusCode());

    $form = $crawler->filter('form[name=registerjuridical]')->form();

    $data = [
      'registerjuridical' => [
        'company' => [
          'name' => 'OOO Pora u KonblTa',
          'inn' => '772747550002',
          'kpp' => '632401001',
          'ogrn' => '1036303288955',
          'country' => 'Poccu9',
          'address' => 'Lenina',
          'director' => 'UBaHoB U.U.',
          'phone' => '+7 (999) 999-99-99',
          'email' => 'e@example.com',
        ],
        'firstname' => 'John',
        'lastname' => 'Doe',
        'middlename' => 'Junior',
        'phone' => '+7 (999) 999-99-99',
        'email' => 'e@example.com',
        'city' => $ekb->getId(),
        'plainPassword' => [
          'first' => '12345',
          'second' => '12345',
        ],
        'tos' => true,
        'roles' => 'ROLE_JURIDICAL',
      ]
    ];

    $this->getClient()->submit($form, $data);
    $this->assertSame(301, $this->getClient()->getResponse()->getStatusCode(), 'Регистрация не прошла успешно');

    $newUser = $this->getEntityManager()->getRepository('StoreBundle:User\User')->findOneBy([], ['id' => 'DESC']);
    $this->assertNotNull($newUser, 'Новый пользователь не появлился в БД');
    $this->assertSame('John', $newUser->getFirstName(), 'Пользователь в БД не тот, которого мы создали');
    $this->assertSame('Doe', $newUser->getLastName(), 'Пользователь в БД не тот, которого мы создали');
    $this->assertTrue($newUser->hasRole('ROLE_JURIDICAL'), 'Роль не проставилась');
  }

  public function testRegisterIndividual()
  {
    $this->markTestSkipped('Регистрация без js не работает');
    $this->appendFixture(new CdekCityFixture());
    $ekb = $this->getReference('city-ekb');
    $crawler = $this->getClient()->request('GET', '/registration');
    $this->assertSame(200, $this->getClient()->getResponse()->getStatusCode());

    $form = $crawler->filter('form[name=registerIndividual]')->form();

    $data = [
      'registerIndividual' => [
        'firstname' => 'John',
        'lastname' => 'Doe',
        'middlename' => 'Junior',
        'phone' => '+7 (999) 999-99-99',
        'email' => 'e@example.com',
        'city' => $ekb->getId(),
        'plainPassword' => [
          'first' => '12345',
          'second' => '12345',
        ],
        'tos' => true,
        'roles' => 'ROLE_INDIVIDUAL',
        'contragent' => true,
      ]
    ];

    $this->getClient()->submit($form, $data);
    $this->assertSame(301, $this->getClient()->getResponse()->getStatusCode(), 'Регистрация не прошла успешно');

    $newUser = $this->getEntityManager()->getRepository('StoreBundle:User\User')->findOneBy([], ['id' => 'DESC']);
    $this->assertNotNull($newUser, 'Новый пользователь не появлился в БД');
    $this->assertSame('John', $newUser->getFirstName(), 'Пользователь в БД не тот, которого мы создали');
    $this->assertSame('Doe', $newUser->getLastName(), 'Пользователь в БД не тот, которого мы создали');
    $this->assertTrue($newUser->hasRole('ROLE_INDIVIDUAL'), 'Роль не проставилась');
  }

  public function testRegisterEnterpreneur()
  {
    $this->markTestSkipped('Регистрация без js не работает');
    $this->appendFixture(new CdekCityFixture());
    $ekb = $this->getReference('city-ekb');
    $crawler = $this->getClient()->request('GET', '/registration');
    $this->assertSame(200, $this->getClient()->getResponse()->getStatusCode());

    $form = $crawler->filter('form[name=registerEnterpreneur]')->form();

    $data = [
      'registerEnterpreneur' => [
        'firstname' => 'John',
        'lastname' => 'Doe',
        'middlename' => 'Junior',
        'phone' => '+7 (999) 999-99-99',
        'email' => 'e@example.com',
        'city' => $ekb->getId(),
        'plainPassword' => [
          'first' => '12345',
          'second' => '12345',
        ],
        'tos' => true,
        'roles' => 'ROLE_ENTREPRENEUR',
      ]
    ];

    $this->getClient()->submit($form, $data);
    $this->assertSame(301, $this->getClient()->getResponse()->getStatusCode(), 'Регистрация не прошла успешно');

    $newUser = $this->getEntityManager()->getRepository('StoreBundle:User\User')->findOneBy([], ['id' => 'DESC']);
    $this->assertNotNull($newUser, 'Новый пользователь не появлился в БД');
    $this->assertSame('John', $newUser->getFirstName(), 'Пользователь в БД не тот, которого мы создали');
    $this->assertSame('Doe', $newUser->getLastName(), 'Пользователь в БД не тот, которого мы создали');
    $this->assertTrue($newUser->hasRole('ROLE_ENTREPRENEUR'), 'Роль не проставилась');
  }

  /**
   * @dataProvider registrationFormDataProvider
   */
  public function testXmlHttpRequest($data)
  {
    $this->appendFixture(new CdekCityFixture());
    $ekb = $this->getReference('city-ekb');
    $data['city'] = $ekb->getId();

    $this->getClient()->request('POST', '/api/user', [], [], [
      'HTTP_X-Requested-With' => 'XMLHttpRequest',
    ], json_encode($data));
    $this->assertSame(200, $this->getClient()->getResponse()->getStatusCode(), $this->getClient()->getResponse()->getContent());

    $lastUser = $this->getEntityManager()->getRepository('StoreBundle:User\User')->findOneBy([], ['id' => 'DESC']);
    $this->assertNotNull($lastUser);
    $this->assertSame('John', $lastUser->getFirstName());
    $this->assertTrue($lastUser->hasRole($data['roles'][0]));
  }

  public function testUploadRegisterDocument()
  {
    $this->appendFixture(new UserDocumentTypeFixture());
    $type = $this->getReference('userDocumentType-passport');
    $passport = $this->getResource('passport.doc');
    $file = (new Base64Transformer())->transform($passport);
    $data = [
      'file' => $file,
    ];

    $documentUploader = $this->getMockBuilder('StoreBundle\Service\Uploader\DocumentUploader')->disableOriginalConstructor()->getMock();
    $this->getClient(true)->getContainer()->set('store.document.uploader', $documentUploader);
    $this->getClient()->disableReboot();
    $documentUploader->expects($this->once())->method('upload');

    $this->getClient()->request('POST', sprintf('/api/user/upload/%s', $type->getId()), [], [], [], json_encode($data));
    $this->assertSame(200, $this->getClient()->getResponse()->getStatusCode());
    $session = $this->getClient()->getRequest()->getSession();
    $this->assertTrue($session->has('registration.file.uploads'), 'Документ не добавился в сессию');
    $this->assertCount(1, $session->get('registration.file.uploads'), 'Ожидали, что после загрузки одного документа только один документ и будет');
  }

  public function testConfirmRegistrationAfterUpload()
  {
    $this->appendFixture(new UserDocumentTypeFixture());
    $this->appendFixture(new UserDocumentFixture());
    $passport = $this->getReference('userDocument-passport');
    /*
     * Мочим аплоадер
     */
    $documentUploader = $this->getMockBuilder('StoreBundle\Service\Uploader\DocumentUploader')->disableOriginalConstructor()->getMock();
    $this->getClient(true)->getContainer()->set('store.document.uploader', $documentUploader);
    $this->getClient()->disableReboot();
    /*
     * Мочим DocumentStorage, чтобы брал тестовые файлы из тестовой директории
     */
    $documentStorage = new DocumentStorage($this->getResourceDir(), '', $this->getClient()->getContainer()->get('router'));
    $this->getClient()->getContainer()->set('store.document.storage', $documentStorage);
    /*
     * Добавлеям в сессиюю загруженный файл
     */
    $this->getClient()->getContainer()->get('session')->set('registration.file.uploads', [$passport->getUuid()]);

    /*
     * Продолжаем регистрацию
     */
    $this->testXmlHttpRequest(($this->registrationFormDataProvider())['ROLE_JURIDICAL'][0]);
    $lastUser = $this->getEntityManager()->getRepository('StoreBundle:User\User')->findOneBy([], ['id' => 'DESC']);

    $this->assertCount(1, $lastUser->getDocuments());
    $this->assertSame('Паспорт', ($lastUser->getDocuments()[0])->getName());
  }

  public function registrationFormDataProvider()
  {
    return [
      'ROLE_JURIDICAL' => [[
        'company' => [
          'name' => 'OOO Pora u KonblTa',
          'inn' => '772747550002',
          'kpp' => '632401001',
          'ogrn' => '1036303288955',
          'country' => 'Poccu9',
          'address' => 'Lenina',
          'director' => 'UBaHoB U.U.',
          'phone' => '+7 (999) 999-99-99',
          'email' => 'e@example.com',
        ],
        'firstname' => 'John',
        'lastname' => 'Doe',
        'middlename' => 'Junior',
        'phone' => '+7 (999) 999-99-99',
        'email' => 'e@example.com',
        'city' => 1,
        'plainPassword' => [
          'first' => '12345',
          'second' => '12345',
        ],
        'tos' => true,
        'roles' => ['ROLE_JURIDICAL'],
        'contragent' => true,
      ]],
      'ROLE_INDIVIDUAL' => [[
        'firstname' => 'John',
        'lastname' => 'Doe',
        'middlename' => 'Junior',
        'phone' => '+7 (999) 999-99-99',
        'email' => 'e@example.com',
        'city' => 1,
        'plainPassword' => [
          'first' => '12345',
          'second' => '12345',
        ],
        'tos' => true,
        'roles' => ['ROLE_INDIVIDUAL'],
        'contragent' => true,
      ]],
      'ROLE_ENTREPRENEUR' => [[
        'firstname' => 'John',
        'lastname' => 'Doe',
        'middlename' => 'Junior',
        'phone' => '+7 (999) 999-99-99',
        'email' => 'e@example.com',
        'city' => 1,
        'plainPassword' => [
          'first' => '12345',
          'second' => '12345',
        ],
        'tos' => true,
        'roles' => ['ROLE_ENTREPRENEUR'],
      ]],
    ];
  }
}