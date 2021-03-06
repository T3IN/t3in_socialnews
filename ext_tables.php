<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function()
    {

        if (TYPO3_MODE === 'BE') {

            \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
                'T3IN.T3inSocialnews',
                'tools', // Make module a submodule of 'tools'
                'socialpost', // Submodule key
                '', // Position
                [
                    'SocialPost' => 'list,socialPost,facebookPagePost',
                    
                ],
                [
                    'access' => 'user,group',
                    'icon'   => 'EXT:t3in_socialnews/Resources/Public/Icons/user_mod_socialpost.svg',
                    'labels' => 'LLL:EXT:t3in_socialnews/Resources/Private/Language/locallang_socialpost.xlf',
                ]
            );

        }

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('t3in_socialnews', 'Configuration/TypoScript', 'News to Facebook');

    }
);
