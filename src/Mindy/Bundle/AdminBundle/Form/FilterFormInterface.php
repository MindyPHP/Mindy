<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 07/01/2017
 * Time: 19:13
 */

namespace Mindy\Bundle\AdminBundle\Form;

use Symfony\Component\Form\FormInterface;

interface FilterFormInterface extends FormInterface
{
    public function filter($qs);
}