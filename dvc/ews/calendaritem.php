<?php
/**
	David Bray
	BrayWorth Pty Ltd
	e. david@brayworth.com.au

	This work is licensed under a Creative Commons Attribution 4.0 International Public License.
		http://creativecommons.org/licenses/by/4.0/

	*/
NameSpace dvc\ews;

class calendaritem {
	var $start = '',
		$end = '',
		$location = '',
		$subject = '',
		$notes = '',
		$startUTC = '',
		$endUTC = '',
		$timelabel = '',
		$invitees = '',
		$IsAllDayEvent = FALSE,
		$id = '',
		$change = '',
		$changekey = ''
		;

	public function tostring() {
		return ( sprintf( '%s - %s : Subject: %s, Location: %s :: utc: %s - %s',
			$this->start,
			$this->end,
			$this->subject,
			$this->location,
			$this->startUTC,
			$this->endUTC));

	}

	public function clone() {
		$c = new calendaritem();

		$c->start = $this->start;
		$c->end = $this->end;
		$c->location = $this->location;
		$c->subject = $this->subject;
		$c->notes = $this->notes;
		$c->startUTC = $this->startUTC;
		$c->endUTC = $this->endUTC;
		$c->IsAllDayEvent = $this->IsAllDayEvent;

		return ( $c);

	}

}
