<?php

if (!defined('TYPO3')) {
    die('Access denied.');
}

return [
    'ctrl' => [
        'title' => 'Letzter Import',
        'label' => 'arraykey',
        'label_alt' => 'matchday',
        'label_alt_force' => true,
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => true,
        'delete' => 'deleted',
        'sortby' => 'sorting',
        'shadowColumnsForNewPlaceholders' => 'sys_language_uid,l18n_parent',
        'transOrigPointerField' => 'l18n_parent',
        'transOrigDiffSourceField' => 'l18n_diffsource',
        'languageField' => 'sys_language_uid',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'iconfile' => 'EXT:kickermanagerspiel/Resources/Public/Icons/Extension.svg',
        'searchFields' => 'hash,matchday,content,arraykey',
    ],
    'types' => [
        '1' => ['showitem' => 'hidden,hash,matchday,content,arraykey'],
    ],
    'palettes' => [
        '1' => ['showitem' => 'hash,matchday,content,arraykey'],
    ],
    'columns' => [
        'hidden' => [
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.enabled',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        0 => '',
                        'invertStateDisplay' => true,
                    ],
                ],
            ],
        ],
        'hash' => [
            'exclude' => 0,
            'label' => 'Id',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ]
        ],
        'content' => [
            'exclude' => 0,
            'label' => 'Inhalt',
            'config' => [
                'type' => 'text',
                'cols' => 20,
                'rows' => 4,
            ]
        ],
        'arraykey' => [
            'exclude' => 0,
            'label' => 'Array key des Imports',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ]
        ],
        'matchday' => [
            'exclude' => 0,
            'label' => 'Spieltag',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,int'
            ]
        ]
    ],
];
