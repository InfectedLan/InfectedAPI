<?php
require_once 'PHPUnit/Autoload.php';
use PHPUnit\Framework\TestCase;

class DummyTest extends TestCase {
    public function testIsWorking() {
	$this->assertEquals(1, 1);
    }
}
?>