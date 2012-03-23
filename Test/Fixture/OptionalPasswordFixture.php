<?php
class OptionalPasswordFixture extends CakeTestFixture {
	var $name = 'OptionalPassword';

	var $fields = array(
		'id' => array('type'=>'integer', 'key' => 'primary'),
		'name' => array('type'=>'string', 'null' => false, 'default' => NULL),
		'password' => array('type'=>'string', 'null' => true),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);

	var $records = array(
		array(
			'id'  => 1000,
			'name'  => 'joao',
			'password'  => 'senha'
		),
		array(
			'id'  => 1001,
			'name'  => 'tonho',
			'password'  => null
		)
	);
}
