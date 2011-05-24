<?php

/* Foursquare Community Site
 * 
 * Copyright (C) 2011 Foursquare Church.
 * 
 * Developers: Jesse Morgan <jmorgan@foursquarestaff.com>
 *
 */

class Email {
    private $subject;
    private $to;
    private $from;
    private $fromname;
    private $message;
    private $headers;

    public function __construct($to) {
        $this->to = $to;
        $this->from = $GLOBALS['CONFIG']['email_from'];
        $this->message = "";
        $this->headers = array();
    }

    public function setFrom($from) {
        $this->from = $from;

        if (strstr($from, "<"))
            $this->fromname = preg_replace("/([^<>]+) <([^<>]+)>/", "$1", $from);
    }

    public function setSubject($subject) {
        $this->subject = $subject;
    }

    public function addHeader($header, $value) {
        $this->headers[] = "$header: $value";
    }

    public function appendMessage($message) {
        $this->message .= $message;
    }

    public function send($logprefix="") {
        // Headers
        if ($this->fromname) {
            $headers  = "From: ". $this->fromname ." <". $this->from .">\n";
        } else {
            $headers  = "From: ". $this->from ."\n";
        }
        $headers .= "Reply-To: ". $this->from ."\n";
        $headers .= "Date: ". date("r") ."\n";
        $headers .= join("\n", $this->headers);

        if ($GLOBALS['CONFIG']['production']) {
            $ret = mail($this->to, $this->subject, $this->message, $headers);
        
        } else {
            // If we're not in production, save to file instead of emailing.
            $fh = fopen($GLOBALS['CONFIG']['root'].'/emails.log', 'a');
            fwrite($fh, sprintf("To: %s\n%s\nSubject: %s\n\n%s\n\n",
                $this->to, $headers, $this->subject, $this->message));
            fclose($fh);
        }

        // TODO: Add logger
        //$GLOBALS['logger']->log_email($ret, $this->to, $this->subject, $logprefix);
    }
}

?>
