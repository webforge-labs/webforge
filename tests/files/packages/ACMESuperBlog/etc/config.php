<?php

$conf['psc-cms']['version'] = '1.2';

// displayed in header of cms
$conf['project']['title'] = 'ACME SuperBlog';

$conf['db']['default']['host'] = '127.0.0.1';
$conf['db']['default']['user'] = $package->getSlug();
$conf['db']['default']['password'] = 'eb9ep2xph82r4b';
$conf['db']['default']['database'] = $package->getSlug();
$conf['db']['default']['port'] = NULL;
$conf['db']['default']['charset'] = 'utf8';

$conf['db']['tests'] = $conf['db']['default'];
$conf['db']['tests']['database'] = $package->getSlug().'_tests';

// the order is relevant: the first is the default language
$conf['languages'] = array('de', 'en'); 

// lowercasename => CamelCaseName
$conf['doctrine']['entities']['names'] = array(
  'contentstream'=>'ContentStream',
  'navigationnode'=>'NavigationNode',
  'newsentry'=>'NewsEntry',
  'calendarevent'=>'CalendarEvent'
);

// full FQN to custom Type
$conf['doctrine']['types'] = array();

// mail
$conf['mail']['from'] = 'info@acme.ps-webforge.com';
$conf['mail']['envelope'] = 'info@acme.ps-webforge.com';
