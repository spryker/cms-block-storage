<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CustomerGroup\Communication\Controller;

use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \Spryker\Zed\CustomerGroup\Business\CustomerGroupFacade getFacade()
 * @method \Spryker\Zed\CustomerGroup\Communication\CustomerGroupCommunicationFactory getFactory()
 */
class EditController extends AbstractController
{

    const PARAM_ID_CUSTOMER_GROUP = 'id-customer-group';

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function indexAction(Request $request)
    {
        $idCustomerGroup = $this->castId($request->query->get(static::PARAM_ID_CUSTOMER_GROUP));

        $dataProvider = $this->getFactory()->createCustomerGroupFormDataProvider();
        $form = $this->getFactory()
            ->createCustomerGroupForm(
                $dataProvider->getData($idCustomerGroup),
                $dataProvider->getOptions($idCustomerGroup)
            )
            ->handleRequest($request);

        if ($form->isValid()) {
            $customerGroupTransfer = $dataProvider->prepareDataAsTransfer($form->getData());

            $this->getFacade()->update($customerGroupTransfer);

            return $this->redirectResponse(
                sprintf('/customer-group/view?%s=%d', static::PARAM_ID_CUSTOMER_GROUP, $idCustomerGroup)
            );
        }

        return $this->viewResponse([
            'form' => $form->createView(),
            'idCustomerGroup' => $idCustomerGroup,
        ]);
    }

}
