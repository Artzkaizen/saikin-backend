<?php

namespace App\Whatsapp;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverKeys;
use Facebook\WebDriver\WebDriverWait;
use Illuminate\Support\Facades\Storage;

/**
 * Whatsapp Login
 * Class path 'App\Whatsapp\WhatsAppLogin'
 * Test call (new App\Whatsapp\WhatsAppLogin())->LoginWithQRCode()->sendMessageToSpecificContact();
 */
class WhatsAppLogin
{
    /**
     * The server url where browser will be run
     * 
     * @var string $server_url
     */
    protected string $server_url;

    /**
     * The path where browser screen shots will be saved
     * 
     * @var string $screenshot_directory
     */
    protected string $screenshot_directory;

    /**
     * The instance of Facebook\WebDriver\Remote\RemoteWebDriver
     * 
     * @var Facebook\WebDriver\Remote\RemoteWebDriver $driver
     */
    protected $driver;

    /**
     * Create a new WhatsAppLogin instance.
     * @param void
     * @return void
     */
    public function __construct(string $server_url = 'http://localhost:9515') {

        $this->server_url = $server_url;
        $this->screenshot_directory = storage_path('app/public/images/screenshots');
        if (!is_dir($this->screenshot_directory)) { mkdir($this->screenshot_directory, 0777, true); }
    }

    /**
     * Login to whatsapp with QR code
     * @param string $unique_identifier
     * @return void
     */
    public function LoginWithQRCode(string $unique_identifier = "01b61787-d976-4136-8ca4-1678af5664b2")
    {
        // Chrome
        $driver = RemoteWebDriver::create($this->server_url, DesiredCapabilities::chrome());

        // Go to URL
        $driver->get('https://web.whatsapp.com/');

        // Wait until QR code is visible
        $driver->wait()->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::cssSelector('[data-testid="qrcode"]'))
        );

        // Find QR code
        $driver->findElement(WebDriverBy::cssSelector('[data-testid="qrcode"]'));

        // Continually snapshot
        do {

            // Take a screenshot of the entire page
            $screenshot = $driver->takeScreenshot();

            // Create a new image resource from the screenshot
            $image = imagecreatefromstring($screenshot);

            // Crop the image to the element's location and size
            $croppedImage = imagecrop($image, ['x' => 1510, 'y' => 280, 'width' => 600, 'height' => 600]);

            // Generate a unique filename
            $filename = $unique_identifier.'.png';

            // Save the cropped image to a file
            imagepng($croppedImage, $this->screenshot_directory.'/'.$filename);

            // Wait for 9 second before continuing
            sleep(9);

        } while ( !empty($driver->findElements(WebDriverBy::cssSelector('[data-testid="qrcode"]'))) );

        // Store driver instance
        $this->driver = $driver;

        // Return class instance
        return $this;
    }

    /**
     * Send a message to a specific contact
     * @param string $contact
     * @return void
     */
    public function sendMessageToSpecificContact(string $contact = "tom")
    {
        // Retrieve driver instance
        $driver = $this->driver;

        // Wait until new chat is visible
        $driver->wait()->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::cssSelector('[data-testid="chat"]'))
        );

        // Find and click on new chat
        $driver->findElement(WebDriverBy::cssSelector('[data-testid="chat"]'))->click();

        // Wait until new chat is visible
        $driver->wait()->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::cssSelector('[data-testid="contact-list-key"]'))
        );

        // Find user contact list
        $contact_list = $driver->findElement(WebDriverBy::cssSelector('[data-testid="contact-list-key"]'));

        // Click on the scrollable element to ensure it has focus
        $contact_list->click();

        // Simulate pressing the "End" key to scroll to the bottom
        $driver->getKeyboard()->sendKeys(WebDriverKeys::END);

        // Wait for 9 secs for the scroll position reaches the bottom
        $driver->wait(9);

        // Find all user contacts
        // $contacts = $contact_list->findElements(WebDriverBy::cssSelector('[data-testid^="list-item-"]'));
        $contacts = $driver->executeScript('return document.querySelectorAll("[data-testid^=\'list-item-\']");');

        // Iterate over the contacts and do something
        foreach ($contacts as $contact) {

            try {

                $contactElement = $driver->getWrappedElement($contact);

                // Find the specific element containing the desired text
                $cellFrameTitle = $contactElement->findElement(WebDriverBy::cssSelector('[data-testid="cell-frame-title"]'));

                // Get the text from the element
                $text = $cellFrameTitle->getText();

                // Output the text
                echo $text . "\n";

            } catch (\Throwable $th) {

                echo 'error' .$th->getMessage(). "\n";

            }
        }
    }
}

