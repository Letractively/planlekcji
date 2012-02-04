<?php

class Controller_Mobile extends Controller {

    public function __construct() {
	unset($_COOKIE['_nomobile']);
    }

    public function action_index() {

	$view = View::factory('_mobile');
	$view->set('content', View::factory('_mobile_classlist')->render());
	echo $view->render();
    }

    public function action_klasa($klasa) {
	$view = View::factory('_mobile');
	$view2 = View::factory('_mobile_klasa');

	$view2->set('klasa', $klasa);

	$view->set('title', $klasa);
	$view->set('content', $view2->render());
	echo $view->render();
    }

}
