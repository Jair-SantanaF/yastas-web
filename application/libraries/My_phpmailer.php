<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require('PHPMailer/class.phpmailer.php');
class My_PHPMailer extends PHPMailer {
    public function __construct()
    {
        parent::__construct();
    }
}
?>