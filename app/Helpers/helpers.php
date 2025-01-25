<?php
	function getOrdinalSuffix($number) {
		$suffixes = ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'];
		if (($number % 100) >= 11 && ($number % 100) <= 13) {
			return 'th';
		}
		return $suffixes[$number % 10];
	}
	
