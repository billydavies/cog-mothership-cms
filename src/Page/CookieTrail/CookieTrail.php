<?php

namespace Message\Mothership\CMS\Page\CookieTrail;

use Message\Cog\ValueObject\Collection;
use Message\Mothership\CMS\Page\Page;

class CookieTrail extends Collection
{
	public function __construct()
	{
		// Needs to be of type Page
		$this->addValidator(function($page) {
			if($page instanceof Page) {
				return true;
			}

			return false;
		});

		$this->setSort(function($a, $b) {
			return $a->depth >= $b->depth;
		});
	}
}