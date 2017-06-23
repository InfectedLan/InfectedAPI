<?php
require_once 'PHPUnit/Autoload.php';

class DummyTest extends TestCase {
    public function testIsWorking() {
	$this->assertEquals(1, 1);
    }
}
?>