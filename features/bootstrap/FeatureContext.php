<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

use Behat\MinkExtension\Context\MinkContext;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Behat\Context\Step;
use Assert\Assertion;


class FeatureContext extends MinkContext
{
	private $params = array();

	public function __construct(array $parameters)
	{
		$this->params = $parameters;
	}	

	/**
	* @Given I am logged in :arg1 with :arg2
	*/
	public function iAmLoggedInWith($arg1, $arg2)
	{		
		$this->iGoToCms($arg1);
		
		$session = $this->getSession();
		$page = $session->getPage();
		$this->getSession()->wait(1000);

		$amILoggedIn = $page->find('css', '.modal-open');
		
		if ($amILoggedIn) {
			$emailField = $page->find('css', '#inputEmail');
			$passwordField = $page->find('css', '#inputPassword');
			$submitButton = $page->find('css', '.btn.btn-lg.btn-primary.btn-block');
			
			switch($arg2)
			{
				case 'admin':
					$emailField->setValue($this->params['user_admin_name']);
					$passwordField->setValue($this->params['user_admin_password']);
				break;	
			}
			$submitButton->click();
			$this->getSession()->wait(1000);				
		}
		else 
		{
			return;
		}
	}

	/**
	 * @Then /^I wait for the suggestion box to appear$/
	*/
	public function iWaitForTheSuggestionBoxToAppear()
	{
    	$this->getSession()->wait(5000, "$('.suggestions-results').children().length > 0");
	}

	/**
	* @When /^I click on "([^"]*)" field$/
	*/
	public function iClickOnField($arg1)
	{
		$session = $this->getSession();
        	$page = $session->getPage();
		$el = $page->find('css', '#searchInput');

		$el->click();
	}

	/**
	* @Given /^I click on the element with xpath "([^"]*)"$/
	*/  
	public function iClickOnTheElementWithXPath($arg1)
	{
		$session = $this->getSession(); // get the mink session
		$element = $session->getPage()->find('xpath', $session->getSelectorsHandler()->selectorToXpath('xpath', $arg1));
 
        	// errors must not pass silently
		if (null === $element) {
			throw new \InvalidArgumentException(sprintf('Could not evaluate XPath: "%s"', $arg1));
		}
       
		$element->click();
	}	

	/**
	* @Given /^I go to "([^"]*)" CMS$/
	*/
	public function iGoToCms($arg1)
	{
		$session = $this->getSession();
		if($arg1 == 'demo')
		{
			$envirnmentValue = $this->params['environment_demo'];
			if($envirnmentValue != $session->getCurrentUrl()) {
				$session->visit($envirnmentValue);
			}	
		}
		elseif($arg1 == 'staging') {
			$envirnmentValue = $this->params['enviroment_staging'];
			if ($envirnmentValue != $session->getCurrentUrl()) {
				$session->visit($envirnmentValue);
			}
		}
		else {
			throw new \InvalidArgumentException(sprintf('Could not find environment: "%s"', $arg1));
		}
	}

	/**
	* @When I go to :arg1 page
	*/
	public function iGoToPage($arg1)
	{
		$session = $this->getSession();
		$currentUrl = $session->getCurrentUrl();
		$session->visit($currentUrl . $arg1);

		$this->getSession()->wait(1000);
	}

	/**
	* @Then /^I should see "([^"]*)" element$/
	*/
	public function iShouldSeeElement($arg1)
	{
		$this->iSeeElement($arg1);
	}

	/**
	* @Given /^I see "([^"]*)" element$/
	*/
	public function iSeeElement($arg1)
	{
		$session = $this->getSession();
		$this->getSession()->wait(3000);
	
		$session->getPage()->find('css', $arg1)->isVisible();
	}

	/**
	* @Given /^I can see that page is disabled$/
	*/
	public function iCanSeeThatPageIsDisabled()
	{
		$session = $this->getSession();	
		Assertion::true($element = $session->getPage()->find('css', '.modal-open')->isVisible());
	}

	/** sign in tests **/
	/**
	* @Given /^I see sign in window$/
	*/
	public function iSeeSignInWindow()
	{
		$session = $this->getSession();
		$this->getSession()->wait(3000);
	
		$session->getPage()->find('css', '.form-signin')->isVisible();
	}
 
	/**
	* @Then /^I should not see "([^"]*)" element$/
	*/
	public function iShouldNotSeeElement($arg1)
	{
		$session = $this->getSession();
		$this->getSession()->wait(1000);
	
		Assertion::true($session->getPage()->find('css', $arg1) === null);
	}

	/**
	* @Given /^I can see that page is not disabled$/
	*/
	public function iCanSeeThatPageIsNotDisabled()
	{
        	$session = $this->getSession();	

		Assertion::true($element = $session->getPage()->find('css', '.modal-open') === null);
	}

	/**
	* @Given /^I cannot see "([^"]*)" element$/
	*/
	public function iCannotSeeElement($arg1)
	{
        	$this->iShouldNotSeeElement($arg1);
	}

	/**
	* @When /^I click "([^"]*)"$/
	*/
	public function iClick($arg1)
	{
        	$session = $this->getSession();
		$selectorsHandler = $session->getSelectorsHandler();
		$linkEl = $session->getPage()->find('named', array('link', $selectorsHandler->xpathLiteral($arg1)));
	
		$linkEl->click();
	}
}

