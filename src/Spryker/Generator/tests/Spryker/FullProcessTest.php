<?php

use SprykerEngine\Zed\Transfer\Business\Model\Generator\ClassCollectionManager;
use SprykerEngine\Zed\Transfer\Business\Model\Generator\ClassGenerator;
use Zend\Config\Config;
use Zend\Config\Factory;

class FullProcessTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    protected $xmlTree;

    /**
     * @var ClassCollectionManager
     */
    protected $manager;

    /**
     * @var array
     */
    protected $generatedClasses = [];

    public function setUp()
    {
        $this->manager = new ClassCollectionManager();

        $file = dirname(__DIR__) . '/data/template.xml';

        $definitions = Factory::fromFile($file, true)->toArray();

        foreach ($definitions as $item) {
            if ( is_array($item) ) {
                foreach ($item as $itemComponent) {
                    $this->manager->setClassDefinition($itemComponent);
                }
            } else {
                $this->manager->setClassDefinition($item);
            }
        }

        $definitions = $this->manager->getCollections();

        $generator = new ClassGenerator();
        $generator->setNamespace('Generated\Shared\Transfer');
        $generator->setTargetFolder(dirname(__DIR__) . '/target/');
        foreach ($definitions as $classDefinition) {
            $this->generatedClasses[] = [
                'definition' => $classDefinition,
                'code' => $generator->generateClass($classDefinition),
            ];
        }
    }


    public function test_class_name()
    {
        $this->assertSame('AlfaTransfer', $this->generatedClasses[0]['definition']->getClassName());
    }

    public function test_get_interfaces()
    {
        $this->assertEquals(3, count($this->generatedClasses[0]['definition']->getInterfaces()));
    }

    /**
     * @dataProvider codeSampleValidationProvider
     * @param $codeSample
     */
    public function test_generated_class_content($codeSample)
    {
        $this->assertContains($codeSample, $this->generatedClasses[0]['code']);
    }

    /**
     * Those strings should be in the generated code class
     *
     * @return array
     */
    public function codeSampleValidationProvider()
    {
        return [
            ['namespace Generated\Shared\Transfer;'],
            ['$properties = [];'],
            ['$published = true;'],
            ['@var array $properties'],
            ['@return array'],
            ['@var Customer $customer'],
            ['@return Customer'],
            ['$this->user_id = $user_id;'],
            ['public function setUserId($user_id)'],
            ['public function getUserId()'],
            ['public function addCartItem(CartItem $cartItems)'],
            ['public function getCartItems()'],
            ['public function removeCartItem()'],
            ['public function setCartItems(array $cartItems)'],
            ['$this->properties[] = $properties;'],
            ['setCustomer(Customer $customer)'],
            ['public function setPublished(bool $published)'],
            ['use Spryker\Demo\FirstInterface'],
            ['use Spryker\Demo\SecondInterface'],
            ['use Spryker\Demo\ThirdInterface'],
            ['$this->addModifiedProperty(\'published\');'],
            ['$this->addModifiedProperty(\'customer\');'],
            ['$this->addModifiedProperty(\'properties\');'],
            ['$this->addModifiedProperty(\'cartItems\');'],
//            ['class AlfaTransfer extends AbstractTransfer implements FirstInterface, SecondInterface, ThirdInterface'],
        ];
    }

    /**
     * @dataProvider codeSampleValidationProviderInexistance
     * @param $codeSample
     */
    public function test_generated_class_content_should_not_exists($codeSample)
    {
        $this->assertNotContains($codeSample, $this->generatedClasses[0]['code']);
    }

    /**
     * Those strings should not be in the generated code class
     *
     * @return array
     */
    public function codeSampleValidationProviderInexistance()
    {
        return [
            ['namespace ;'],
            ['use CartItem'],
            ['use Customer'],
            ['use FirstInterface'],
            ['setCustomer(Customer$customer)'],
            ['setPublished(boolean$published)'],
            ['setProperties(array$properties)'],
            ['setCartItems(CartItem$cartItems)'],
            ['setCartItems($cartItems)'],
            ['setCartItems( $cartItems)'],
            ['setCartItems(CartItems $cartItems)'],
            ['addCartItems(CartItems $cartItems)'],
        ];
    }
}
