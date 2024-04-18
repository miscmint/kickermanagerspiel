<?php
namespace Simon\Kickermanagerspiel\Helper;

use Simon\Kickermanagerspiel\Domain\Model\Player;

class Usort
{
    public static function comparePlayersByValue(?Player $player1, ?Player $player2): int
    {
        if (empty($player1)) {
            return 0;
        }
        if (empty($player2)) {
            return 0;
        }
        $value1 = $player1->getValue();
        $value2 = $player2->getValue();
        if ($value1 == $value2) {
            return 0;
        }
        return ($value1 < $value2) ? 1 : -1;
    }

    public static function compareGoalsOfAMatch(array $goal1, array $goal2): int
    {
        $sum1 = $goal1['scoreTeam1'] + $goal1['scoreTeam2'];
        $sum2 = $goal2['scoreTeam1'] + $goal2['scoreTeam2'];
        return ($sum1 < $sum2) ? -1 : 1;
    }
}
