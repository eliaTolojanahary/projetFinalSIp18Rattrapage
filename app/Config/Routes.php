<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Client::form');
$routes->post('login', 'Client::login');
$routes->get('depot', 'ClientOperation::depotForm');
$routes->post('depot', 'ClientOperation::depotStore');
$routes->get('frais', 'ClientOperation::calculerFraisAjax'); 
 $routes->get('retrait', 'ClientOperation::retraitForm');
    $routes->post('retrait', 'ClientOperation::retraitStore');
$routes->get('transfert', 'ClientOperation::transfertForm');
$routes->post('transfert', 'ClientOperation::transfertStore');
$routes->get('historique', 'ClientOperation::historique');