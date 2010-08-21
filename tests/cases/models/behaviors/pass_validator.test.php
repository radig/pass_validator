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
			'haveConfirm' => false
		)
	);
}

class PassValidatorTest extends CakeTestCase {

	public $name = 'PassValidator';

	public $fixtures = array(
		'plugin.pass_validator.basic_example',
		'plugin.pass_validator.without_confirmation'
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
			'id' => null,
			'name' => 'teste',
			'password_confirm' => '1234',
			'password' => Security::hash('1234', null, true)
		);
		
		$result =  $this->BasicExample->save($data);
		
		$expected = false;

		$this->assertEqual($result, $expected);

		$emptyErrors = $this->BasicExample->validationErrors;
		$expected = array();

		$this->assertEqual($emptyErrors, $expected);

		unset($this->BasicExample);
	}

	public function testWithoutConfirmation()
	{
		$this->WithoutConfirmation =& ClassRegistry::init('WithoutConfirmation');

		unset($this->WithoutConfirmation);
	}
}