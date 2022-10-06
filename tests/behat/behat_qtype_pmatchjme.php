<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.
use Facebook\WebDriver\WebDriverBy;

require_once(__DIR__ . '/../../../../../lib/behat/behat_base.php');

/**
 * Steps definitions for the pattern match with JME question type.
 *
 * @copyright 2022 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_qtype_pmatchjme extends behat_base {

    /**
     * Click at the co-ordinates of the given element, not worrying if it is covered by something else.
     *
     * JME seems to cover everything in an invisible div, which stops the normal
     * $this->execute("behat_general::i_click_on", [this->escape($xpath), "xpath_element"]);
     * from working. Therefore, we have to go low-level.
     *
     * @param string $xpath identifies the node to click over.
     */
    protected function click_at_point($xpath) {
        $driver = $this->getSession()->getDriver();
        if (!$driver instanceof \OAndreyev\Mink\Driver\WebDriver) {
            // This is a feature of the OAndreyev MinkWebDriver.
            throw new \Behat\Mink\Exception\DriverException('Only compatible with OAndreyev MinkWebDriver');
        }

        $webdriver = $driver->getWebDriver();
        if (!$webdriver instanceof \Facebook\WebDriver\Remote\RemoteWebDriver) {
            throw new \Behat\Mink\Exception\DriverException('Only compatible with php-webdriver');
        }

        $webdriver->action()->click($webdriver->findElement(WebDriverBy::xpath($xpath)))->perform();
    }

    /**
     * Click on the Benzene tool in the only JME editor on the page.
     *
     * @Given I click on the benzene tool in the pattern match with JME question
     */
    public function i_click_on_benzene_tool(): void {
        $xpath = "//div[@class = 'ablock']/div/div/div[@class = 'jsa-resetDiv']/div[@class = 'jsa-resetDiv']/" .
                "div[@class = 'jsa-resetDiv'][3]/*[name() = 'svg']/*[name() = 'g']/" .
                "*[name() = 'rect' and @x = '5790' and @y = '750' and @width = '660']";
        $this->click_at_point($xpath);
    }

    /**
     * Click on the drawing area tool in the only JME editor on the page.
     *
     * @Given I click on the drawing area in the pattern match with JME question
     */
    public function i_click_on_drawing_area(): void {
        $xpath = "//div[@class = 'ablock']/div/div/div[@class = 'jsa-resetDiv']/" .
                "div[@class = 'jsa-resetDiv']/div[@class = 'jsa-resetDiv'][2]";
        $this->click_at_point($xpath);
    }
}
