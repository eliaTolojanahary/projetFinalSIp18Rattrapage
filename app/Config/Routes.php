<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'DashboardOperator::index');


$routes->get('livres', 'Livres::index');
$routes->get('livres/(:num)', 'Livres::show/$1');
$routes->get('livres/ajouter', 'Livres::create');
$routes->post('livres/ajouter', 'Livres::store');
$routes->post('livres/(:num)/supprimer', 'Livres::delete/$1');
$routes->post('livres/(:num)/preter', 'Livres::preter/$1');
$routes->post('livres/(:num)/retour', 'Livres::retour/$1');

$routes->get('clients', 'ClientsOperator::index');
$routes->get('clients/(:num)', 'ClientsOperator::show/$1');

// Configuration
$routes->get('configuration', 'ConfigurationOperator::index');
$routes->post('configuration/prefix', 'ConfigurationOperator::storePrefix');
$routes->get('configuration/prefix/(:num)/toggle', 'ConfigurationOperator::togglePrefix/$1');
$routes->get('configuration/prefix/(:num)/delete', 'ConfigurationOperator::deletePrefix/$1');
$routes->post('configuration/bareme', 'ConfigurationOperator::storeBareme');
$routes->get('configuration/bareme/(:num)/delete', 'ConfigurationOperator::deleteBareme/$1');

// API
$routes->get('api/prefixes', 'Api::prefixes');
