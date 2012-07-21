<?php //CPYRGHT
/**
 * api.piratenpartei-bw.de
 * 
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @author  Thomas Heidrich, Adrian Kummerländer
 * @copyright Copyright (c) 2012 Thomas Heidrich and other authors
 */
?><?php
/**
 * OSM-Location extractor
 *
 * This file renders OSM locations into a nice looking map
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('AppController', 'Controller');

/**
 * Stammtisch location extractor controller
 */
class StammtischController extends AppController{
    
    /**
     * Controller name
     *
     * @var string
     */
    public $name = 'Stammtisch';
    
    /**
     * Models in use
     * @var array
     */
    public $uses = array('Stammtisch');
    
    /**
     * Helpers in use
     * @var array
     */
    public $helpers = array('Html');
    
    // ############## PUBLICLY ACCESSIBLE METHODS ################
    /**
     * Displays a static manual page
     */
    public function index(){
        $this->layout = 'default-trans';
    }
    
    /**
     * Displays a static manual page
     */
    public function karte(){
        $this->layout = 'barebone';
        $this->Stammtisch->updateStammtische();
        
        $minZoom = $this->sanitizeIntParam('minzoom', 3, 24, 6);
        $maxZoom = $this->sanitizeIntParam('maxzoom', 3, 24, 18);
        $defaultZoom = $this->sanitizeIntParam('defaultzoom', 3, 24, 8);
        $lat = $this->sanitizeFloatParam('lat', -90, 90, 48.54);
        $lon = $this->sanitizeFloatParam('lon', -180, 180, 9.04);
        $scrollZoom = $this->sanitizeBooleanParam('scrollzoom');
        $dragging = $this->sanitizeBooleanParam('dragging');
        
        $this->set('min_zoom', $minZoom);
        $this->set('max_zoom', $maxZoom);
        $this->set('default_zoom', $defaultZoom);
        $this->set('lat', $lat);
        $this->set('lon', $lon);
        $this->set('scroll_zoom', $scrollZoom);
        $this->set('dragging', $dragging);
    }
    
    /**
     * Renders an iCal file for a certain stammtisch appointment.
     * @param int $id The ID of the appointment to render.
     */
    public function termin_ics($id = 0){
        $event = $this->Stammtisch->findById($id);
        if(!empty($event)){
            $this->layout = 'ajax';
            $event['Stammtisch']['timestamp'] = strtotime($event['Stammtisch']['date']);
            $this->set('event', $event);
        }else{
            $this->Session->setFlash('Es wurde leider kein Kalendereintrag gefunden: :-(');
        }
    }
    
    /**
     * Renders an iCal file for a certain stammtisch appointment.
     * @param int $id The ID of the appointment to render.
     */
    public function termine(){
        $events = $this->Stammtisch->find(
            'all'
            ,array(
                'conditions' => array(
                    array(
                        'not' => array(
                            'Stammtisch.date' => null
                     )
                    )
                )
                ,'recursive' => -1
            )
        );
        if(!empty($events)){
            $this->layout = 'barebone';
            $this->set('events', $events);
        }else{
            $this->Session->setFlash('Es wurden leider keine Kalendereinträge gefunden: :-(');
        }
    }
    
    /**
     * Gets an integer from params
     * @param string $param
     * @param int $min
     * @param int $max
     * @param int $default
     * @return The sanitized param as int or the default.
     */
    protected function sanitizeIntParam($param, $min, $max, $default = 0){
        $retval = $default;
        if(!empty($this->params['named'][$param])
                && ((int)$this->params['named'][$param]) <= $max
                && ((int)$this->params['named'][$param]) >= $min
        ){
            $retval = (int)$this->params['named'][$param];
        }
        return $retval;
    }
    
    /**
     * Gets a float from params
     * @param string $param
     * @param float $min
     * @param float $max
     * @param float $default
     * @return The sanitized param as float or the default.
     */
    protected function sanitizeFloatParam($param, $min, $max, $default = 0){
        $retval = $default;
        if(!empty($this->params['named'][$param])
                && ((float)$this->params['named'][$param]) <= $max
                && ((float)$this->params['named'][$param]) >= $min
        ){
            $retval = (float)$this->params['named'][$param];
        }
        return $retval;
    }
    
    /**
     * Gets a boolean from params
     * @param string $param
     * @param string $default Should be 'true' or 'false'
     * @return string The sanitized param as string ('true' or 'false')
     *     or the default
     */
    protected function sanitizeBooleanParam($param, $default = 'true'){
        $retval = $default;
        if(isset($this->params['named'][$param])
                && $this->params['named'][$param] === '0'
        ){
            $retval = 'false';
        }
        return $retval;
    }
}
