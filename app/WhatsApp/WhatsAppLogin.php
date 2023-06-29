<?php

namespace App\Whatsapp;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverBy;
use Illuminate\Support\Facades\Storage;

class WhatsAppLogin
{
    /**
     * The server url where browser will be run
     * 
     * @var string $server_url
     */
    protected string $server_url;

    /**
     * Create a new WhatsAppLogin instance.
     * @param void
     * @return void
     */
    public function __construct(string $server_url = 'http://localhost:9515') {

        $class_path = 'App\\\Whatsapp\\\WhatsAppLogin';
        $this->server_url = $server_url;
    }

    public function LoginWithQRCode()
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
        $element = $driver->findElement(WebDriverBy::cssSelector('[data-testid="qrcode"]'));

        // Take a screenshot of the entire page
        $screenshot = $driver->takeScreenshot();

        // Create a new image resource from the screenshot
        $image = imagecreatefromstring($screenshot);

        // Crop the image to the element's location and size
        $croppedImage = imagecrop($image, ['x' => 1510, 'y' => 280, 'width' => 600, 'height' => 600]);

        // Generate a unique filename
        $filename = 'screenshot_'.uniqid().'.png';

        // Save the cropped image to a file
        imagepng($croppedImage, public_path('assets/images/screenshots/'.$filename));

        // Save the cropped image to the storage disk using Laravel's Storage facade
		$storage = Storage::put('public/screenshots/'.$filename, fopen(public_path('assets/images/screenshots/'.$filename),'r'));

        // Delete the cropped image file
        @unlink(public_path('assets/images/screenshots/'.$filename));

        // Quit
        $driver->quit();
    }
}

