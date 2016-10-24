<?php

$path = OW::getPluginManager()->getPlugin('spodprivate')->getRootDir() . 'langs.zip';
BOL_LanguageService::getInstance()->importPrefixFromZip($path, 'spodprivate');
