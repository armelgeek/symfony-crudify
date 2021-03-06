<?php

/*
 * This file is part of the  Crudify package.
 * (c) Armel wanes <armelgeek5@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ArmelWanes\Crudify\EventListener;

use Fig\Link\GenericLinkProvider as FigGenericLinkProvider;
use Fig\Link\Link as FigLink;
use Symfony\Component\WebLink\GenericLinkProvider;
use Symfony\Component\WebLink\Link;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use ArmelWanes\Crudify\Asset\TagRenderer;

/**
 * @author Ryan Weaver <ryan@symfonycasts.com>
 */
class PreLoadAssetsEventListener implements EventSubscriberInterface
{
    private $tagRenderer;

    public function __construct(TagRenderer $tagRenderer)
    {
        $this->tagRenderer = $tagRenderer;
    }

    /**
     * @param ResponseEvent $event
     */
    public function onKernelResponse($event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();

        if (null === $linkProvider = $request->attributes->get('_links')) {
            $request->attributes->set(
                '_links',
                // For backwards-compat with symfony/web-link 4.3 and lower
                class_exists(GenericLinkProvider::class) ? new GenericLinkProvider() : new FigGenericLinkProvider()
            );
        }

        /** @var GenericLinkProvider|FigGenericLinkProvider $linkProvider */
        $linkProvider = $request->attributes->get('_links');
        $defaultAttributes = $this->tagRenderer->getDefaultAttributes();
        $crossOrigin = $defaultAttributes['crossorigin'] ?? false;

        foreach ($this->tagRenderer->getRenderedScripts() as $href) {
            $link = ($this->createLink('preload', $href))->withAttribute('as', 'script');

            if (false !== $crossOrigin) {
                $link = $link->withAttribute('crossorigin', $crossOrigin);
            }

            $linkProvider = $linkProvider->withLink($link);
        }

        foreach ($this->tagRenderer->getRenderedStyles() as $href) {
            $link = ($this->createLink('preload', $href))->withAttribute('as', 'style');

            if (false !== $crossOrigin) {
                $link = $link->withAttribute('crossorigin', $crossOrigin);
            }

            $linkProvider = $linkProvider->withLink($link);
        }

        $request->attributes->set('_links', $linkProvider);
    }

    public static function getSubscribedEvents()
    {
        return [
            // must run before AddLinkHeaderListener
            'kernel.response' => ['onKernelResponse', 50],
        ];
    }

    /**
     * For backwards-compat with symfony/web-link 4.3 and lower.
     *
     * @return Link|FigLink
     */
    private function createLink(string $rel, string $href)
    {
        $class = class_exists(Link::class) ? Link::class : FigLink::class;

        return new $class($rel, $href);
    }
}
