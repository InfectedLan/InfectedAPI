<?php
class Object {
	protected $id;
	
	/*
	 * Retuns the internal id for this object.
	 */
	public function getId() {
		return $this->id;
	}

	/*
	 * Compare is a function that allows you to easily compare this object with the specified one. 
	 * It returns true if the specified object is identical to this one.
	 */
	public function compare($object) {
		// Check that the specified object is an instance of this one.
		if ($object instanceof $this) {
			// Compare this objects by the internal id.
			if ($object == $this) {
				return true;
			}
		}

		return false;
	}
}
?>