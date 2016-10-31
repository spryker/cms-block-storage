<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Product\Business\Attribute;

use Generated\Shared\Transfer\RawProductAttributesTransfer;

class AttributeMerger implements AttributeMergerInterface
{

    /**
     * @param \Generated\Shared\Transfer\RawProductAttributesTransfer $rawProductAttributesTransfer
     *
     * @return array
     */
    public function merge(RawProductAttributesTransfer $rawProductAttributesTransfer)
    {
        $combinedAttributes = array_merge(
            (array)$rawProductAttributesTransfer->getAbstractAttributes(),
            (array)$rawProductAttributesTransfer->getAbstractLocalizedAttributes(),
            (array)$rawProductAttributesTransfer->getConcreteAttributes(),
            (array)$rawProductAttributesTransfer->getConcreteLocalizedAttributes()
        );

        return $combinedAttributes;
    }

}
