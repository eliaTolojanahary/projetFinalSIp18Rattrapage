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
