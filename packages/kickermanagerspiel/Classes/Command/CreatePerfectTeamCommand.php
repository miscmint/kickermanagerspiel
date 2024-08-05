<?php

namespace Simon\Kickermanagerspiel\Command;

use Doctrine\DBAL\Exception;
use Simon\Kickermanagerspiel\Domain\Model\Demand\PerfectDemand;
use Simon\Kickermanagerspiel\Domain\Repository\LastimportRepository;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class CreatePerfectTeamCommand extends AbstractCommand
{
    protected PerfectDemand $perfectDemand;
    protected int $matchday = 0;
    protected ?LastimportRepository $lastImportRepository = null;

    public function configure(): void
    {
        $this->setHelp('');
    }

    public function __construct(string $name = null)
    {
        parent::__construct($name);
        $this->perfectDemand = GeneralUtility::makeInstance(PerfectDemand::class);
        $this->lastImportRepository = GeneralUtility::makeInstance(LastimportRepository::class);
    }

    /**
     * @throws Exception
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->csvFiles as $key => $csvFile) {
            // 0 ≙ season, 1 ≙ only current match day
            for ($onlyMatchday = 0; $onlyMatchday < 2; $onlyMatchday++) {
                $keyArray = explode('_', $key);
                if ($keyArray[0] == 'interactive') {
                    continue;
                }

                $this->matchday = $this->lastImportRepository->getMatchday($key);

                if ($this->matchday == 0) {
                    continue;
                }
                $this->perfectDemand->setSeason((int)$keyArray[2]);
                $this->perfectDemand->setLeague((int)$keyArray[1]);
                $this->perfectDemand->setMatchday($this->matchday);
                $this->perfectDemand->setOnlyMatchday((bool)$onlyMatchday);

                $players = $this->perfectDemand->collectAllSelectablePlayers();
                $leanArray = $this->perfectDemand->createLeanPlayerArray($players);
                file_put_contents('/tmp/test.txt', print_r($leanArray, true));
                exit;
            }
        }
        return 0;
    }
}
