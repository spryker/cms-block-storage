<?php
/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */
namespace Spryker\Client\Wishlist\Cart;

use Generated\Shared\Transfer\WishlistMoveToCartRequestTransfer;

interface CartHandlerInterface
{

    /**
     * @param \Generated\Shared\Transfer\WishlistMoveToCartRequestTransfer $wishlistMoveToCartRequestTransfer
     *
     * @return \Generated\Shared\Transfer\WishlistMoveToCartRequestTransfer
     */
    public function moveToCart(WishlistMoveToCartRequestTransfer $wishlistMoveToCartRequestTransfer);

}
