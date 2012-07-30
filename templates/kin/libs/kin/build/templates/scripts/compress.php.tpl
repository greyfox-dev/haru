<?php
require_once '#phing:libs.${born-properties.lib_name}.deploy.dst#/scripts/bootstrap.php';
$helper = ${born-properties.lib_name}_FrontOffice_DataHelper_JsCssList::getInstance();

$helper->css( true );
$helper->js( true );