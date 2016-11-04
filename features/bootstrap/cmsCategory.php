<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Behat\Context\ContextInterface;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

use Behat\MinkExtension\Context\MinkContext;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Behat\Context\Step;
use Assert\Assertion;

/**
 * cmsCategory.
 */
class cmsCategory extends RawMinkContext {
    private $params = array();
    	
	public function __construct(array $parameters)
	{
		$this->params = $parameters;
	}

	/**
	* @Then I should be in :arg1 page
	*/
	public function iShouldBeInPage($arg1)
	{
        	$session = $this->getSession();
		$page = $session->getPage();
		$this->getSession()->wait(1000);

		$header = $page->find('css', '.page-header')->getText();

		Assertion::true($header == $arg1);
	}

	/**
	* @Given I am in :arg1 page
	*/
	public function iAmInPage($arg1)
	{
        	$this->iShouldBeInPage($arg1);
	}


	/**
	* @AfterScenario
	**/
	public function tearDown()
	{
		$this->getSession()->reset();	
	}
	
}

