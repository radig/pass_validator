<?php
// App::import('Core', 'Security');
App::uses('Security', 'Utility');
// App::uses('PassValidator', 'Behavior');

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

class User extends CakeTestModel {
	public $name = 'User';

	public $actsAs = array(
		'PassValidator.PassValidator' => array(
			'haveConfirm' => false,
			'isSecurityPassword' => false,
			'preConditions' => array(
				'User.type' => 'admin',
			)
		)
	);
}

class PassValidatorTest extends CakeTestCase {

	public $name = 'PassValidator';

	public $fixtures = array(
		'Plugin.PassValidator.BasicExample',
		'Plugin.PassValidator.WithoutConfirmation',
		'Plugin.PassValidator.OptionalPassword',
		'Plugin.PassValidator.PasswordPolicy',
		'Plugin.PassValidator.PasswordPreCondition',
		'Plugin.PassValidator.User'
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

		// $result = $this->BasicExample->find('first',
		// 	array('conditions' => array('id' => '1000'))
		// );
	
		$expected = array(
			'BasicExample' => array(
				'id' => 1000,
				'name' => 'joao',
				'password' => 'senha'
			)
		);
		
		// $this->assertEqual($result, $expected);

		unset($this->BasicExample);
	}

	/**
	 * 
	 */
	// public function testFindAll()
	// {
	// 	$this->BasicExample =& ClassRegistry::init('BasicExample');

	// 	$result = $this->BasicExample->find('all');
		
	// 	$expected = array(
	// 		array(
	// 			'BasicExample' => array(
	// 				'id'  => 1000,
	// 				'name'  => 'joao',
	// 				'password'  => 'senha'
	// 			),
	// 		),
	// 		array(
	// 			'BasicExample' => array(
	// 				'id'  => 1001,
	// 				'name'  => 'tonho',
	// 				'password'  => 'sem_hash'
	// 			)
	// 		)
	// 	);
		
	// 	$this->assertEqual($result, $expected);

	// 	unset($this->BasicExample);
	// }

	// public function testSaveOne()
	// {
	// 	$this->BasicExample =& ClassRegistry::init('BasicExample');

	// 	$data = array(
	// 		'name' => 'teste',
	// 		'password_confirm' => '1234',
	// 		'password' => '1234'
	// 	);
		
	// 	$result = $this->BasicExample->save($data);
		
	// 	$expected = true;

	// 	$this->assertEqual($result, $expected);

	// 	$emptyErrors = $this->BasicExample->validationErrors;
	// 	$expected = array();

	// 	$this->assertEqual($emptyErrors, $expected);

	// 	unset($this->BasicExample);
	// }

	// public function testUpdate()
	// {
	// 	$this->BasicExample =& ClassRegistry::init('BasicExample');

	// 	$data = array(
	// 		'id' => 1000,
	// 		'name' => 'teste'
	// 	);

	// 	$result = $this->BasicExample->save($data);

	// 	$expected = true;

	// 	$this->assertEqual($result, $expected);

	// 	$emptyErrors = $this->BasicExample->validationErrors;
	// 	$expected = array();

	// 	$this->assertEqual($emptyErrors, $expected);

	// 	unset($this->BasicExample);
	// }

	/*************
	* 
	*/
	// public function testEmptyPassword()
	// {
	// 	$this->BasicExample =& ClassRegistry::init('BasicExample');

	// 	$data = array(
	// 		'id' => null,
	// 		'name' => 'teste',
	// 		'password_confirm' => '',
	// 		'password' => ''
	// 	);

	// 	$result = $this->BasicExample->save($data);

	// 	$expected = false;

	// 	$this->assertEqual($result, $expected);

	// 	$errors = $this->BasicExample->validationErrors;

	// 	$expected = array(
	// 		'password' => __('Campo obrigatório'),
	// 		'password_confirm' => __('Campo obrigatório')
	// 	);

	// 	$this->assertEqual($errors, $expected);

	// 	unset($this->BasicExample);
	// }

	/*************
	*
	*/
	// public function testBogusConfirm()
	// {
	// 	$this->BasicExample =& ClassRegistry::init('BasicExample');

	// 	$data = array(
	// 		'name' => 'teste',
	// 		'password_confirm' => '1234',
	// 		'password' => '4321'
	// 	);

	// 	$result = $this->BasicExample->save($data);

	// 	$expected = false;

	// 	$this->assertEqual($result, $expected);

	// 	$errors = $this->BasicExample->validationErrors;
		
	// 	$expected = array(
	// 		'password_confirm' => __('A confirmação não bate com a senha')
	// 	);

	// 	$this->assertEqual($errors, $expected);

	// 	unset($this->BasicExample);
	// }

	/*************
	* Funcionando
	*/
	public function testShortPassConfirm()
	{
		$this->BasicExample =& ClassRegistry::init('BasicExample');

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
			'password_confirm' => __('Insira pelo menos 4 caracteres')
		);

		$this->assertEqual($errors, $expected);

