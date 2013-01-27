<?php
class BasicExample extends CakeTestModel {
	public $name = 'BasicExample';

	public $actsAs = array('PassValidator.PassValidator');
}

class WithoutConfirmation extends CakeTestModel {
	public $name = 'WithoutConfirmation';

	public $actsAs = array(
		'PassValidator.PassValidator' => array(
			'haveConfirm' => false
		)
	);
}

class OptionalPassword extends CakeTestModel {
	public $name = 'OptionalPassword';

	public $actsAs = array(
		'PassValidator.PassValidator' => array(
			'allowEmpty' => true
		)
	);
}

class PasswordPolicy extends CakeTestModel {
	public $name = 'PasswordPolicy';

	public $actsAs = array(
		'PassValidator.PassValidator' => array(
			'haveConfirm' => true,
			'minLength'  => 5,
			'minAlpha' => 2,
			'minNumbers' => 2,
			'minSpecialChars' => 2
		)
	);
}

class PasswordPreCondition extends CakeTestModel {
	public $name = 'PasswordPreCondition';

	public $actsAs = array(
		'PassValidator.PassValidator' => array(
			'allowEmpty' => false,
			'haveConfirm' => false,
			'preConditions' => array(
				'PasswordPreCondition.type' => array(
					'or' => array('admin', 'moderator')
				)
			)
		)
	);
}

class PasswordPreCondition2 extends CakeTestModel {
	public $name = 'PasswordPreCondition2';

	public $useTable = 'password_pre_conditions';

	public $actsAs = array(
		'PassValidator.PassValidator' => array(
			'allowEmpty' => false,
			'haveConfirm' => false,
			'preConditions' => array(
				'or' => array(
					'PasswordPreCondition2.type' => 'admin',
					'PasswordPreCondition2.type' => 'moderator'
				)
			)
		)
	);
}

class User extends CakeTestModel {
	public $name = 'User';

	public $actsAs = array(
		'PassValidator.PassValidator' => array(
			'haveConfirm' => false,
			'preConditions' => array(
				'User.type' => 'admin',
			)
		)
	);
}

class PassValidatorTest extends CakeTestCase
{
	public $name = 'PassValidator';

	public $plugin = 'PassValidator';

	public $fixtures = array(
		'plugin.PassValidator.BasicExample',
		'plugin.PassValidator.WithoutConfirmation',
		'plugin.PassValidator.OptionalPassword',
		'plugin.PassValidator.PasswordPolicy',
		'plugin.PassValidator.PasswordPreCondition',
		'plugin.PassValidator.User'
	);

	public function setUp() {
		parent::setUp();

		$this->BasicExample = ClassRegistry::init('BasicExample');
	}

	public function tearDown() {
		parent::tearDown();

		unset($this->BasicExample);
		ClassRegistry::flush();
	}

	public function testFindOne() {
		$result = $this->BasicExample->find('first',
			array('conditions' => array('id' => '1000'))
		);
		$expected = array(
			'BasicExample' => array(
				'id' => '1000',
				'name' => 'joao',
				'password' => 'senha'
			)
		);

		$this->assertEquals($result, $expected);

		unset($this->BasicExample);
	}

	public function testFindAll() {
		$result = $this->BasicExample->find('all');

		$expected = array(
			array(
				'BasicExample' => array(
					'id'  => 1000,
					'name'  => 'joao',
					'password'  => 'senha'
				),
			),
			array(
				'BasicExample' => array(
					'id'  => 1001,
					'name'  => 'tonho',
					'password'  => 'sem_hash'
				)
			)
		);

		$this->assertEqual($result, $expected);

		unset($this->BasicExample);
	}

	public function testSaveOne() {
		$data = array(
			'name' => 'teste',
			'password_confirm' => '1234',
			'password' => '1234'
		);

		$result = $this->BasicExample->save($data);

		$expected = array('BasicExample' => $data);
		$expected['BasicExample']['id'] = $this->BasicExample->id;

		$this->assertEqual($result, $expected);

		$emptyErrors = $this->BasicExample->validationErrors;
		$expected = array();

		$this->assertEqual($emptyErrors, $expected);

		unset($this->BasicExample);
	}

