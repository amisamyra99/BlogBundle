<?php
/**
 * This file is part of the planetubuntu package.
 *
 * (c) Daniel González <daniel@desarrolla2.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Desarrolla2\Bundle\BlogBundle\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Desarrolla2\Bundle\BlogBundle\Model\PostStatus;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\ORM\Query\QueryException;
use FastFeed\Url;

/**
 * RedirectController
 *
 * Route("/r")
 */
class RedirectController extends Controller
{
    /**
     * Redirect to post source if it has
     *
     * @Route("/p/s/{id}", name="_blog_redirect_post_source", requirements={"id" = "\d{1,11}"})
     * @Method({"GET"})
     *
     * @param Request $request
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return RedirectResponse
     */
    public function postSourceAction(Request $request)
    {
        $post = $this->getDoctrine()->getManager()
            ->getRepository('BlogBundle:Post')->find($request->get('id', false));
        if (!$post) {
            throw $this->createNotFoundException('The post does not exist');
        }
        if ($post->getStatus() != PostStatus::PUBLISHED) {
            return new RedirectResponse($this->generateUrl('_blog_default'), 302);
        }
        if (!$post->hasSource()) {
            return new RedirectResponse(
                $this->generateUrl('_blog_view', array('slug' => $post->getSlug())),
                302
            );
        }

        $url = new Url($post->getSource());
        $url->resetParameters();

        $utm_source = str_replace(
            array('http://', 'https://', '/', '-', '.'),
            array('', '', '', '_', '_'),
            strtolower($this->generateUrl('_blog_default'))
        );

        return new RedirectResponse(
            $url->toString(),
            302
        );
    }
} 