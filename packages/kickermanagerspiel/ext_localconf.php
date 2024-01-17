<?php

if (!defined('TYPO3')) {
    die();
}

TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Kickermanagerspiel',
    'RandomTeam',
    [\Simon\Kickermanagerspiel\Controller\RandomTeamController::class => 'index,create'],
    [\Simon\Kickermanagerspiel\Controller\RandomTeamController::class => 'create'],
);

TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Kickermanagerspiel',
    'EffectiveFootballers',
    [\Simon\Kickermanagerspiel\Controller\EffectiveFootballersController::class => 'index'],
);
