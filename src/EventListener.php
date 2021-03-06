<?php

namespace Message\Mothership\CMS;

use Message\Mothership\CMS\Event\Frontend\BuildPageMenuEvent as FrontendBuildMenuEvent;

use Message\Mothership\ControlPanel\Event\BuildMenuEvent as ControlPanelBuildMenuEvent;
use Message\Mothership\ControlPanel\Event\Dashboard\DashboardEvent;
use Message\Mothership\ControlPanel\Event\Dashboard\ActivitySummaryEvent;
use Message\Mothership\ControlPanel\Event\Dashboard\Activity;

use Message\Cog\Event\EventListener as BaseListener;
use Message\Cog\Event\SubscriberInterface;
use Message\Cog\HTTP\RedirectResponse;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Message\Mothership\Report\Event as ReportEvents;

/**
 * Event listener for the Mothership CMS.
 *
 * @author Joe Holdcroft <joe@message.co.uk>
 */
class EventListener extends BaseListener implements SubscriberInterface
{
	/**
	 * {@inheritDoc}
	 */
	static public function getSubscribedEvents()
	{
		return array(
			Events::FRONTEND_BUILD_MENU => array(
				array('filterMenuItems', -9000),
			),
			ControlPanelBuildMenuEvent::BUILD_MAIN_MENU => array(
				array('registerMainMenuItems'),
			),
			KernelEvents::EXCEPTION => array(
				array('pageNotFound'),
			),
			DashboardEvent::DASHBOARD_INDEX => array(
				'buildDashboardIndex',
			),
			'dashboard.cms.content' => array(
				'buildDashboardContent',
			),
			'dashboard.activity.summary' => array(
				'buildDashboardBlockUserSummary',
			),
			ReportEvents\Events::REGISTER_REPORTS => [
				'registerReports'
			],
		);
	}

	/**
	 * Filter out any `Page`s in an array that should not be shown in a menu.
	 *
	 * Pages are filtered out if they shouldn't be visible in menus; are not
	 * published; or are not viewable by the current user.
	 *
	 * @param FrontendBuildMenuEvent  $events Build menu event
	 */
	public function filterMenuItems(FrontendBuildMenuEvent $event)
	{
		$auth = $this->get('cms.page.authorisation');

		foreach ($event->getPages() as $page) {
			if (!$page->visibilityMenu || !$auth->isPublished($page) || !$auth->isViewable($page)) {
				$event->remove($page);
			}
		}
	}

	/**
	 * Register items to the main menu of the control panel.
	 *
	 * @param ControlPanelBuildMenuEvent $event The event
	 */
	public function registerMainMenuItems(ControlPanelBuildMenuEvent $event)
	{
		$event->addItem('ms.cp.cms.dashboard', 'Content', array('ms.cp.cms'));
	}

	/**
	 * Redirect user to dashboard with error message if they get a NotFoundHttpException
	 *
	 * @param GetResponseForExceptionEvent $event
	 */
	public function pageNotFound(GetResponseForExceptionEvent $event)
	{
		$exception = $event->getException();
	}

	/**
	 * Add controller references to the dashboard index.
	 *
	 * @param  DashboardEvent $event
	 */
	public function buildDashboardIndex(DashboardEvent $event)
	{
		$event->addReference('Message:Mothership:CMS::Controller:Module:Dashboard:CMSSummary#index');
		$event->addReference('Message:Mothership:CMS::Controller:Module:Dashboard:BlogComments#index');
	}

	/**
	 * Add controller references to the content dashboard.
	 *
	 * @param  DashboardEvent $event
	 */
	public function buildDashboardContent(DashboardEvent $event)
	{
		$event->addReference('Message:Mothership:CMS::Controller:Module:Dashboard:CMSSummary#index');
		$event->addReference('Message:Mothership:CMS::Controller:Module:Dashboard:BlogComments#index');
	}

	/**
	 * Add the user's last edited page into the user summary dashboard block.
	 *
	 * @param  ActivitySummaryEvent $event
	 */
	public function buildDashboardBlockUserSummary(ActivitySummaryEvent $event)
	{
		$pageID = $this->get('db.query')->run("
			SELECT page_id
			FROM page
			WHERE updated_by = :userID?i
			ORDER BY updated_at DESC
			LIMIT 1
		", [
			'userID' => $event->getUser()->id
		]);

		if (count($pageID)) {
			$page = $this->get('cms.page.loader')->getByID($pageID[0]->page_id);

			if($page) {
				$event->addActivity(new Activity(
					'Last edited page',
					$page->authorship->updatedAt(),
					$page->title,
					$this->get('routing.generator')->generate('ms.cp.cms.edit', [
						'pageID' => $page->id,
					], UrlGeneratorInterface::ABSOLUTE_URL)
				));
			}
		}
	}

	public function registerReports(ReportEvents\BuildReportCollectionEvent $event)
	{
		foreach ($this->get('cms.reports') as $report) {
			$event->registerReport($report);
		}
	}
}