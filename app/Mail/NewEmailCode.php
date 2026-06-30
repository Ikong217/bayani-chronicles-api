<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewEmailCode extends Mailable
{
    use Queueable, SerializesModels;

    public $imageUrl;

    /**
     * Create a new message instance.
     *
     * @param  string  $randomCode
     * @return void
     */
    public function __construct(public string $randomCode)
    {
        // You could generate and store the CAPTCHA image here
        $this->imageUrl = $this->generateCaptchaImage($randomCode);
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Verify Your New Email')
            ->view('Emails.new_email_code')
            ->with([
                'imageUrl' => $this->imageUrl,
            ]);
    }

    /**
     * Generate a distorted CAPTCHA-like image from code.
     */
    private function generateCaptchaImage(string $text)
    {
        $dir = base_path('../assets/images/captcha');
        if (! file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        $path = $dir . "/{$text}.png";

        $img       = imagecreatetruecolor(200, 70);
        $bg        = imagecolorallocate($img, rand(200, 255), rand(200, 255), rand(200, 255));
        $textColor = imagecolorallocate($img, rand(0, 100), rand(0, 100), rand(0, 100));

        imagefilledrectangle($img, 0, 0, 200, 70, $bg);

                       // Use built-in GD font
        $fontSize = 5; // 1-5 built-in sizes
        $x        = 20;
        $y        = 20;
        for ($i = 0; $i < strlen($text); $i++) {
            imagestring($img, $fontSize, $x, $y + rand(-5, 5), $text[$i], $textColor);
            $x += 30; // spacing
        }

        // Add noise
        for ($i = 0; $i < 5; $i++) {
            $noiseColor = imagecolorallocate($img, rand(150, 200), rand(150, 200), rand(150, 200));
            imageline($img, rand(0, 200), rand(0, 70), rand(0, 200), rand(0, 70), $noiseColor);
        }

        imagepng($img, $path);
        imagedestroy($img);

        return asset("assets/images/captcha/{$text}.png");
    }
}
