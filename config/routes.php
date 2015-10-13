<?php
use Cake\Routing\Router;

Router::plugin('Tutor', function ($routes) {
    $routes->fallbacks('InflectedRoute');

    $routes->resources('Tutors');
    $routes->resources('Tutorquestions');
    $routes->resources('Tutors', function ($routes) {
        $routes->resources('Tutorquestions');
        $routes->resources('Tutoranswers');
    });
});