	public function testUpdate() {
		$data = array(
			'id' => 1000,
			'name' => 'teste'
		);

		$result = $this->BasicExample->save($data);

		$expected = array('BasicExample' => $data);
		$expected['BasicExample']['id'] = $this->BasicExample->id;

		$this->assertEqual($result, $expected);

		$emptyErrors = $this->BasicExample->validationErrors;
		$expected = array();
		$this->assertEqual($emptyErrors, $expected);

		// Atualização com senha
		$data = array(
			'id' => 1000,
			'name' => 'teste',
			'password' => '12'
		);

		$result = $this->BasicExample->save($data);
		$this->assertFalse($result);

		$errors = $this->BasicExample->validationErrors;
		$expected = array(
			'password' => __('Insira pelo menos 4 caracteres'),
			'password_confirm' => __('Campo obrigatório')
		);

		$this->assertEqual($errors, $expected);

		unset($this->BasicExample);
	}

	public function testEmptyPassword() {
		$data = array(
			'id' => null,
			'name' => 'teste',
			'password_confirm' => '',
			'password' => ''
		);

		$result = $this->BasicExample->save($data);

		$expected = false;

		$this->assertEqual($result, $expected);

		$errors = $this->BasicExample->validationErrors;

		$expected = array(
			'password' => __('Campo obrigatório'),
			'password_confirm' => __('Campo obrigatório')
		);

		$this->assertEqual($errors, $expected);

		unset($this->BasicExample);
	}

	public function testBogusConfirm() {
		$data = array(
			'name' => 'teste',
			'password_confirm' => '1234',
			'password' => '4321'
		);

		$result = $this->BasicExample->save($data);

		$expected = false;

		$this->assertEqual($result, $expected);

		$errors = $this->BasicExample->validationErrors;

		$expected = array(
			'password_confirm' => __('A confirmação não bate com a senha')
		);

		$this->assertEqual($errors, $expected);

		unset($this->BasicExample);
	}

	public function testShortPassConfirm() {
		$data = array(
			'id' => null,
			'name' => 'teste',
			'password_confirm' => '123',
			'password' => '123'
		);

		$result = $this->BasicExample->save($data);

		$expected = false;

		$this->assertEqual($result, $expected);

		$errors = $this->BasicExample->validationErrors;

		$expected = array(
			'password' => __('Insira pelo menos 4 caracteres')
		);

		$this->assertEqual($errors, $expected);

		unset($this->BasicExample);
	}

	public function testEmptyConfirmation() {
		$data = array(
			'id' => null,
			'name' => 'teste',
			'password' => '1234'
		);

		$result = $this->BasicExample->save($data);

		$expected = false;

		$this->assertEqual($result, $expected);

		$errors = $this->BasicExample->validationErrors;

		$expected = array(
			'password_confirm' => __('Campo obrigatório')
		);

		$this->assertEqual($errors, $expected);

		unset($this->BasicExample);
	}

	public function testWithoutConfirmation() {
		$this->WithoutConfirmation = ClassRegistry::init('WithoutConfirmation');

		$data = array(
			'name' => 'teste',
			'password' => '1234'
		);

		$result = $this->WithoutConfirmation->save($data);

		$expected = array('WithoutConfirmation' => $data);
		$expected['WithoutConfirmation']['id'] = $this->WithoutConfirmation->id;

		$this->assertEqual($result, $expected);

		$emptyErrors = $this->WithoutConfirmation->validationErrors;
		$expected = array();

		$this->assertEqual($emptyErrors, $expected);

		unset($this->WithoutConfirmation);
	}

	public function testWithoutConfirmationLength() {
		$this->WithoutConfirmation = ClassRegistry::init('WithoutConfirmation');

		$data = array(
			'id' => null,
			'name' => 'teste',
			'password' => '123'
		);

		$result = $this->WithoutConfirmation->save($data);

		$this->assertFalse($result);

		$errors = $this->WithoutConfirmation->validationErrors;
		$expected = array(
			'password' => __('Insira pelo menos 4 caracteres')
		);

		$this->assertEqual($errors, $expected);

		unset($this->WithoutConfirmation);
	}

	public function testOptionalPassword() {
		$this->OptionalPassword = ClassRegistry::init('OptionalPassword');

		$data = array(
			'name' => 'teste'
		);

		$result = $this->OptionalPassword->save($data);

		$expected = array('OptionalPassword' => $data);
		$expected['OptionalPassword']['id'] = $this->OptionalPassword->id;

		$this->assertEqual($result, $expected);

		$emptyErrors = $this->OptionalPassword->validationErrors;
		$expected = array();

		$this->assertEqual($emptyErrors, $expected);

		unset($this->OptionalPassword);
	}

