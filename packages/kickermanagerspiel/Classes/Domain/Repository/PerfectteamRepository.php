<?php

namespace Simon\Kickermanagerspiel\Domain\Repository;

use Doctrine\DBAL\Exception;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Repository;

class PerfectteamRepository extends Repository
{
    /**
     * @throws Exception
     */
    public function findByParams(int $matchday, int $season, int $league)
    {
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $queryBuilder = $connectionPool->getQueryBuilderForTable('tx_kickermanagerspiel_domain_model_perfectteam');
        $queryBuilder->select('players')
            ->from('tx_kickermanagerspiel_domain_model_perfectteam')
            ->where(
                $queryBuilder->expr()->eq('matchday', $queryBuilder->createNamedParameter($matchday)),
                $queryBuilder->expr()->eq('season', $queryBuilder->createNamedParameter($season)),
                $queryBuilder->expr()->eq('league', $queryBuilder->createNamedParameter($league)),
            );
        return $queryBuilder->executeQuery()->fetchAllAssociative();
    }
}
