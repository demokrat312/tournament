<?php
/**
 * Created by PhpStorm.
 * User: ogismatulin@srs.lan
 * Date: 06.12.18
 * Time: 16:27
 */

namespace Team\Form;


use Doctrine\ORM\EntityManager;
use Team\Entity\Team;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class TeamForm extends Form
{
    /**
     * Scenario ('create' or 'update').
     * @var string
     */
    private $scenario;


    /**
     * Constructor.
     * @param EntityManager $em
     * @param string        $scenario
     */

    public function __construct(EntityManager $em, $scenario = 'create')
    {

        // Define form name
        parent::__construct('team');

        $this->setAttributes(array('method' => 'post'));

        // Set POST method for this form
        $this->setAttribute('method', 'post');

        // Save parameters for internal use.
        $this->scenario = $scenario;

        $this->setHydrator(new DoctrineHydrator($em));
        $this->setObject(new Team());


        $this->addElements();
        $this->addInputFilter();
    }

    /**
     * This method adds elements to form (input fields and submit button).
     */
    protected function addElements()
    {
        // Add "email" field
        $this->add([
            'type'       => 'text',
            'name'       => 'title',
            'attributes' => [
                'class'       => 'form-control',
                'placeholder' => 'Title',
                'tabIndex'    => 1,
            ],
            'options'    => [
                'label' => 'Name of team',
            ],

        ]);

        $this->add([
            'type' => 'Zend\Form\Element\Csrf',
            'name' => 'csrf',
        ]);

        // Add the Submit button
        $this->add([
            'type'       => 'Button',
            'name'       => 'submit',
            'options'    => array(
                'label' => 'Save',
            ),
            'attributes' => array(
                'type'  => 'submit',
                'class' => 'btn btn-primary',
                'tabIndex'    => 2,
            )
        ]);
    }

    /**
     * This method creates input filter (used for form filtering/validation).
     */
    private function addInputFilter()
    {
        // Create main input filter
        $inputFilter = new InputFilter();
        $this->setInputFilter($inputFilter);

        // Add input for "email" field
        $inputFilter->add([
            'name'       => 'title',
            'required'   => true,
            'filters'    => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name'    => 'StringLength',
                    'options' => [
                        'min' => 1,
                        'max' => 255
                    ],
                ],
            ],
        ]);
    }
}