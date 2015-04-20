<?php

namespace LibvirtPHP\App;

use LibvirtPHP\Auth\InterfaceAuth;

class Libvirt
{
    private $resource;

    /**
     * Connect
     *
     * @param InterfaceAuth $auth
     */
    public function connect(InterfaceAuth $auth)
    {
        $this->resource = $auth->authenticate();
    }

    /**
     * Is Connected
     *
     * @return bool
     */
    public function isConnected()
    {
        return (is_resource($this->resource) ? true : false);
    }

    /**
     * Domain Manager
     *
     * @param $domain
     * @return Domain
     * @throws \Exception
     */
    public function getDomainManager($domain)
    {
        if (!is_resource($domain))
            $domain = $this->getDomainResource($domain);

        return new Domain($domain);
    }

    /**
     * Domain Resource
     *
     * @param $domain
     * @return resource
     * @throws \Exception
     */
    public function getDomainResource($domain)
    {
        $domain = libvirt_domain_lookup_by_name($this->resource, $domain);
        if(is_resource($domain))
            return $domain;

        $domain = libvirt_domain_lookup_by_uuid_string($this->resource, $domain);
        if(is_resource($domain))
            return $domain;

        throw new \Exception("Dominio informado não existe: " . libvirt_get_last_error());
    }
}