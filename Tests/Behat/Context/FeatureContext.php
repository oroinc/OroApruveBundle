<?php

namespace Oro\Bundle\ApruveBundle\Tests\Behat\Context;

use Behat\Mink\Exception\ElementNotFoundException;
use Oro\Bundle\TestFrameworkBundle\Behat\Context\OroFeatureContext;
use Oro\Bundle\TestFrameworkBundle\Behat\Element\OroPageObjectAware;
use Oro\Bundle\TestFrameworkBundle\Tests\Behat\Context\PageObjectDictionary;

class FeatureContext extends OroFeatureContext implements OroPageObjectAware
{
    use PageObjectDictionary;

    /**
     * @param string $button
     * @param string $embeddedForm
     *
     * @throws ElementNotFoundException
     *
     * @Given /^(?:|I )press "(?P<button>[\w\s]*)" in "(?P<embeddedForm>[\w\s]*)"$/
     */
    public function iClickButtonInEmbed(string $button, string $embeddedForm)
    {
        $embeddedFormId = $this->elementFactory->createElement($embeddedForm)->getOption('embedded-id');

        if (!$embeddedFormId) {
            throw new \RuntimeException(sprintf('Element "%s" has not embedded-id option', $embeddedForm));
        }

        $this->getDriver()->switchToIFrame($embeddedFormId);
        $page = $this->getSession()->getPage();
        $button = $this->fixStepArgument($button);

        try {
            $page->pressButton($button);
        } catch (ElementNotFoundException $e) {
            if ($page->hasLink($button)) {
                $page->clickLink($button);
            } elseif ($this->elementFactory->hasElement($button)) {
                $this->elementFactory->createElement($button)->click();
            } else {
                throw $e;
            }
        }
        $this->getDriver()->switchToWindow();
    }
}
