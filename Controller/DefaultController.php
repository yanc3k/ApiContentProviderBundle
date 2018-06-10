<?php

namespace yanc3k\ApiContentProviderBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sulu\Bundle\WebsiteBundle\Controller\WebsiteController;
use Sulu\Component\Content\Compat\StructureInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends WebsiteController
{
    /**
     * Is used for the Sulu rendering usually.
     * Here used to create a json response from the content that was created in the CMS.
     *
     * @param \Sulu\Component\Content\Compat\StructureInterface $structure
     * @param bool $preview
     * @param bool $partial
     *
     * @return Response
     */
    public function indexAction(StructureInterface $structure, $preview = false, $partial = false)
    {
        // Let Sulu create the content data for us. Not yet usable over API.
        $cmsData = $this->getAttributes([], $structure, false);

        // Get the manager for the preparation of the CMS content.
        $manager = $this->get('api_content_provider.content_preparation_manager');

        // Let the manager map the original CMS content data to the API usable custom class.
        $apiContent = $manager->mapCmsContentToApiContent($cmsData);

        // Return the public data the ApiContent class provides as JSON.
        return new JsonResponse($apiContent->getPublicData());
    }

    /**
     * Generates attributes using services from the Sulu CMS.
     *
     * @param array $attributes
     * @param StructureInterface $structure
     * @param bool $preview
     *
     * @return array
     */
    protected function getAttributes($attributes, StructureInterface $structure = null, $preview = false)
    {
        return $this->get('sulu_website.resolver.parameter')->resolve(
            $attributes,
            $this->get('sulu_core.webspace.request_analyzer'),
            $structure,
            $preview
        );
    }
}