		unset($this->BasicExample);
	}

	/*************
	* Funcionando
	*/
	public function testEmptyConfirmation()
	{
		$this->BasicExample =& ClassRegistry::init('BasicExample');

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

	// public function testWithoutConfirmation()
	// {
	// 	$this->WithoutConfirmation =& ClassRegistry::init('WithoutConfirmation');

	// 	$data = array(
	// 		'name' => 'teste',
	// 		'password' => '1234'
	// 	);

	// 	$result = $this->WithoutConfirmation->save($data);

	// 	$expected = true;

	// 	$this->assertEqual($result, $expected);

	// 	$emptyErrors = $this->WithoutConfirmation->validationErrors;
	// 	$expected = array();

	// 	$this->assertEqual($emptyErrors, $expected);

	// 	unset($this->WithoutConfirmation);
	// }

	/*************
	* Funcionando
	*/
	public function testWithoutConfirmationLength()
	{
		$this->WithoutConfirmation =& ClassRegistry::init('WithoutConfirmation');

		$data = array(
			'id' => null,
			'name' => 'teste',
			'password' => '123'
		);

		$result = $this->WithoutConfirmation->save($data);

		$expected = false;

		$this->assertEqual($result, $expected);

		$emptyErrors = $this->WithoutConfirmation->validationErrors;
		$expected = array(
			'password' => __('Insira pelo menos 4 caracteres')
		);

		$this->assertEqual($emptyErrors, $expected);

		unset($this->WithoutConfirmation);
	}

	// public function testOptionalPassword()
	// {
	// 	$this->OptionalPassword =& ClassRegistry::init('OptionalPassword');

	// 	$data = array(
	// 		'name' => 'teste'
	// 	);

	// 	$result = $this->OptionalPassword->save($data);

	// 	$expected = true;

	// 	$this->assertEqual($result, $expected);

	// 	$emptyErrors = $this->OptionalPassword->validationErrors;
	// 	$expected = array();

	// 	$this->assertEqual($emptyErrors, $expected);

	// 	unset($this->OptionalPassword);
	// }
	
	/*************
	* Funcionando
	*/
	public function testOnlyAlphaPasswordConfirm()
	{
		$this->PasswordPolicy =& ClassRegistry::init('PasswordPolicy');
		
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
			'password_confirm' => __('A senha deve ter pelo menos 2 caracteres especiais')
		);
		
		$this->assertEqual($emptyErrors, $expected);
		
		unset($this->PasswordPolicy);
	}
	
	/*************
	* Funcionando
	*/
	public function testOnlyNumberPasswordConfirm()
	{
		$this->PasswordPolicy =& ClassRegistry::init('PasswordPolicy');
		
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
			'password_confirm' => __('A senha deve ter pelo menos 2 caracteres especiais')
		);
		
		$this->assertEqual($emptyErrors, $expected);
		
		unset($this->PasswordPolicy);
	}
	
	/*************
	* Funcionando
	*/
	public function testNumAlphaPasswordConfirm()
	{
		$this->PasswordPolicy =& ClassRegistry::init('PasswordPolicy');
		
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
			'password_confirm' => __('A senha deve ter pelo menos 2 caracteres especiais')
		);
		
		$this->assertEqual($emptyErrors, $expected);
		
		unset($this->PasswordPolicy);
	}


	// public function testPreConditionSkip()
	// {
	// 	$this->User =& ClassRegistry::init('User');

	// 	$data = array(
	// 		'name' => 'teste'
	// 	);

	// 	$result = $this->User->save($data);

	// 	$expected = true;

	// 	$this->assertEqual($result, $expected);

	// 	$emptyErrors = $this->User->validationErrors;
	// 	$expected = array();

	// 	$this->assertEqual($emptyErrors, $expected);

	// 	unset($this->User);
	// }

	
	// public function testPreConditionRun()
	// {
	// 	$this->User =& ClassRegistry::init('User');

	// 	$data = array(
	// 		'name' => 'teste',
	// 		'type' => 'admin'
	// 	);

	// 	$result = $this->User->save($data);

	// 	$expected = false;

	// 	$this->assertEqual($result, $expected);

	// 	$emptyErrors = $this->User->validationErrors;
		
	// 	$expected = array(
	// 		'password' => __('Campo obrigatório')
	// 	);

	// 	$this->assertEqual($emptyErrors, $expected);

	// 	unset($this->User);
	// }
	
	
	// public function testPreConditionComboFail()
	// {
	// 	$User =& ClassRegistry::init('PasswordPreCondition');
		
	// 	$data = array(
	// 		'name' => 'teste',
	// 		'type' => 'admin'
	// 	);

	// 	$result = $User->save($data);

	// 	$expected = false;

	// 	$this->assertEqual($result, $expected);

	// 	$emptyErrors = $User->validationErrors;
		
	// 	$expected = array(
	// 		'password' => __('Campo obrigatório')
	// 	);
		
	// 	$this->assertEqual($emptyErrors, $expected);
	// }
	
	// public function testPreConditionComboRun()
	// {
	// 	$User =& ClassRegistry::init('PasswordPreCondition');

	// 	$data = array(
	// 		'name' => 'teste',
	// 		'type' => 'admin',
	// 		'password' => '123123123'
	// 	);

	// 	$result = $User->save($data);
	// 	$expected = true;
		
	// 	$this->assertEqual($result, $expected);
	// }
}
