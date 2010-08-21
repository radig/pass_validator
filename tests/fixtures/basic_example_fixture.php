<?php
class BasicExampleFixture extends CakeTestFixture {
	var $name = 'BasicExample';

	var $fields = array(
		'id' => array('type'=>'integer', 'null' => false, 'key' => 'primary'),
		'name' => array('type'=>'string', 'null' => false, 'default' => NULL),
		'password' => array('type'=>'string', 'null' => false),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);

	var $records = array(
		array(
			'id'  => 1,
			'name'  => 'joao',
			'password'  => '40bd001563085fc35165329ea1ff5c5ecbdbbeef'
		),
		array(
			'id'  => 2,
			'name'  => 'tonho',
			'password'  => 'sem_hash'
		)
	);
}
