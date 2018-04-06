<?php

namespace OCA\Files_Sharing\BackgroundJob;

use OC\BackgroundJob\TimedJob;
use OCP\IDBConnection;
use OCP\OCS\IDiscoveryService;

class FederatedSharesDiscoverJob extends TimedJob {
	/** @var IDBConnection */
	private $connection;
	/** @var IDiscoveryService */
	private $discoveryService;

	public function __construct(IDBConnection $connection,
								IDiscoveryService $discoveryService) {
		$this->connection = $connection;
		$this->discoveryService = $discoveryService;

		$this->setInterval(86400);
	}

	public function run($argument) {
		$qb = $this->connection->getQueryBuilder();

		$qb->selectDistinct('remote')
			->from('share_external');

		$result = $qb->execute();
		while($row = $result->fetch()) {
			$this->discoveryService->discover($row['remote'], 'FEDERATED_SHARING', true);
		}
		$result->closeCursor();
	}
}
