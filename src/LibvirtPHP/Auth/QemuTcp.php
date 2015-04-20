<?php

namespace LibvirtPHP\Auth;

class QemuTcp implements InterfaceAuth
{
    private $hostname;
    private $username;
    private $password;
    private $readonly;

    public function __construct($hostname, array $credentials, $readonly = false)
    {
        $this->hostname = $hostname;
        $this->username = (isset($credentials['username']) ? $credentials['username'] : '');
        $this->password = (isset($credentials['password']) ? $credentials['password'] : '');
        $this->readonly = $readonly;
    }

    public function authenticate()
    {
        $resource =
            libvirt_connect(
                "qemu+tcp://{$this->hostname}/system",
                $this->readonly,
                [
                    VIR_CRED_AUTHNAME   => $this->username,
                    VIR_CRED_PASSPHRASE => $this->password
                ]
            )
        ;

        if (!is_resource($resource))
            throw new \Exception("Falha na authenticação: " . libvirt_get_last_error());

        return $resource;
    }
}