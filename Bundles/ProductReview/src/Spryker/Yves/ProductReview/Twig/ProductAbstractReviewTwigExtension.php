<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Yves\ProductReview\Twig;

use Spryker\Client\ProductReview\ProductReviewClientInterface;
use Spryker\Shared\Twig\TwigExtension;
use Spryker\Yves\Kernel\Application;
use Twig_Environment;
use Twig_SimpleFunction;

class ProductAbstractReviewTwigExtension extends TwigExtension
{

    const FUNCTION_NAME_PRODUCT_ABSTRACT_REVIEW = 'spyProductAbstractReview';

    /**
     * @var \Spryker\Client\ProductReview\ProductReviewClientInterface
     */
    protected $productReviewClient;

    /**
     * @var \Spryker\Yves\Kernel\Application
     */
    protected $application;

    /**
     * @param \Spryker\Client\ProductReview\ProductReviewClientInterface $productReviewClient
     * @param \Spryker\Yves\Kernel\Application $application
     */
    public function __construct(ProductReviewClientInterface $productReviewClient, Application $application)
    {
        $this->productReviewClient = $productReviewClient;
        $this->application = $application;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction(static::FUNCTION_NAME_PRODUCT_ABSTRACT_REVIEW, [$this, 'renderProductAbstractReview'], [
                'is_safe' => ['html'],
                'needs_environment' => true,
            ]),
        ];
    }

    /**
     * @param \Twig_Environment $twig
     * @param int $idProductAbstract
     * @param string $template
     *
     * @return string
     */
    public function renderProductAbstractReview(Twig_Environment $twig, $idProductAbstract, $template)
    {
        $productAbstractReviewTransfer = $this->productReviewClient->findProductAbstractReview($idProductAbstract, $this->getLocale());

        if (!$productAbstractReviewTransfer) {
            return '';
        }

        return $twig->render($template, [
            'productAbstractReviewTransfer' => $productAbstractReviewTransfer,
        ]);
    }

    /**
     * @return string
     */
    protected function getLocale()
    {
        return $this->application['locale'];
    }

}
