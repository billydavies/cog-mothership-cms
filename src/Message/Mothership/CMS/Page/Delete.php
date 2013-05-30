<?php

namespace Message\Mothership\CMS\Page;

use Message\Mothership\CMS\Page\Page;
use Message\Mothership\CMS\Event\Event;
use Message\Mothership\CMS\Page\Loader;
use Message\Mothership\CMS\Event\PageEvent;

use Message\Cog\Event\DispatcherInterface;
use Message\Cog\DB\Query as DBQuery;

/**
 * Decorator for deleting pages.
 *
 * @author Daniel Hannah <danny@message.co.uk>
 *
 * @todo implement deleted_by setting correctly once User cogule is complete.
 */
class Delete
{
	protected $_query;
	protected $_eventDispatcher;

	/**
	 * Constructor.
	 *
	 * @param DBQuery             $query           The database query instance to use
	 * @param DispatcherInterface $eventDispatcher The event dispatcher
	 */
	public function __construct(DBQuery $query, DispatcherInterface $eventDispatcher)
	{
		$this->_query           = $query;
		$this->_eventDispatcher = $eventDispatcher;
	}

	/**
	 * Delete a page by marking it as deleted in the database.
	 *
	 * @param  Page $page The page to be deleted
	 *
	 * @return Page       The page that was been deleted, with the "delete"
	 *                    authorship data set
	 *
	 * @throws \InvalidArgumentException If the page has child pages
	 */
	public function delete(Page $page)
	{
		// Check that the page doesn't have children pages
		$loader = new Loader('gb', $this->_query);

		// Throw an exception if it does
		if ($loader->getChildren($page)) {
			throw new \InvalidArgumentException(sprintf('Cannot delete page #%i because it has children pages', $page->id));
		}

		$page->authorship->delete(new \Datetime, 0);
		$result = $this->_query->run('
			UPDATE
				page
			SET
				deleted_at = :at?i,
				deleted_by = :by?i
			WHERE
				page_id = :id?i
		', array(
			'at' => $page->authorship->deletedAt()->getTimestamp(),
			'by' => $page->authorship->deletedBy(),
			'id' => $page->id,
		));

		$this->_eventDispatcher->dispatch(
			PageEvent::DELETE,
			new PageEvent($page)
		);

		return $page;
	}


	/**
	 * Restores a currently deleted page to its former self.
	 *
	 * @param Page page   The deleted page to be restored
	 *
	 * @return Page $page The restored page, with the "delete" authorship data
	 *                    cleared
	 */
	public function restore(Page $page)
	{
		$page->authorship->restore();

		$result = $this->_query->run('
			UPDATE
				page
			SET
				deleted_at = NULL,
				deleted_by = NULL
			WHERE
				page_id = ?i
		', $page->id);

		$this->_eventDispatcher->dispatch(
			PageEvent::RESTORE,
			new PageEvent($page)
		);

		return $page;
	}
}