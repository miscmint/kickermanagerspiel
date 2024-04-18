<?php

if (!defined('TYPO3')) {
    die('Access denied.');
}

return [
    'ctrl' => [
        'title' => 'Spieler',
        'label' => 'lastname',
        'label_alt' => 'firstname,mode',
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
        'searchFields' => 'id,mode,firstname,lastname,position,value,season',
    ],
    'types' => [
        '1' => ['showitem' => 'hidden,id,mode,firstname,lastname,position,value,club,club_before_first_matchday,points,points_matchdays,season,league,efficiency'],
    ],
    'palettes' => [
        '1' => ['showitem' => 'id,mode,firstname,lastname,position,value,club,club_before_first_matchday,points,points_matchdays,season,league,efficiency'],
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
        'id' => [
            'exclude' => 0,
            'label' => 'Id',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ]
        ],
        'mode' => [
            'exclude' => 0,
            'label' => 'Modus',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'label' => 'interactive',
                        'value' => 'interactive',
                    ],
                    [
                        'label' => 'classic',
                        'value' => 'classic',
                    ],
                ],
            ]
        ],
        'firstname' => [
            'exclude' => 0,
            'label' => 'Vorname',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ]
        ],
        'lastname' => [
            'exclude' => 0,
            'label' => 'Nachname',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ]
        ],
        'position' => [
            'exclude' => 0,
            'label' => 'Position',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'label' => 'goalkeeper',
                        'value' => 'goalkeeper',
                    ],
                    [
                        'label' => 'defender',
                        'value' => 'defender',
                    ],
                    [
                        'label' => 'midfielder',
                        'value' => 'midfielder',
                    ],
                    [
                        'label' => 'forward',
                        'value' => 'forward',
                    ],
                ],
            ]
        ],
        'value' => [
            'exclude' => 0,
            'label' => 'Marktwert (in Mio. €)',
            'config' => [
                'type' => 'input',
                'size' => 20,
                'eval' => 'trim,double2',
            ]
        ],
        'club' => [
            'exclude' => 0,
            'label' => 'Verein',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_kickermanagerspiel_domain_model_club',
                'foreign_table_where' => 'ORDER BY title',
            ]
        ],
        'club_before_first_matchday' => [
            'exclude' => 0,
            'label' => 'Verein vor dem ersten Spieltag',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_kickermanagerspiel_domain_model_club',
                'foreign_table_where' => 'ORDER BY title',
                'items' => [
                    [
                        'label' => '-- nichts angegeben --',
                        'value' => 0,
                    ],
                ]
            ]
        ],
        'points' => [
            'exclude' => 0,
            'label' => 'Punkte gesamt',
            'config' => [
                'type' => 'input',
                'size' => 20,
                'eval' => 'trim,int',
            ]
        ],
        'points_matchdays' => [
            'exclude' => 0,
            'label' => 'Punkte an den jeweiligen Spieltagen',
            'config' => [
                'type' => 'text',
                'cols' => 20,
                'rows' => 4,
            ]
        ],
        'season' => [
            'exclude' => 0,
            'label' => 'Saison',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,int'
            ]
        ],
        'league' => [
            'exclude' => 0,
            'label' => 'Liga',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,int'
            ]
        ],
        'efficiency' => [
            'exclude' => 0,
            'label' => 'Effizienz',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,double'
            ]
        ],
    ],
];
