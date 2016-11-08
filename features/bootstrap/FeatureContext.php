<?php

// To do: for all switches add default when none of expected values are got, throw exceptio maybe?
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

class FeatureContext extends MinkContext {

    private $params = array();

    public function __construct(array $parameters) {
        $this->params = $parameters;
    }

    /**
     * @Given I am logged in :arg1 with :arg2
     */
    public function iAmLoggedInWith($arg1, $arg2) {
        $this->iGoToCms($arg1);

        $session = $this->getSession();
        $page = $session->getPage();
        $this->getSession()->wait(1000);

        $amILoggedIn = $page->find('css', '.modal-open');

        if ($amILoggedIn) {
            $emailField = $page->find('css', '#inputEmail');
            $passwordField = $page->find('css', '#inputPassword');
            $submitButton = $page->find('css', '.btn.btn-lg.btn-primary.btn-block');

            switch ($arg2) {
                case 'admin':
                    $emailField->setValue($this->params['user_admin_name']);
                    $passwordField->setValue($this->params['user_admin_password']);
                    break;
            }
            $submitButton->click();
            $this->getSession()->wait(1000);
        } else {
            return;
        }
    }

    /**
     * @Then /^I wait for the suggestion box to appear$/
     */
    public function iWaitForTheSuggestionBoxToAppear() {
        $this->getSession()->wait(5000, "$('.suggestions-results').children().length > 0");
    }

    /**
     * @When /^I click on "([^"]*)" field$/
     */
    public function iClickOnField($arg1) {
        $session = $this->getSession();
        $page = $session->getPage();
        $el = $page->find('css', '#searchInput');

        $el->click();
    }

