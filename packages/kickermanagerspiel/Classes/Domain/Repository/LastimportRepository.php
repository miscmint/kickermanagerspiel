<?php

namespace Simon\Kickermanagerspiel\Domain\Repository;

use Doctrine\DBAL\Exception;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Repository;

class LastimportRepository extends Repository
{
    /**
     * @throws Exception
     */
    public function getMatchday(string $key): int
    {
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $queryBuilder = $connectionPool->getQueryBuilderForTable('tx_kickermanagerspiel_domain_model_lastimport');
        $queryBuilder->select('matchday')
            ->from('tx_kickermanagerspiel_domain_model_lastimport')
            ->where(
                $queryBuilder->expr()->eq('arraykey', $queryBuilder->createNamedParameter($key))
            )
            ->orderBy('tstamp', 'desc')
            ->setMaxResults(1);
        $result = $queryBuilder->executeQuery()->fetchAssociative();
        return (int)($result['matchday'] ?? 0);
    }
}
