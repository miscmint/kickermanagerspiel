<?php

declare(strict_types=1);

namespace Simon\Kickermanagerspiel\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

class EffectiveFootballersController extends ActionController
{
    /**
     * @return ResponseInterface
     */
    public function indexAction(): ResponseInterface
    {
        DebuggerUtility::var_dump('perko');
        $assignedValues = [];
        $this->view->assignMultiple($assignedValues);
        return $this->htmlResponse();
    }
}
