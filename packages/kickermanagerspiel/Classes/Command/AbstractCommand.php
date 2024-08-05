<?php

namespace Simon\Kickermanagerspiel\Command;

use Symfony\Component\Console\Command\Command;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class AbstractCommand extends Command
{
    protected array $csvFiles = [
        'interactive_1_2024' => 'https://www.kicker-libero.de/api/sportsdata/v1/players-details/se-k00012024.csv',
        'interactive_2_2024' => 'https://www.kicker-libero.de/api/sportsdata/v1/players-details/se-k00022024.csv',
        'interactive_3_2024' => 'https://www.kicker-libero.de/api/sportsdata/v1/players-details/se-k00032024.csv',
        'classic_1_2024' => 'https://classic.kicker-libero.de/api/sportsdata/v1/players-details/se-k00012024.csv',
        'classic_2_2024' => 'https://classic.kicker-libero.de/api/sportsdata/v1/players-details/se-k00022024.csv',
        'classic_3_2024' => 'https://classic.kicker-libero.de/api/sportsdata/v1/players-details/se-k00032024.csv',
    ];

    protected int $folder = 0;

    protected ConnectionPool $connectionPool;

    /**
     * @throws ExtensionConfigurationPathDoesNotExistException
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     */
    public function __construct(string $name = null)
    {
        parent::__construct($name);
        $this->connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $extConfig = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('kickermanagerspiel');
        $this->folder = $extConfig['folder'];
    }
}