    /**
     * @Given /^I click on the element with xpath "([^"]*)"$/
     */
    public function iClickOnTheElementWithXPath($arg1) {
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
    public function iGoToCms($arg1) {
        $session = $this->getSession();
        if ($arg1 == 'demo') {
            $envirnmentValue = $this->params['environment_demo'];
            if ($envirnmentValue != $session->getCurrentUrl()) {
                $session->visit($envirnmentValue);
            }
        } elseif ($arg1 == 'staging') {
            $envirnmentValue = $this->params['enviroment_staging'];
            if ($envirnmentValue != $session->getCurrentUrl()) {
                $session->visit($envirnmentValue);
            }
        } else {
            throw new \InvalidArgumentException(sprintf('Could not find environment: "%s"', $arg1));
        }
    }

    /**
     * @When I go to :arg1 page
     */
    public function iGoToPage($arg1) {
        $session = $this->getSession();
        $currentUrl = $session->getCurrentUrl();
        $session->visit($currentUrl . $arg1);

        $this->getSession()->wait(1000);
    }

    /**
     * @Then /^I should see "([^"]*)" element$/
     */
    public function iShouldSeeElement($arg1) {
        $this->iSeeElement($arg1);
    }

    /**
     * @Given /^I see "([^"]*)" element$/
     */
    public function iSeeElement($arg1) {
        $session = $this->getSession();
        $this->getSession()->wait(3000);

        $session->getPage()->find('css', $arg1)->isVisible();
    }

    /**
     * @Given /^I can see that page is disabled$/
     */
    public function iCanSeeThatPageIsDisabled() {
        $session = $this->getSession();
        Assertion::true($element = $session->getPage()->find('css', '.modal-open')->isVisible());
    }

    /** sign in tests * */

    /**
     * @Given /^I see sign in window$/
     */
    public function iSeeSignInWindow() {
        $session = $this->getSession();
        $this->getSession()->wait(3000);

        $session->getPage()->find('css', '.form-signin')->isVisible();
    }

    /**
     * @Then /^I should not see "([^"]*)" element$/
     */
    public function iShouldNotSeeElement($arg1) {
        $session = $this->getSession();
        $this->getSession()->wait(1000);

        Assertion::true($session->getPage()->find('css', $arg1) === null);
    }

    /**
     * @Given /^I can see that page is not disabled$/
     */
    public function iCanSeeThatPageIsNotDisabled() {
        $session = $this->getSession();

        Assertion::true($element = $session->getPage()->find('css', '.modal-open') === null);
    }

    /**
     * @Given /^I cannot see "([^"]*)" element$/
     */
    public function iCannotSeeElement($arg1) {
        $this->iShouldNotSeeElement($arg1);
    }

    /**
     * @When /^I click "([^"]*)"$/
     */
    public function iClick($arg1) {
        $session = $this->getSession();
        $selectorsHandler = $session->getSelectorsHandler();
        $linkEl = $session->getPage()->find('named', array('link', $selectorsHandler->xpathLiteral($arg1)));

        $linkEl->click();
    }

    /**
     * @When I click :arg1 tab
     */
    public function iClickTab($arg1) {
        $session = $this->getSession(); // get the mink session
        $element = $session->getPage()->find('xpath', $session->getSelectorsHandler()->selectorToXpath('xpath', "//li[not(@class)]/a[contains(text(), '" . $arg1 . "')]"));

        // errors must not pass silently
        if (null === $element) {
            throw new \InvalidArgumentException(sprintf('Could not evaluate XPath for Tab: "%s"', $arg1));
        }

        $element->click();
    }

    /**
     * @Then I should see notification with :arg1
     */
    public function iShouldSeeNotificationWith($arg1) {
        $this->getSession()->wait(1000);
        $session = $this->getSession();
        $page = $session->getPage();
        $notification = $page->find('xpath', $session->getSelectorsHandler()->selectorToXpath('xpath', "//sg-simple-notifications//div[@class='title']"));

        Assertion::true($notification->isVisible(), 'Notification is not visile.');
        Assertion::true($notification->getText() == $arg1, 'Notification message is not as expected.');
    }

    /**
     * @When I click :arg1 for :arg2 property
     */
    public function iClickForProperty($arg1, $arg2) {
        $this->getSession()->wait(1000);
        $session = $this->getSession();
        $page = $session->getPage();

        $defaultSlug = $page->find('xpath', $session->getSelectorsHandler()->selectorToXpath
                                ('xpath', "//table//tr[td//text()[contains(., '" . $arg2 . "')]]/td[2]/sg-grid-item"))->getText();

        switch ($arg1) {
            case 'edit':
                $actionIcon = $page->find('xpath', $session->getSelectorsHandler()->selectorToXpath
                                ('xpath', "//table//tr[td//text()[contains(., '" . $arg2 . "')]]//span[contains(@class, 'glyphicon-edit')]"));
                $actionIcon->click();
                $this->verifyStaticPropertyEdit($defaultSlug);
                break;
            case 'delete':
                $actionIcon = $page->find('xpath', $session->getSelectorsHandler()->selectorToXpath
                                ('xpath', "//table//tr[td//text()[contains(., '" . $arg2 . "')]]//span[contains(@class, 'glyphicon-trash')]"));
                $actionIcon->click();
                break;
        }
    }

    public function verifyStaticPropertyEdit($defaultSlug) {
        $session = $this->getSession();
        $session->wait(1000);
        $page = $session->getPage();

        $actualSlug = $session->evaluateScript('document.getElementById("general_slugInput").value;');

        Assertion::true($defaultSlug === $actualSlug, 'Correct edit page of static property was not opened.');
    }

    /**
     * @When in confirmation window I click :arg1
     */
    public function inConfirmationWindowIClick($arg1) {
        $session = $this->getSession();
        $session->wait(1000);
        $page = $session->getPage();

        $actionButton = $page->find('xpath', $session->getSelectorsHandler()->selectorToXpath
                        ('xpath', "//sg-modal-confirm//div[@class='modal-footer']/button[text()='" . $arg1 . "']"));

        $actionButton->click();
    }

    /**
     * @Then I should :arg1 :arg2 in the list
     */
    public function iShouldInTheList($arg1, $arg2) {
        $this->getSession()->wait(1000);
        $session = $this->getSession();
        $page = $session->getPage();

        switch ($arg1) {
            case 'see':
                $staticProperty = $page->find('xpath', $session->getSelectorsHandler()->selectorToXpath
                                ('xpath', "//table//tr[td//text()[contains(., '" . $arg2 . "')]]"));
                Assertion::true($staticProperty !== null);
                break;
            case 'not see':
                $staticProperty = $page->find('xpath', $session->getSelectorsHandler()->selectorToXpath
                                ('xpath', "//table//tr[td//text()[contains(., '" . $arg2 . "')]]"));
                Assertion::true($staticProperty === null);
                break;
        }
    }
}
