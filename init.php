<?php

// Check if the route is accessible by guest
$eventHandler = SPODPRIVACY_CLASS_EventHandler::getInstance();
OW::getEventManager()->bind(OW_EventManager::ON_AFTER_ROUTE, array($eventHandler, 'onAfterRoute'));

// Add Admin route
OW::getRouter()->addRoute(new OW_Route('spodprivacy-settings', '/spodprivacy/settings', 'SPODPRIVACY_CTRL_Admin', 'settings'));
