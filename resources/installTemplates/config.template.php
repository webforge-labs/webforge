<?php
require __DIR__.DIRECTORY_SEPARATOR.'changelog.php';

$conf['version'] = $data[0]['version'];
$conf['psc-cms']['version'] = '0.2';

// displayed in header of cms
$conf['project']['title'] = 'Psc - CMS - %package.title% ['.($project->isStaging() ? 'staging' : 'live').']';

$conf['db']['default']['host'] = '127.0.0.1';
$conf['db']['default']['user'] = $project->getLowerName();
$conf['db']['default']['password'] = '%db.password%';
$conf['db']['default']['database'] = $project->isStaging() ? $project->getLowerName().'_staging' : $project->getLowerName();
$conf['db']['default']['port'] = NULL;
$conf['db']['default']['charset'] = 'utf8';

$conf['db']['tests'] = $conf['db']['default'];
$conf['db']['tests']['database'] = $project->getLowerName().'_tests';

// the order is relevant: the first is the default language
$conf['languages'] = array('%defaultLanguage%'); 


// lowercasename => CamelCaseName
$conf['doctrine']['entities']['names'] = array(
                                          'contentstream'=>'ContentStream',
                                          'navigationnode'=>'NavigationNode',
                                          'newsentry'=>'NewsEntry',
                                          'calendarevent'=>'CalendarEvent'
                                        );

// full FQN to custom Type
$conf['doctrine']['types'] = array();

//require 'inc.testCreater.config.php';
?>