<?php
App::import('Core', 'Security');
App::import('Behavior', 'PassValidator');

class BasicExample extends CakeTestModel {
	public $name = 'BasicExample';

	public $actsAs = array('PassValidator.PassValidator');
}

class WithoutConfirmation extends CakeTestModel {
	public $name = 'WithoutConfirmation';

	public $actsAs = array(
		'PassValidator.PassValidator' => array(
			'haveConfirm' => false,
			'isSecurityPassword' => false
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

class PassValidatorTest extends CakeTestCase {

	public $name = 'PassValidator';

	public $fixtures = array(
		'plugin.pass_validator.basic_example',
		'plugin.pass_validator.without_confirmation',
		'plugin.pass_validator.optional_password'
	);

	public function startTest()
	{
	}

	public function endTest()
	{
	}

	/**
	 *
	 */
	public function testFindOne()
	{
		$this->BasicExample =& ClassRegistry::init('BasicExample');

		$result = $this->BasicExample->find('first',
			array('conditions' => array('id' => '1'))
		);
		
		$expected = array(
			'BasicExample' => array(
				'id' => 1,
				'name' => 'joao',
				'password' => '40bd001563085fc35165329ea1ff5c5ecbdbbeef'
			)
		);
		
		$this->assertEqual($result, $expected);

		unset($this->BasicExample);
	}

	/**
	 * 
	 */
	public function testFindAll()
	{
		$this->BasicExample =& ClassRegistry::init('BasicExample');

		$result = $this->BasicExample->find('all');

		$expected = array(
			array(
				'BasicExample' => array(
					'id'  => 1,
					'name'  => 'joao',
					'password'  => '40bd001563085fc35165329ea1ff5c5ecbdbbeef'
				),
			),
			array(
				'BasicExample' => array(
					'id'  => 2,
					'name'  => 'tonho',
					'password'  => 'sem_hash'
				)
			)
		);
		
		$this->assertEqual($result, $expected);

		unset($this->BasicExample);
	}

	public function testSaveOne()
	{
		$this->BasicExample =& ClassRegistry::init('BasicExample');

		$data = array(
			'name' => 'teste',
			'password_confirm' => '1234',
			'password' => Security::hash('1234', null, true)
		);
		
		$result =  $this->BasicExample->save($data);
		
		$expected = true;

		$this->assertEqual($result, $expected);

		$emptyErrors = $this->BasicExample->validationErrors;
		$expected = array();

		$this->assertEqual($emptyErrors, $expected);

		unset($this->BasicExample);
	}

	public function testUpdate()
	{
		$this->BasicExample =& ClassRegistry::init('BasicExample');

		$data = array(
			'id' => 1,
			'name' => 'teste'
		);

		$result =  $this->BasicExample->save($data);

		$expected = true;

		$this->assertEqual($result, $expected);

		$emptyErrors = $this->BasicExample->validationErrors;
		$expected = array();

		$this->assertEqual($emptyErrors, $expected);

		unset($this->BasicExample);
	}

	public function testEmptyPassword()
	{
		$this->BasicExample =& ClassRegistry::init('BasicExample');

		$data = array(
			'name' => 'teste',
			'password_confirm' => '',
			'password' => ''
		);

		$result =  $this->BasicExample->save($data);

		$expected = false;

		$this->assertEqual($result, $expected);

		$errors = $this->BasicExample->validationErrors;

		$expected = array(
			'password' => __('Campo obrigatório', true),
			'password_confirm' => __('Campo obrigatório', true)
		);

		$this->assertEqual($errors, $expected);

		unset($this->BasicExample);
	}

	public function testBogusConfirm()
	{
		$this->BasicExample =& ClassRegistry::init('BasicExample');

		$data = array(
			'name' => 'teste',
			'password_confirm' => '1234',
			'password' => Security::hash('4321', null, true)
		);

		$result =  $this->BasicExample->save($data);

		$expected = false;

		$this->assertEqual($result, $expected);

		$errors = $this->BasicExample->validationErrors;
		
		$expected = array(
			'password_confirm' => __('A confirmação não bate com a senha', true)
		);

		$this->assertEqual($errors, $expected);

		unset($this->BasicExample);
	}

	public function testShortPassConfirm()
	{
		$this->BasicExample =& ClassRegistry::init('BasicExample');

		$data = array(
			'id' => null,
			'name' => 'teste',
			'password_confirm' => '123',
			'password' => Security::hash('123', null, true)
		);

		$result =  $this->BasicExample->save($data);

		$expected = false;

		$this->assertEqual($result, $expected);

		$errors = $this->BasicExample->validationErrors;

		$expected = array(
			'password_confirm' => __('Insira pelo menos 4 caracteres', true)
		);

		$this->assertEqual($errors, $expected);

		unset($this->BasicExample);
	}

	public function testEmptyConfirmation()
	{
		$this->BasicExample =& ClassRegistry::init('BasicExample');

		$data = array(
			'id' => null,
			'name' => 'teste',
			'password' => Security::hash('1234', null, true)
		);

		$result =  $this->BasicExample->save($data);

		$expected = false;

		$this->assertEqual($result, $expected);

		$errors = $this->BasicExample->validationErrors;
		
		$expected = array(
			'password_confirm' => __('Campo obrigatório', true)
		);

		$this->assertEqual($errors, $expected);

		unset($this->BasicExample);
	}

	public function testWithoutConfirmation()
	{
		$this->WithoutConfirmation =& ClassRegistry::init('WithoutConfirmation');

		$data = array(
			'id' => null,
			'name' => 'teste',
			'password' => '1234'
		);

		$result =  $this->WithoutConfirmation->save($data);

		$expected = true;

		$this->assertEqual($result, $expected);

		$emptyErrors = $this->WithoutConfirmation->validationErrors;
		$expected = array();

		$this->assertEqual($emptyErrors, $expected);

		unset($this->WithoutConfirmation);
	}

	public function testWithoutConfirmationLength()
	{
		$this->WithoutConfirmation =& ClassRegistry::init('WithoutConfirmation');

		$data = array(
			'id' => null,
			'name' => 'teste',
			'password' => '123'
		);

		$result =  $this->WithoutConfirmation->save($data);

		$expected = false;

		$this->assertEqual($result, $expected);

		$emptyErrors = $this->WithoutConfirmation->validationErrors;
		$expected = array(
			'password' => __('Insira pelo menos 4 caracteres', true)
		);

		$this->assertEqual($emptyErrors, $expected);

		unset($this->WithoutConfirmation);
	}

	public function testOptionalPassword()
	{
		$this->OptionalPassword =& ClassRegistry::init('OptionalPassword');

		$data = array(
			'name' => 'teste'
		);

		$result =  $this->OptionalPassword->save($data);

		$expected = true;

		$this->assertEqual($result, $expected);

		$emptyErrors = $this->OptionalPassword->validationErrors;
		$expected = array();

		$this->assertEqual($emptyErrors, $expected);

		unset($this->OptionalPassword);
	}
}