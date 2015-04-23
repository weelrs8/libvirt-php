<?php

namespace LibvirtPHP\App;

use LibvirtPHP\Auth\InterfaceAuth;
use LibvirtPHP\Manager\Domain;

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

    /**
     * Get Domains
     *
     * @return array
     */
    public function getDomains()
    {
        return libvirt_list_domains($this->resource);
    }

    /**
     * Get Version
     *
     * @return array
     */
    public function getVersion()
    {
        return libvirt_version();
    }

    public function newDomain($id, $vcpu, $memory, array $disk, $network, array $path, array $os)
    {
        $id = sprintf("vm-%u", $id);
        $vcpu = (int) $vcpu;
        $memory = (int) (1024 * (int) $memory);
        $disk['size'] = (int) $disk['size'];

        $cmd =
            sprintf(
                "
                    nohup virt-install
                    -n %s -r %u --vcpus=%u --disk path=%s,size=%d
                    --network %s --location=%s --extra-args \"ks=%s\"
                    --graphics vnc,listen=0.0.0.0 --noautoconsole
                    --os-type=%s --os-variant=%s
                    > %s &
                ",
                $id,
                $memory,
                $vcpu,
                $disk['path'],
                $disk['size'],
                $network,
                $path['path'],
                $path['ks'],
                $os['type'],
                $os['variant'],
                "/logs/{$id}.log"
            )
        ;
        /*
         * virt-install -n vm-02 -r 1024 --vcpus=1 \
         * --disk path=/datastore/instances/vm-02.qcow2,size=20 \
         * --network bridge=virbr0 \
         * --location=http://172.17.10.110:8001/centos/6/os/x86_64/ \
         * --extra-args "ks=http://172.17.10.110:8001/centos/6/centos.ks" \
         * --graphics vnc,listen=0.0.0.0 --noautoconsole \
         * --os-type=linux --os-variant=rhel6
         */
    }
}