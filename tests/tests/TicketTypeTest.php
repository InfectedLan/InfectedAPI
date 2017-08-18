<?php
use PHPUnit\Framework\TestCase;

require_once 'handlers/tickettypehandler.php';
require_once 'handlers/tickethandler.php';
require_once 'handlers/paymenthandler.php';
require_once 'objects/tickettype.php';
require_once 'database.php';

/* 
 * TicketTypeTest
 *
 * Responsible for testing TicketTypeHandler and the TicketType object. Not much, but important to keep working
 *
 */
class TicketTypeTest extends TestCase {
	public function test() {
		$this->ticketTypeSanityCheck();
		$this->cleanup();
	}

	private function ticketTypeSanityCheck() {
		//We expect there to be two different types of tickets, and we expect to be able to fetch it
		//This tests both the object, and the default data from deployment
		$types = TicketTypeHandler::getTicketTypes();
		$this->assertEquals(2, count($types));

		$this->assertEquals(1, $types[0]->getId());
		$this->assertEquals("participant", $types[0]->getName());
		$this->assertEquals(350, $types[0]->getPrice());
		$this->assertEquals(true, $types[0]->isRefundable());

		$this->assertEquals(2, $types[1]->getId());
		$this->assertEquals("free", $types[1]->getName());
		$this->assertEquals(0, $types[1]->getPrice());
		$this->assertEquals(false, $types[1]->isRefundable());

		//Check discount, so create a ticket for a test user

		$user = UserHandler::getUser(1);
		$payment = PaymentHandler::createPayment($user, $types[0], 1, 350, "foo");

		$this->assertFalse($types[0]->isUserEligibleForDiscount($user));
		$this->assertEquals(350, $types[0]->getPriceByUser($user));
		$this->assertEquals(680, $types[0]->getPriceByUser($user, 2));

		$ticket = TicketHandler::createTicket($user, $types[0], $payment);

		$this->assertTrue($types[0]->isUserEligibleForDiscount($user));
		$this->assertEquals(330, $types[0]->getPriceByUser($user));
		$this->assertEquals(660, $types[0]->getPriceByUser($user, 2));

		//Check that GetTicketType works
		$type = TicketTypeHandler::getTicketType($types[0]->getId());
		$this->assertEquals($types[0], $type);
		$type = TicketTypeHandler::getTicketType($types[1]->getId());
		$this->assertEquals($types[1], $type);


		Database::cleanup();
	}

	private function cleanup() {

	}
}
?>