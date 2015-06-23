<?php
/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Yves\Assets\Plugin;

use Silex\Application;
use SprykerFeature\Yves\Twig\Dependency\Plugin\TwigFunctionPluginInterface;
use SprykerEngine\Yves\Kernel\AbstractPlugin;
use SprykerFeature\Yves\Assets\AssetsDependencyContainer;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @method AssetsDependencyContainer getDependencyContainer()
 */
class TwigAssetPlugin extends AbstractPlugin implements TwigFunctionPluginInterface
{
    /**
     * @param Application $application
     *
     * @return \Twig_SimpleFunction[]
     */
    public function getFunctions(Application $application)
    {
        $isDomainSecured = $this->isDomainSecured($application);
        $assetUrlBuilder = $this->getDependencyContainer()->createAssetUrlBuilder($isDomainSecured);
        $mediaUrlBuilder = $this->getDependencyContainer()->createMediaUrlBuilder($isDomainSecured);

        return [
            new \Twig_SimpleFunction('asset', function ($value) use ($assetUrlBuilder) {
                return $assetUrlBuilder->buildUrl($value);
            }),
            new \Twig_SimpleFunction('media', function ($value) use ($mediaUrlBuilder) {
                return $mediaUrlBuilder->buildUrl($value);
            })
        ];
    }

    /**
     * @param Application $application
     *
     * @return RequestStack
     */
    private function getRequestStack(Application $application)
    {
        $requestStack = $application['request_stack'];

        return $requestStack;
    }

    /**
     * @param Application $application
     *
     * @return bool
     */
    private function isDomainSecured(Application $application)
    {
        $requestStack = $this->getRequestStack($application);

        return  $requestStack->getCurrentRequest()->isSecure();
    }
}
