<?php
namespace Domi202\Mailcatcher\Parser;

class MboxParser
{
    /**
     * @var string
     */
    protected $fileName = '';

    /**
     * @var string
     */
    protected $filePath = 'jobs.txt';

    /**
     * @var bool|null|resource
     */
    protected $file = null;

    /**
     * @var array
     */
    protected $mails = [];

    /**
     * @param string $filePath
     */
    public function __construct(string $filePath)
    {
        if (!file_exists($filePath)) {
            throw new \RuntimeException('File ' . $filePath . ' not found');
        }
        $this->filePath = $filePath;
        $this->file = fopen($this->filePath, 'rt');
    }

    /**
     * return void
     */
    public function processFile()
    {
        $mailNumber = 0;
        $nextIsMessage = false;
        $startCopy = false;
        $message = '';

        $boundary = null;
        $boundaryCopy = false;

        do {
            $fileLine = fgets($this->file);

            if (strpos($fileLine, 'From ') !== false) {
                $mail = new \stdClass();
                $this->mails[$mailNumber++] = $mail;

                $message = '';
                $nextIsMessage = false;
                $startCopy = false;
                $boundary = null;
            }

            if (strpos($fileLine, 'Date: ') !== false) {
                $dateString = substr($fileLine, 6);
                $dateObject = new \DateTime($dateString);
                $dateObject->setTimezone(new \DateTimeZone('Europe/Berlin'));
                $mail->date = $dateObject;
            }
            if (strpos($fileLine, 'From: ') !== false) {
                $mail->from = substr($fileLine, 6);
            }
            if (strpos($fileLine, 'To: ') !== false && strpos($fileLine, 'To: ') == 0) {
                $mail->to = substr($fileLine, 4);
            }
            if (strpos($fileLine, 'Subject: ') !== false) {
                $mail->subject = substr($fileLine, 9);
                $nextIsMessage = true;
            }

            if ($nextIsMessage
                && ($fileLine == "\n" || $fileLine == "\r" || $fileLine == "\r\n")
            ) {
                $startCopy = true;
            }
            if (strpos($fileLine, 'boundary=') !== false) {
                $boundary = $this->getBoundary($fileLine);
            }

            if ($startCopy) {
                if (isset($boundary)) {
                    if (strpos($fileLine, 'Content-Disposition: inline') !== false
                        || strpos($fileLine, 'Content-Transfer-Encoding: quoted-printable') !== false
                    ) {
                        $boundaryCopy = true;
                        continue;
                    } elseif (strpos($fileLine, $boundary . '--') !== false) {
                        $boundaryCopy = false;
                    } elseif (strpos($fileLine, 'Content-Disposition: attachment') !== false) {
                        $boundaryCopy = false;
                    } elseif (strpos($fileLine, '--' . $boundary) !== false) {
                        $boundaryCopy = false;
                    }
                    if ($boundaryCopy) {
                        $message .= $fileLine;
                    }
                } else {
                    $message .= $fileLine;
                }
            }

            if (trim($message) != '') {
                $mail->message = quoted_printable_decode($message);
            }
        } while (!feof($this->file));
    }

    /**
     * @return array
     */
    public function getMails()
    {
        return $this->mails;
    }

    /**
     * @param string $line
     * @return bool|string
     */
    protected function getIP($line)
    {
        $begin = strpos($line, '[');
        $end = strpos($line, ']');
        $length = $end - $begin - 1;

        if ($length > 0) {
            return substr($line, $begin + 1, $length);
        } else {
            return '';
        }
    }

    /**
     * @param string $fileLine
     * @return bool|null|string
     */
    protected function getBoundary($fileLine)
    {
        $begin = strpos($fileLine, '"');
        $end = strpos($fileLine, '"', $begin + 1);

        $length = $end - $begin - 1;

        if ($length > 0) {
            return substr($fileLine, $begin + 1, $length);
        } else {
            return null;
        }
    }
}
