<?php

namespace LibvirtPHP\Auth;

interface InterfaceAuth
{
    /**
     * Constructor
     *
     * @param $hostname
     * @param array $credentials
     * @param bool $readonly
     */
    public function __construct($hostname, array $credentials, $readonly = false);

    /**
     * Authenticate
     *
     * @return resource
     */
    public function authenticate();
}