	public function testOnlyAlphaPasswordConfirm() {
		$this->PasswordPolicy = ClassRegistry::init('PasswordPolicy');

		$data = array(
			'name' => 'only alpha',
			'password_confirm' => 'foobar',
			'password' => 'foobar'
		);

		$result = $this->PasswordPolicy->save($data);

		$expected = false;

		$this->assertEqual($result, $expected);

		$emptyErrors = $this->PasswordPolicy->validationErrors;
		$expected = array(
			'password' => __('A senha deve ter pelo menos 2 caracteres especiais')
		);

		$this->assertEqual($emptyErrors, $expected);

		unset($this->PasswordPolicy);
	}

	public function testOnlyNumberPasswordConfirm() {
		$this->PasswordPolicy = ClassRegistry::init('PasswordPolicy');

		$data = array(
			'name' => 'only numbers',
			'password_confirm' => '123456',
			'password' => '123456'
		);

		$result = $this->PasswordPolicy->save($data);

		$expected = false;

		$this->assertEqual($result, $expected);

		$emptyErrors = $this->PasswordPolicy->validationErrors;
		$expected = array(
			'password' => __('A senha deve ter pelo menos 2 caracteres especiais')
		);

		$this->assertEqual($emptyErrors, $expected);

		unset($this->PasswordPolicy);
	}

	public function testNumAlphaPasswordConfirm() {
		$this->PasswordPolicy = ClassRegistry::init('PasswordPolicy');

		$data = array(
			'name' => 'numbers and alpha',
			'password_confirm' => '123foo',
			'password' => '123foo'
		);

		$result = $this->PasswordPolicy->save($data);

		$expected = false;

		$this->assertEqual($result, $expected);

		$emptyErrors = $this->PasswordPolicy->validationErrors;
		$expected = array(
			'password' => __('A senha deve ter pelo menos 2 caracteres especiais')
		);

		$this->assertEqual($emptyErrors, $expected);

		unset($this->PasswordPolicy);
	}


	public function testPreConditionSkip() {
		$this->User = ClassRegistry::init('User');

		$data = array(
			'name' => 'teste'
		);

		$result = $this->User->save($data);

		$expected = array('User' => $data);
		$expected['User']['id'] = $this->User->id;

		$this->assertEqual($result, $expected);

		$emptyErrors = $this->User->validationErrors;
		$expected = array();

		$this->assertEqual($emptyErrors, $expected);

		unset($this->User);
	}


	public function testPreConditionRun() {
		$this->User = ClassRegistry::init('User');

		$data = array(
			'name' => 'teste',
			'type' => 'admin'
		);

		$result = $this->User->save($data);

		$expected = false;

		$this->assertEqual($result, $expected);

		$emptyErrors = $this->User->validationErrors;

		$expected = array(
			'password' => __('Campo obrigatório')
		);

		$this->assertEqual($emptyErrors, $expected);

		unset($this->User);
	}

	public function testPreConditionComboFail() {
		$this->User = ClassRegistry::init('PasswordPreCondition');

		$data = array(
			'name' => 'teste',
			'type' => 'admin'
		);

		$result = $this->User->save($data);

		$expected = false;

		$this->assertEqual($result, $expected);

		$errors = $this->User->validationErrors;

		$expected = array(
			'password' => __('Campo obrigatório')
		);

		$this->assertEqual($errors, $expected);
	}

	public function testPreConditionComboIgnorePassword() {
		$this->User = ClassRegistry::init('PasswordPreCondition2');

		$data = array(
			'name' => 'teste',
			'type' => 'adm'
		);

		$result = $this->User->save($data);
		$expected = array('PasswordPreCondition2' => $data);
		$expected['PasswordPreCondition2']['id'] = $this->User->id;

		$this->assertEqual($result, $expected);

		$emptyErrors = empty($this->User->validationErrors);

		$this->assertTrue($emptyErrors);
	}

	public function testPreConditionComboRun() {
		$this->User = ClassRegistry::init('PasswordPreCondition');

		$data = array(
			'name' => 'teste',
			'type' => 'admin',
			'password' => '123123123'
		);

		$result = $this->User->save($data);

		$expected = array('PasswordPreCondition' => $data);
		$expected['PasswordPreCondition']['id'] = $this->User->id;

		$this->assertEqual($result, $expected);
	}

	public function testPreCondition2ComboRun() {
		$this->User = ClassRegistry::init('PasswordPreCondition2');

		$data = array(
			'name' => 'teste',
			'type' => 'admin',
			'password' => '123123123'
		);

		$result = $this->User->save($data);

		$expected = array('PasswordPreCondition2' => $data);
		$expected['PasswordPreCondition2']['id'] = $this->User->id;

		$this->assertEqual($result, $expected);
	}
}
