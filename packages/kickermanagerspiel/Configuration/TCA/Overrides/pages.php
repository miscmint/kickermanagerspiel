<?php

if (!defined('TYPO3')) {
    die();
}
call_user_func(function () {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerPageTSConfigFile(
        'kickermanagerspiel',
        'Configuration/TSconfig/Page/page.tsconfig',
        'Seitendefinitionen Managerspiel'
    );
});
