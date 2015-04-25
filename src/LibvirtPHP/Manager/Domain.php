<?php

namespace LibvirtPHP\Manager;

class Domain
{
    private $domain;

    /**
     * Constructor
     *
     * @param $domain
     */
    public function __construct($domain)
    {
        $this->domain = $domain;
    }

    /**
     * Start
     *
     * @return bool
     */
    public function powerOn()
    {
        return (bool) libvirt_domain_create($this->domain);
    }

    /**
     * Shutdown
     *
     * @return bool
     */
    public function powerOff()
    {
        return (bool) libvirt_domain_shutdown($this->domain);
    }

    /**
     * Reboot
     * @return bool
     */
    public function reboot()
    {
        return (bool) libvirt_domain_reboot($this->domain);
    }

    /**
     * Forced Shutdown
     *
     * @return bool
     */
    public function forceOff()
    {
        return (bool) libvirt_domain_destroy($this->domain);
    }

    /**
     * Get Info
     *
     * @return array
     */
    public function getInfo()
    {
        return libvirt_domain_get_info($this->domain);
    }
}