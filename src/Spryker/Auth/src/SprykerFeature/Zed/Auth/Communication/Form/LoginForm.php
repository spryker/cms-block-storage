<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\Auth\Communication\Form;

use Spryker\Zed\Gui\Communication\Form\AbstractForm;

class LoginForm extends AbstractForm
{

    const USERNAME = 'username';
    const PASSWORD = 'password';

    /**
     * @return self
     */
    protected function buildFormFields()
    {
        return $this
            ->addText(self::USERNAME, [
                'constraints' => [
                    $this->getConstraints()->createConstraintRequired(),
                    $this->getConstraints()->createConstraintNotBlank(),
                ],
                'attr' => [
                    'placeholder' => 'Email Address',
                ],
            ])
            ->addPassword(self::PASSWORD, [
                'constraints' => [
                    $this->getConstraints()->createConstraintRequired(),
                    $this->getConstraints()->createConstraintNotBlank(),
                ],
                'attr' => [
                    'placeholder' => 'Password',
                ],
            ]);
    }

}
