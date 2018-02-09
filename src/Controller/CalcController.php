<?php

namespace App\Controller;

use App\Entity\Calc;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Routing\Annotation\Route;

class CalcController extends Controller
{
    /**
     * @Route("/", name="welcome")
     */


    public function index()
    {
        return $this->render('index.html.twig');
    }

    /**
     * @Route("/calc/", name="calc")
     */

    public function calc(Request $request)
    {

        $calc = new Calc();

        $calc_form = $this->createFormBuilder($calc)
            ->add('cb', NumberType::class, array('label' => 'Base liquid concentrate, mg/ml'))
            ->add('cn', NumberType::class, array('label' => 'Nicotine (booster) concentrate, mg/ml'))
            ->add('cv', NumberType::class, array('label' => 'Needed concentrate, mg/ml'))
            ->add('Vv', NumberType::class, array('label' => 'Needed volume, ml'))
            ->add('save', SubmitType::class, array('label' => 'Calculate this','attr' => array('class' => 'btn-primary')))
            ->getForm();

        $calc_form->handleRequest($request);

        if ($calc_form->isSubmitted() && $calc_form->isValid()) {

            $values = $calc_form->getData();

            if ($calc->getCb()>$calc->getCn()){
                $calc_form->addError(new FormError('Nicotine concentrate must be greater than the base liquid concentrate'));
                return $this->render('calc/index.html.twig', array(
                    'calc_form' => $calc_form->createView(),
                ));
            }

            if ($calc->getCv()>$calc->getCn() || $calc->getCv()<$calc->getCb()){
                $calc_form->addError(new FormError('Needed concentrate must be greater than the base liquid concentrate and less than the nicotine concentrate'));
                return $this->render('calc/index.html.twig', array(
                    'calc_form' => $calc_form->createView(),
                ));
            }

            return $this->render('calc/index.html.twig', array(
                'calc_form' => $calc_form->createView(),
                'values' => $values,
                'Vn' => $calc->getVn($calc->getVv(),$calc->getCv(),$calc->getCb(),$calc->getCn()),
                'Vb' => $calc->getVb($calc->getVv(),$calc->getCv(),$calc->getCb(),$calc->getCn())
            ));
        }

        return $this->render('calc/index.html.twig', array(
            'calc_form' => $calc_form->createView(),
        ));
    }
}