<?php
/**
 * Created by PhpStorm.
 * User: Josh
 * Date: 20/3/2014
 * Time: 5:27 μμ
 */

namespace League\Form;

use Zend\Form\Form;

class SummonerForm extends Form{
    public function __construct()
    {
        parent::__construct('summonerForm');

        $this->setAttributes(array(
            'method' => 'post',
            'class' => 'standardForm'
        ));

        $this->add(array(
            'name' => 'security',
            'type' => 'Zend\Form\Element\Csrf'
        ));

        $this->add(array(
            'name' => 'submit',
            'type' => 'submit'
        ));

        $this->setValidationGroup(array(
            'security',
            'summoner' => array('name',
                'region')
        ));
    }
} 