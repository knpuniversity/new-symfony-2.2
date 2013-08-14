<?php

namespace Yoda\EventBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class NewFeaturesController extends Controller
{
    /**
     * A test page to show off ESI
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function testFragmentsAction()
    {
        return $this->render('EventBundle:NewFeatures:testFragments.html.twig');
    }

    /**
     * The "embedded" controller, rendered in testFragments.html.twig
     *
     * @param string $color
     * @return string
     */
    public function innerAction($color)
    {
        return new Response(
            sprintf(
                '<div class="inner-box" style="background-color: %s;">Inside "fragment"</div>',
                $color
            )
        );
    }
}