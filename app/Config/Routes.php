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
$routes->get('detail', 'ClientOperation::detail');

$routes->get('operator/login', 'OperatorLogin::form');
$routes->post('operator/login', 'OperatorLogin::login');
$routes->get('operator/logout', 'OperatorLogin::logout');

$routes->get('operator/dahsboard', 'DashboardOperator::index');

$routes->get('operator/clients', 'ClientsOperator::index');
$routes->get('operator/clients/(:num)', 'ClientsOperator::show/$1');

// Configuration
$routes->get('operator/configuration', 'ConfigurationOperator::index');
$routes->post('operator/configuration/prefix', 'ConfigurationOperator::storePrefix');
$routes->get('operator/configuration/prefix/(:num)/toggle', 'ConfigurationOperator::togglePrefix/$1');
$routes->get('operator/configuration/prefix/(:num)/toggle-principal', 'ConfigurationOperator::togglePrincipal/$1');
$routes->get('operator/configuration/prefix/(:num)/delete', 'ConfigurationOperator::deletePrefix/$1');
$routes->post('operator/configuration/bareme', 'ConfigurationOperator::storeBareme');
$routes->get('operator/configuration/bareme/(:num)/delete', 'ConfigurationOperator::deleteBareme/$1');

// API
$routes->get('api/prefixes', 'Api::prefixes');
$routes->get('logout', 'Client::logout');
